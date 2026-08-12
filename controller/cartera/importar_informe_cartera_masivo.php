<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('max_execution_time', 1200);
ini_set('memory_limit', '512M');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_zip'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Solicitud invalida.']);
    exit;
}

$archivo = $_FILES['archivo_zip'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if ($archivo['error'] !== UPLOAD_ERR_OK || $ext !== 'zip') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Archivo invalido. Solo se permiten .zip.']);
    exit;
}

// ── Log ──
$logDir = __DIR__ . '/../../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFileMasivo = $logDir . '/import_informe_cartera.log';
function logInformeMasivo($msg) {
    global $logFileMasivo;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents($logFileMasivo, $line, FILE_APPEND | LOCK_EX);
}
logInformeMasivo('========== INICIO IMPORTACION MASIVA ==========');
logInformeMasivo('Usuario: ' . ($_SESSION['username'] ?? 'desconocido'));
logInformeMasivo('Archivo ZIP: ' . ($_FILES['archivo_zip']['name'] ?? 'N/A'));

// ── Mapeo de meses ──
$mesesMap = [
    'ene' => '01', 'feb' => '02', 'mar' => '03', 'abr' => '04',
    'may' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
    'sep' => '09', 'oct' => '10', 'nov' => '11', 'dic' => '12',
];

// ── Mapeo de detalle → concepto ──
function inferirConceptoMasivo(string $detalle): string
{
    $d = trim($detalle);
    $dLower = strtolower($d);
    if (preg_match('/^pag[a-z]*pp\d+|^giro\s*renta/i', $dLower)) return 'Giro Renta';
    if (strpos($dLower, 'canon de') === 0 || strpos($dLower, 'canon de  desde') === 0) return 'Canon de arrendamiento';
    if (strpos($dLower, 'comi/canon') === 0 || strpos($dLower, 'comi/canon desde') === 0) return 'Comisión/Canon';
    if (strpos($dLower, 'iva/comision') === 0 || strpos($dLower, 'iva/comision desde') === 0) return 'Iva/Comisión';
    if (strpos($dLower, 'iva arrendamiento') !== false || strpos($dLower, 'iva/arrendamiento') !== false) return 'IVA ARRENDAMIENTO';
    if (strpos($dLower, 'pago admon') === 0 || strpos($dLower, 'pago admo') === 0) return 'Pago Administración';
    if (strpos($dLower, 'reintegro') !== false || strpos($dLower, 'reintegros') !== false) return 'Reintegro Administración';
    if (strpos($dLower, 'retroactivo') !== false) return 'Retroactivo Administración';
    return mb_strlen($d) > 80 ? mb_substr($d, 0, 77) . '...' : $d;
}

function limpiarMonedaInformeMasivo($valor): float
{
    if (is_numeric($valor)) return abs((float) $valor);
    $limpio = preg_replace('/[^0-9,.\-]/', '', trim((string) $valor));
    $ultimaComa  = strrpos($limpio, ',');
    $ultimoPunto = strrpos($limpio, '.');
    if ($ultimaComa !== false && ($ultimoPunto === false || $ultimaComa > $ultimoPunto)) {
        $limpio = str_replace('.', '', $limpio);
        $limpio = str_replace(',', '.', $limpio);
    } else {
        $limpio = str_replace(',', '', $limpio);
    }
    return (float) str_replace('-', '', $limpio);
}

function esFilaDatosMasivo($hoja, int $fila): bool
{
    $celdaB = trim((string) $hoja->getCell("B{$fila}")->getValue());
    $celdaC = trim((string) $hoja->getCell("C{$fila}")->getValue());
    $celdaF = trim((string) $hoja->getCell("F{$fila}")->getValue());
    $celdaJ = trim((string) $hoja->getCell("J{$fila}")->getValue());
    if (!is_numeric($celdaB) || empty($celdaB)) return false;
    if (empty($celdaF)) return false;
    if (!is_numeric($celdaJ) || empty($celdaJ)) return false;
    if (stripos($celdaC, 'Nombre Tercero') !== false) return false;
    if (stripos($celdaC, 'Cuenta') !== false) return false;
    return true;
}

// ── Procesar un archivo Excel individual ──
function procesarArchivoExcel($filePath, $fileName, $conn, $mesesMap): array
{
    $reader      = IOFactory::createReaderForFile($filePath);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($filePath);
    $hoja        = $spreadsheet->getActiveSheet();
    $filas       = $hoja->getHighestDataRow();

    // Recolectar filas de datos y combos
    $filasDatos     = [];
    $paresPropiedad = [];
    $combosBorrar   = [];

    for ($fila = 1; $fila <= $filas; $fila++) {
        if (!esFilaDatosMasivo($hoja, $fila)) continue;

        $nit           = trim((string) $hoja->getCell("B{$fila}")->getValue());
        $nombreTercero = trim((string) $hoja->getCell("C{$fila}")->getValue());

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

        $mesRawRow = trim((string) $hoja->getCell("E{$fila}")->getValue());
        $mes = '';
        foreach ($mesesMap as $abr => $num) {
            if (stripos($mesRawRow, $abr) !== false) { $mes = $num; break; }
        }
        if (empty($mes)) $mes = date('m');

        $detalle  = trim((string) $hoja->getCell("F{$fila}")->getValue());
        $concepto = inferirConceptoMasivo($detalle);
        $debito   = limpiarMonedaInformeMasivo($hoja->getCell("G{$fila}")->getValue());
        $credito  = limpiarMonedaInformeMasivo($hoja->getCell("H{$fila}")->getValue());
        $codigoInm = (int) trim((string) $hoja->getCell("J{$fila}")->getValue());
        $anio = max((int) trim((string) $hoja->getCell("K{$fila}")->getValue()), (int) date('Y'));

        $filasDatos[] = [
            'nit' => $nit, 'nombre' => $nombreTercero, 'fecha' => $fecha, 'mes' => $mes,
            'concepto' => $concepto, 'detalle' => $detalle, 'debito' => $debito, 'credito' => $credito,
            'codigo' => $codigoInm, 'anio' => $anio,
        ];
        $paresPropiedad["{$nit}|{$codigoInm}"] = ['cedula' => $nit, 'contrato' => $codigoInm];
        $combosBorrar["{$codigoInm}|{$mes}|{$anio}"] = true;
    }

    if (empty($filasDatos)) {
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return ['archivo' => $fileName, 'error' => 'No se encontraron datos validos'];
    }

    // Verificar cada par en BD
    $sqlVerifica = "SELECT propietario, direccion FROM contratos_somos_propiedad
                    WHERE cedula_propietario = ? AND no_contrato = ? LIMIT 1";
    $stmtV = $conn->prepare($sqlVerifica);
    $propiedadesValidas = [];
    $paresInvalidos = [];
    foreach ($paresPropiedad as $key => $p) {
        $stmtV->bind_param('si', $p['cedula'], $p['contrato']);
        $stmtV->execute();
        $resV = $stmtV->get_result();
        $dato = $resV->fetch_assoc();
        $resV->free();
        if ($dato) {
            $propiedadesValidas[$key] = ['propietario' => $dato['propietario'], 'direccion' => $dato['direccion']];
        } else {
            $paresInvalidos[$key] = $p;
        }
    }
    $stmtV->close();

    if (empty($propiedadesValidas)) {
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return ['archivo' => $fileName, 'error' => 'Ninguna propiedad del archivo existe en la BD'];
    }

    // Borrar previos por combo
    $sqlDel = "DELETE FROM cartera_propietario WHERE codigo_inmueble = ? AND mes = ? AND anio = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $borrados = 0;
    foreach ($combosBorrar as $key => $_) {
        [$cod, $mesD, $anioD] = explode('|', $key);
        $stmtDel->bind_param('isi', $cod, $mesD, $anioD);
        $stmtDel->execute();
        $borrados += $stmtDel->affected_rows;
    }
    $stmtDel->close();

    // Insertar
    $sqlIns = "INSERT INTO cartera_propietario
        (nit_propietario, nombre_tercero, fecha, mes, concepto, detalle, debito, credito, codigo_inmueble, anio, es_giro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtIns = $conn->prepare($sqlIns);
    if (!$stmtIns) {
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return ['archivo' => $fileName, 'error' => 'Error preparando INSERT'];
    }

    $insertados = 0;
    $omitidas   = 0;
    $errores    = [];
    $porPropiedad = [];

    foreach ($filasDatos as $f) {
        $key = "{$f['nit']}|{$f['codigo']}";
        if (!isset($propiedadesValidas[$key])) { $omitidas++; continue; }
        $esGiro = ($f['concepto'] === 'Giro Renta') ? 1 : 0;
        $stmtIns->bind_param('ssssssddiis',
            $f['nit'], $f['nombre'], $f['fecha'], $f['mes'], $f['concepto'], $f['detalle'],
            $f['debito'], $f['credito'], $f['codigo'], $f['anio'], $esGiro
        );
        if ($stmtIns->execute()) {
            $insertados++;
            $porPropiedad[$key] = ($porPropiedad[$key] ?? 0) + 1;
        } else {
            $errores[] = "Contrato #{$f['codigo']}: " . $stmtIns->error;
        }
    }
    $stmtIns->close();

    $propiedadesResumen = [];
    foreach ($propiedadesValidas as $key => $info) {
        [$ced, $ctr] = explode('|', $key);
        $propiedadesResumen[] = [
            'cedula' => $ced, 'contrato' => $ctr,
            'propietario' => $info['propietario'], 'direccion' => $info['direccion'],
            'insertados' => $porPropiedad[$key] ?? 0,
        ];
    }
    foreach ($paresInvalidos as $key => $p) {
        $errores[] = "CC{$p['cedula']} / Contrato #{$p['contrato']} no existen en la BD, omitidos.";
    }

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);

    return [
        'archivo'    => $fileName,
        'insertados' => $insertados,
        'borrados'   => $borrados,
        'omitidas'   => $omitidas,
        'errores'    => $errores,
        'numPropiedades' => count($propiedadesResumen),
        'propiedades'=> $propiedadesResumen,
    ];
}

try {
    $startTime = microtime(true);

    // ── Descomprimir ZIP ──
    $tmpDir = sys_get_temp_dir() . '/cartera_import_' . uniqid();
    if (!mkdir($tmpDir, 0777, true)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No se pudo crear directorio temporal.']);
        exit;
    }

    $zip = new ZipArchive();
    if ($zip->open($archivo['tmp_name']) !== true) {
        rmdirRecursive($tmpDir);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No se pudo abrir el archivo ZIP.']);
        exit;
    }
    $zip->extractTo($tmpDir);
    $zip->close();

    // ── Buscar todos los archivos Excel recursivamente ──
    $excelFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tmpDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extFile = strtolower($file->getExtension());
            if (in_array($extFile, ['xls', 'xlsx', 'ods'])) {
                $excelFiles[] = $file->getPathname();
            }
        }
    }

    if (empty($excelFiles)) {
        logInformeMasivo('ERROR: No se encontraron archivos Excel dentro del ZIP');
        rmdirRecursive($tmpDir);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No se encontraron archivos .xls/.xlsx/.ods dentro del ZIP.']);
        exit;
    }

    // ── Procesar cada archivo ──
    $resultados      = [];
    $totalInsertados = 0;
    $totalBorrados   = 0;
    $totalErrores    = 0;
    $totalArchivos   = count($excelFiles);
    $archivosConError = 0;

    logInformeMasivo("Archivos Excel encontrados: {$totalArchivos}");

    foreach ($excelFiles as $filePath) {
        $fileName = basename($filePath);
        logInformeMasivo("Procesando: {$fileName}");
        $res = procesarArchivoExcel($filePath, $fileName, $conn, $mesesMap);

        if (isset($res['error']) && !isset($res['insertados'])) {
            $archivosConError++;
            logInformeMasivo("  ERROR: {$res['error']}");
        } else {
            $totalInsertados += $res['insertados'];
            $totalBorrados   += $res['borrados'];
            logInformeMasivo("  OK: Ins={$res['insertados']} Borr={$res['borrados']} Props={$res['numPropiedades']}");
        }
        $totalErrores += count($res['errores'] ?? []);
        $resultados[] = $res;
    }

    // Limpiar temp
    rmdirRecursive($tmpDir);

    $totalTime = round(microtime(true) - $startTime, 2);

    logInformeMasivo("RESULTADO: Archivos={$totalArchivos} OK=" . ($totalArchivos - $archivosConError) . " Error={$archivosConError} Ins={$totalInsertados} Borr={$totalBorrados} Errores={$totalErrores} Tiempo={$totalTime}s");
    logInformeMasivo('========== FIN IMPORTACION MASIVA ==========' . PHP_EOL);

    ob_end_clean();
    echo json_encode([
        'success'          => true,
        'totalArchivos'    => $totalArchivos,
        'archivosConError' => $archivosConError,
        'totalInsertados'  => $totalInsertados,
        'totalBorrados'    => $totalBorrados,
        'totalErrores'     => $totalErrores,
        'tiempo'           => $totalTime,
        'resultados'       => $resultados,
    ]);

} catch (Throwable $e) {
    if (isset($tmpDir)) rmdirRecursive($tmpDir);
    logInformeMasivo('EXCEPCION: ' . $e->getMessage());
    logInformeMasivo('========== FIN IMPORTACION MASIVA CON ERROR ==========' . PHP_EOL);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// ── Helper para borrar directorio recursivo ──
function rmdirRecursive($dir): void
{
    if (!is_dir($dir)) return;
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
    }
    rmdir($dir);
}
