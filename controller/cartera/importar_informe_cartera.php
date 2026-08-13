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

// ── Log ──
$logDir = __DIR__ . '/../../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/import_informe_cartera.log';
function logInforme($msg) {
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}
logInforme('========== INICIO IMPORTACION INFORME ==========');
logInforme('Usuario: ' . ($_SESSION['username'] ?? 'desconocido'));
logInforme('Archivo: ' . ($_FILES['archivo_informe']['name'] ?? 'N/A'));

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

    // ── Primer barrido: recolectar filas de datos y combos (cedula/contrato, contrato/mes/anio) ──
    $filasDatos     = [];
    $paresPropiedad = [];   // "cedula|contrato" => ['cedula'=>..., 'contrato'=>...]
    $combosBorrar   = [];   // "contrato|mes|anio" => true

    for ($fila = 1; $fila <= $filas; $fila++) {
        if (!esFilaDatos($hoja, $fila)) {
            continue;
        }

        $nit            = trim((string) $hoja->getCell("B{$fila}")->getValue());
        $nombreTercero  = trim((string) $hoja->getCell("C{$fila}")->getValue());

        // Fecha
        $fechaRaw = $hoja->getCell("D{$fila}")->getValue();
        if (is_numeric($fechaRaw) && $fechaRaw > 1000) {
            $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRaw)->format('Y-m-d');
        } else {
            $cd = trim((string) $fechaRaw);
            $fp = date_parse(str_replace('/', '-', $cd));
            $fecha = ($fp['year'] && $fp['month'] && $fp['day'])
                ? sprintf('%04d-%02d-%02d', $fp['year'], $fp['month'], $fp['day'])
                : date('Y-m-d');
        }

        // Mes
        $mesRaw = trim((string) $hoja->getCell("E{$fila}")->getValue());
        $mes = '';
        foreach ($mesesMap as $abr => $num) {
            if (stripos($mesRaw, $abr) !== false) { $mes = $num; break; }
        }
        if (empty($mes)) {
            if (preg_match('/(\d{2})/', $mesRaw, $m)) { $mes = $m[1]; }
            else { $mes = date('m'); }
        }

        $detalle  = trim((string) $hoja->getCell("F{$fila}")->getValue());
        $concepto = inferirConcepto($detalle);
        $debito   = limpiarMonedaInforme($hoja->getCell("G{$fila}")->getValue());
        $credito  = limpiarMonedaInforme($hoja->getCell("H{$fila}")->getValue());
        $codigoInmueble = (int) trim((string) $hoja->getCell("J{$fila}")->getValue());
        $anio = (int) trim((string) $hoja->getCell("K{$fila}")->getValue());
        if ($anio <= 0) { $anio = (int) date('Y'); }

        $filasDatos[] = [
            'nit' => $nit, 'nombre' => $nombreTercero, 'fecha' => $fecha, 'mes' => $mes,
            'concepto' => $concepto, 'detalle' => $detalle, 'debito' => $debito, 'credito' => $credito,
            'codigo' => $codigoInmueble, 'anio' => $anio,
        ];
        $paresPropiedad["{$nit}|{$codigoInmueble}"] = ['cedula' => $nit, 'contrato' => $codigoInmueble];
        $combosBorrar["{$nit}|{$codigoInmueble}|{$mes}|{$anio}"] = true;
    }

    if (empty($filasDatos)) {
        logInforme('ERROR: No se encontraron filas de datos validas');
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No se encontraron datos validos en el archivo. Revise el formato.']);
        exit;
    }

    // ── Verificar en BD cada par (cedula, contrato) ──
    $sqlVerifica = "SELECT propietario, direccion FROM contratos_somos_propiedad
                    WHERE cedula_propietario = ? AND no_contrato = ? LIMIT 1";
    $stmtVerifica = $conn->prepare($sqlVerifica);

    $propiedadesValidas = []; // "cedula|contrato" => ['propietario'=>..., 'direccion'=>...]
    $paresInvalidos     = [];
    foreach ($paresPropiedad as $key => $p) {
        $stmtVerifica->bind_param('si', $p['cedula'], $p['contrato']);
        $stmtVerifica->execute();
        $resV = $stmtVerifica->get_result();
        $dato = $resV->fetch_assoc();
        $resV->free();
        if ($dato) {
            $propiedadesValidas[$key] = ['propietario' => $dato['propietario'], 'direccion' => $dato['direccion']];
        } else {
            $paresInvalidos[$key] = $p;
        }
    }
    $stmtVerifica->close();

    if (empty($propiedadesValidas)) {
        logInforme('ERROR: Ninguna propiedad del archivo existe en la BD');
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Ninguna de las propiedades del archivo existe en la base de datos.']);
        exit;
    }

    // ── Borrar movimientos previos por cada combo (nit, contrato, mes, año) ──
    $sqlDelete = "DELETE FROM cartera_propietario WHERE nit_propietario = ? AND codigo_inmueble = ? AND mes = ? AND anio = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    if (!$stmtDelete) {
        throw new RuntimeException('Error preparando borrado: ' . $conn->error);
    }
    $totalBorrados = 0;
    foreach ($combosBorrar as $key => $_) {
        [$nitD, $codigo, $mesD, $anioD] = explode('|', $key);
        $stmtDelete->bind_param('sisi', $nitD, $codigo, $mesD, $anioD);
        $stmtDelete->execute();
        $totalBorrados += $stmtDelete->affected_rows;
    }
    $stmtDelete->close();
    logInforme("Borrados previos totales: {$totalBorrados}");

    // ── Insertar registros (solo propiedades validas) ──
    $insertados = 0;
    $omitidas   = 0;
    $errores    = [];
    $porPropiedad = []; // "cedula|contrato" => ['insertados'=>0]

    $sqlInsert = "INSERT INTO cartera_propietario
        (nit_propietario, nombre_tercero, fecha, mes, concepto, detalle, debito, credito, codigo_inmueble, anio, es_giro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    if (!$stmtInsert) {
        throw new RuntimeException('Error preparando sentencia: ' . $conn->error);
    }

    foreach ($filasDatos as $f) {
        $key = "{$f['nit']}|{$f['codigo']}";
        if (!isset($propiedadesValidas[$key])) {
            $omitidas++;
            continue;
        }
        $esGiro = ($f['concepto'] === 'Giro Renta') ? 1 : 0;
        $stmtInsert->bind_param(
            'ssssssddiis',
            $f['nit'], $f['nombre'], $f['fecha'], $f['mes'], $f['concepto'], $f['detalle'],
            $f['debito'], $f['credito'], $f['codigo'], $f['anio'], $esGiro
        );
        if ($stmtInsert->execute()) {
            $insertados++;
            $porPropiedad[$key]['insertados'] = ($porPropiedad[$key]['insertados'] ?? 0) + 1;
        } else {
            $errores[] = "Contrato #{$f['codigo']} ({$f['detalle']}): " . $stmtInsert->error;
        }
    }
    $stmtInsert->close();

    // ── Construir resumen de propiedades ──
    $propiedadesResumen = [];
    foreach ($propiedadesValidas as $key => $info) {
        [$ced, $ctr] = explode('|', $key);
        $propiedadesResumen[] = [
            'cedula'      => $ced,
            'contrato'    => $ctr,
            'propietario' => $info['propietario'],
            'direccion'   => $info['direccion'],
            'insertados'  => $porPropiedad[$key]['insertados'] ?? 0,
        ];
    }

    // Avisar de propiedades invalidas
    foreach ($paresInvalidos as $key => $p) {
        $errores[] = "CC{$p['cedula']} / Contrato #{$p['contrato']} no existen en la BD, omitidos.";
        logInforme("WARN: CC{$p['cedula']} / Contrato #{$p['contrato']} no existen en BD");
    }

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);

    $totalTime = round(microtime(true) - $startTime, 2);

    logInforme("RESULTADO: Insertados={$insertados} Borrados={$totalBorrados} Omitidas={$omitidas} Propiedades=" . count($propiedadesValidas) . " Errores=" . count($errores) . " Tiempo={$totalTime}s");
    logInforme('========== FIN IMPORTACION INFORME ==========' . PHP_EOL);

    ob_end_clean();
    echo json_encode([
        'success'         => true,
        'insertados'      => $insertados,
        'borradosPrevios' => $totalBorrados,
        'omitidas'        => $omitidas,
        'errores'         => $errores,
        'tiempo'          => $totalTime,
        'numPropiedades'  => count($propiedadesValidas),
        'propiedades'     => $propiedadesResumen,
    ]);

} catch (Throwable $e) {
    logInforme('EXCEPCION: ' . $e->getMessage());
    logInforme('========== FIN IMPORTACION INFORME CON ERROR ==========' . PHP_EOL);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
