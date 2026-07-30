<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

ob_start();

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'No autenticado.']);
    exit;
}
if (empty($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Sin permisos para esta accion.']);
    exit;
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_informe'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Solicitud invalida.']);
    exit;
}

$archivo = $_FILES['archivo_informe'];
$extensionesPermitidas = ['xls', 'xlsx', 'ods'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if ($archivo['error'] !== UPLOAD_ERR_OK || !in_array($ext, $extensionesPermitidas)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Archivo invalido. Solo se permiten .xls, .xlsx u .ods.']);
    exit;
}

// ── Mapeo de meses ──
$mesesMap = [
    'ene' => '01', 'feb' => '02', 'mar' => '03', 'abr' => '04',
    'may' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
    'sep' => '09', 'oct' => '10', 'nov' => '11', 'dic' => '12',
];

// ── Mapeo de detalle → concepto ──
function inferirConcepto(string $detalle): string
{
    $d = trim($detalle);
    $dLower = strtolower($d);

    if (preg_match('/^pag[a-z]*pp\d+|^giro\s*renta/i', $dLower)) {
        return 'Giro Renta';
    }
    if (strpos($dLower, 'canon de') === 0 || strpos($dLower, 'canon de  desde') === 0) {
        return 'Canon de arrendamiento';
    }
    if (strpos($dLower, 'comi/canon') === 0 || strpos($dLower, 'comi/canon desde') === 0) {
        return 'Comisión/Canon';
    }
    if (strpos($dLower, 'iva/comision') === 0 || strpos($dLower, 'iva/comision desde') === 0) {
        return 'Iva/Comisión';
    }
    if (strpos($dLower, 'iva arrendamiento') !== false || strpos($dLower, 'iva/arrendamiento') !== false) {
        return 'IVA ARRENDAMIENTO';
    }
    if (strpos($dLower, 'pago admon') === 0 || strpos($dLower, 'pago admo') === 0) {
        return 'Pago Administración';
    }
    if (strpos($dLower, 'reintegro') !== false || strpos($dLower, 'reintegros') !== false) {
        return 'Reintegro Administración';
    }
    if (strpos($dLower, 'retroactivo') !== false) {
        return 'Retroactivo Administración';
    }
    return mb_strlen($d) > 80 ? mb_substr($d, 0, 77) . '...' : $d;
}

// ── Limpiar valores monetarios ──
function limpiarMonedaInforme($valor): float
{
    if (is_numeric($valor)) {
        return abs((float) $valor);
    }
    $limpio = preg_replace('/[^0-9,.\-]/', '', trim((string) $valor));
    $ultimaComa  = strrpos($limpio, ',');
    $ultimoPunto = strrpos($limpio, '.');
    if ($ultimaComa !== false && ($ultimoPunto === false || $ultimaComa > $ultimoPunto)) {
        $limpio = str_replace('.', '', $limpio);
        $limpio = str_replace(',', '.', $limpio);
    } else {
        $limpio = str_replace(',', '', $limpio);
    }
    $limpio = str_replace('-', '', $limpio);
    return (float) $limpio;
}

// ── Saber si una fila es un registro de datos válido ──
function esFilaDatos($hoja, int $fila): bool
{
    $celdaB = trim((string) $hoja->getCell("B{$fila}")->getValue());
    $celdaC = trim((string) $hoja->getCell("C{$fila}")->getValue());
    $celdaF = trim((string) $hoja->getCell("F{$fila}")->getValue());
    $celdaJ = trim((string) $hoja->getCell("J{$fila}")->getValue());

    // B debe ser numerico (cedula), C no debe ser cabecera, F no vacio, J numerico
    if (!is_numeric($celdaB) || empty($celdaB)) return false;
    if (empty($celdaF)) return false;
    if (!is_numeric($celdaJ) || empty($celdaJ)) return false;
    if (stripos($celdaC, 'Nombre Tercero') !== false) return false;
    if (stripos($celdaC, 'Cuenta') !== false) return false;

    return true;
}

try {
    $startTime = microtime(true);

    $reader      = IOFactory::createReaderForFile($archivo['tmp_name']);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($archivo['tmp_name']);
    $hoja        = $spreadsheet->getActiveSheet();
    $filas       = $hoja->getHighestDataRow();

    // ── Extraer cedula y no_contrato de la fila 3 (primer registro de datos) ──
    $cedulaArchivo     = trim((string) $hoja->getCell("B3")->getValue());
    $noContratoArchivo = (int) trim((string) $hoja->getCell("J3")->getValue());

    if (!is_numeric($cedulaArchivo) || empty($cedulaArchivo) || $noContratoArchivo <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No se pudo extraer la cedula y/o el numero de contrato del archivo. Revise que el formato del Excel sea el esperado.']);
        exit;
    }

    // ── Verificar que el propietario y el contrato existan ──
    $sqlVerifica = "SELECT propietario, cedula_propietario, direccion, ciudad
                    FROM contratos_somos_propiedad
                    WHERE cedula_propietario = ? AND no_contrato = ?
                    LIMIT 1";
    $stmtVerifica = $conn->prepare($sqlVerifica);
    $stmtVerifica->bind_param('si', $cedulaArchivo, $noContratoArchivo);
    $stmtVerifica->execute();
    $resVerifica = $stmtVerifica->get_result();
    $datosPropiedad = $resVerifica->fetch_assoc();
    $stmtVerifica->close();

    if (!$datosPropiedad) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => "No se encontro en la base de datos el propietario CC{$cedulaArchivo} con contrato #{$noContratoArchivo}. Verifique que exista en contratos_somos_propiedad."]);
        exit;
    }

    $nombrePropietario = $datosPropiedad['propietario'];

    // ── Extraer mes y año del primer registro (fila 3) ──
    $mesRaw = trim((string) $hoja->getCell("E3")->getValue());
    $mesArchivo = '';
    foreach ($mesesMap as $abr => $num) {
        if (stripos($mesRaw, $abr) !== false) {
            $mesArchivo = $num;
            break;
        }
    }
    if (empty($mesArchivo)) {
        $mesArchivo = date('m');
    }
    $anioArchivo = (int) trim((string) $hoja->getCell("K3")->getValue());
    if ($anioArchivo <= 0) {
        $anioArchivo = (int) date('Y');
    }

    // ── Borrar movimientos existentes de este (contrato, mes, año) ──
    $sqlDelete = "DELETE FROM cartera_propietario WHERE codigo_inmueble = ? AND mes = ? AND anio = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    if (!$stmtDelete) {
        throw new RuntimeException('Error preparando borrado: ' . $conn->error);
    }
    $stmtDelete->bind_param('isi', $noContratoArchivo, $mesArchivo, $anioArchivo);
    $stmtDelete->execute();
    $borradosPrevios = $stmtDelete->affected_rows;
    $stmtDelete->close();

    // ── Segundo barrido: insertar registros ──
    $insertados = 0;
    $omitidas   = 0;
    $errores    = [];

    $sqlInsert = "INSERT INTO cartera_propietario
        (nit_propietario, nombre_tercero, fecha, mes, concepto, detalle, debito, credito, codigo_inmueble, anio, es_giro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    if (!$stmtInsert) {
        throw new RuntimeException('Error preparando sentencia: ' . $conn->error);
    }

    for ($fila = 1; $fila <= $filas; $fila++) {
        if (!esFilaDatos($hoja, $fila)) {
            $omitidas++;
            continue;
        }

        $nit            = trim((string) $hoja->getCell("B{$fila}")->getValue());
        $nombreTercero  = trim((string) $hoja->getCell("C{$fila}")->getValue());

        // Fecha: puede ser serial de Excel o string d/m/Y
        $fechaRaw = $hoja->getCell("D{$fila}")->getValue();
        if (is_numeric($fechaRaw) && $fechaRaw > 1000) {
            $fechaObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRaw);
            $fecha = $fechaObj->format('Y-m-d');
        } else {
            $celdaD = trim((string) $fechaRaw);
            $fechaParse = date_parse(str_replace('/', '-', $celdaD));
            if ($fechaParse['year'] && $fechaParse['month'] && $fechaParse['day']) {
                $fecha = sprintf('%04d-%02d-%02d', $fechaParse['year'], $fechaParse['month'], $fechaParse['day']);
            } else {
                $fecha = date('Y-m-d');
            }
        }

        // Mes: "2026 - Jul" → "07"
        $mesRaw = trim((string) $hoja->getCell("E{$fila}")->getValue());
        $mes = '';
        foreach ($mesesMap as $abr => $num) {
            if (stripos($mesRaw, $abr) !== false) {
                $mes = $num;
                break;
            }
        }
        if (empty($mes)) {
            if (preg_match('/(\d{2})/', $mesRaw, $m)) {
                $mes = $m[1];
            } else {
                $mes = date('m');
            }
        }

        $detalle  = trim((string) $hoja->getCell("F{$fila}")->getValue());
        $concepto = inferirConcepto($detalle);

        $debito  = limpiarMonedaInforme($hoja->getCell("G{$fila}")->getValue());
        $credito = limpiarMonedaInforme($hoja->getCell("H{$fila}")->getValue());

        $codigoInmueble = (int) trim((string) $hoja->getCell("J{$fila}")->getValue());
        $anio = (int) trim((string) $hoja->getCell("K{$fila}")->getValue());
        if ($anio <= 0) {
            $anio = (int) date('Y');
        }

        // Saltar si el codigo de inmueble no coincide con el extraido
        if ($codigoInmueble !== $noContratoArchivo) {
            $omitidas++;
            continue;
        }

        $esGiro = ($concepto === 'Giro Renta') ? 1 : 0;

        $stmtInsert->bind_param(
            'ssssssddiis',
            $nit,
            $nombreTercero,
            $fecha,
            $mes,
            $concepto,
            $detalle,
            $debito,
            $credito,
            $codigoInmueble,
            $anio,
            $esGiro
        );

        if ($stmtInsert->execute()) {
            $insertados++;
        } else {
            $errores[] = "Fila {$fila}: " . $stmtInsert->error;
        }
    }

    $stmtInsert->close();
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);

    $totalTime = round(microtime(true) - $startTime, 2);

    ob_end_clean();
    echo json_encode([
        'success'       => true,
        'insertados'    => $insertados,
        'borradosPrevios' => $borradosPrevios,
        'omitidas'      => $omitidas,
        'errores'       => $errores,
        'tiempo'        => $totalTime,
        'cedula'        => $cedulaArchivo,
        'contrato'      => $noContratoArchivo,
        'propietario'   => $nombrePropietario,
        'direccion'     => $datosPropiedad['direccion'],
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
