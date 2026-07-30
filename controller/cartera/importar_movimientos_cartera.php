<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

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

$logDir = __DIR__ . '/../../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/import_cartera.log';

function logImport($msg) {
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

logImport('========== INICIO IMPORTACION ==========');
logImport('Usuario: ' . ($_SESSION['username'] ?? 'desconocido'));
logImport('Archivo: ' . ($_FILES['archivo_cartera']['name'] ?? 'N/A'));

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function limpiarMonedaContratos($valor): float
{
    if (is_numeric($valor)) {
        return (float) $valor;
    }
    $limpio = preg_replace('/[^0-9,.]/', '', trim((string) $valor));
    $ultimaComa  = strrpos($limpio, ',');
    $ultimoPunto = strrpos($limpio, '.');
    if ($ultimaComa !== false && ($ultimoPunto === false || $ultimaComa > $ultimoPunto)) {
        $limpio = str_replace('.', '', $limpio);
        $limpio = str_replace(',', '.', $limpio);
    } else {
        $limpio = str_replace(',', '', $limpio);
    }
    return (float) $limpio;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_cartera'])) {
    ob_end_clean();
    logImport('ERROR: Solicitud invalida');
    echo json_encode(['success' => false, 'message' => 'Solicitud invalida.']);
    exit;
}

$archivo = $_FILES['archivo_cartera'];
$extensionesPermitidas = ['xls', 'xlsx', 'ods'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if ($archivo['error'] !== UPLOAD_ERR_OK || !in_array($ext, $extensionesPermitidas)) {
    ob_end_clean();
    logImport('ERROR: Archivo invalido. Ext=' . $ext . ' ErrorCode=' . $archivo['error']);
    echo json_encode(['success' => false, 'message' => 'Archivo invalido. Solo se permiten .xls, .xlsx u .ods.']);
    exit;
}

try {
    $startTime = microtime(true);
    logImport('Lectura del archivo iniciada. Mem: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB');

    $reader      = IOFactory::createReaderForFile($archivo['tmp_name']);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($archivo['tmp_name']);
    $hoja        = $spreadsheet->getActiveSheet();
    $filas       = $hoja->getHighestDataRow();

    logImport('Archivo cargado. Filas totales: ' . $filas . '. Mem: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'MB');

    $insertados      = 0;
    $actualizados   = 0;
    $usuariosCreados = 0;
    $omitidas        = 0;
    $errores         = [];
    $cedulasProcesadas = [];

    $sql = "INSERT INTO contratos_somos_propiedad
                (no_contrato, direccion, ciudad, vr_canon, vr_administracion,
                 vr_comision, valor_iva, canon_total, propietario, cedula_propietario,
                 arrendatario, cedula_arrendatario, aseguradora, no_solicitud, multiple)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                direccion           = VALUES(direccion),
                ciudad              = VALUES(ciudad),
                vr_canon            = VALUES(vr_canon),
                vr_administracion   = VALUES(vr_administracion),
                vr_comision         = VALUES(vr_comision),
                valor_iva           = VALUES(valor_iva),
                canon_total         = VALUES(canon_total),
                propietario         = VALUES(propietario),
                cedula_propietario  = VALUES(cedula_propietario),
                arrendatario        = VALUES(arrendatario),
                cedula_arrendatario = VALUES(cedula_arrendatario),
                aseguradora         = VALUES(aseguradora),
                no_solicitud        = VALUES(no_solicitud),
                multiple            = VALUES(multiple)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new RuntimeException('Error preparando sentencia: ' . $conn->error);
    }

    $sqlUsuario = "INSERT IGNORE INTO users
        (username, password, nombre, rol, foto, orden,
         fechaCreacionUser, email, genero, telefono, direccion, edad)
     VALUES (?, ?, ?, 6, 'default.png', 0, ?, '', 'No especificado', '', '', 0)";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    if (!$stmtUsuario) {
        throw new RuntimeException('Error preparando sentencia de usuario: ' . $conn->error);
    }
    $hoy = date('d/m/Y');

    $lastLogRow = 0;
    $logInterval = max(1, (int)($filas / 10));

    for ($fila = 2; $fila <= $filas; $fila++) {
        if ($fila - $lastLogRow >= $logInterval) {
            $elapsed = round(microtime(true) - $startTime, 1);
            $progress = round(($fila / $filas) * 100, 1);
            logImport("Progreso: {$progress}% (fila {$fila}/{$filas}) - Ins:{$insertados} Act:{$actualizados} Usr:{$usuariosCreados} - {$elapsed}s");
            $lastLogRow = $fila;
        }

        $celdaA = trim((string) $hoja->getCell("A{$fila}")->getValue());

        if (!is_numeric($celdaA) || $celdaA === '') {
            $omitidas++;
            continue;
        }

        $noContrato = (int) $celdaA;
        if ($noContrato <= 0) {
            $errores[] = "Fila {$fila}: NoContrato invalido ({$celdaA}), omitida.";
            logImport("WARN: Fila {$fila}: NoContrato invalido ({$celdaA})");
            continue;
        }

        $direccion          = trim((string) $hoja->getCell("B{$fila}")->getValue());
        $ciudad             = trim((string) $hoja->getCell("C{$fila}")->getValue());
        $vrCanon            = limpiarMonedaContratos($hoja->getCell("D{$fila}")->getValue());
        $vrAdministracion   = limpiarMonedaContratos($hoja->getCell("E{$fila}")->getValue());
        $vrComision         = limpiarMonedaContratos($hoja->getCell("F{$fila}")->getValue());
        $valorIva           = limpiarMonedaContratos($hoja->getCell("G{$fila}")->getValue());
        $canonTotal         = limpiarMonedaContratos($hoja->getCell("H{$fila}")->getValue());
        $propietario        = trim((string) $hoja->getCell("I{$fila}")->getValue());
        $cedulaPropietario  = trim((string) $hoja->getCell("J{$fila}")->getValue());
        $arrendatario       = trim((string) $hoja->getCell("K{$fila}")->getValue());
        $cedulaArrendatario = trim((string) $hoja->getCell("L{$fila}")->getValue());
        $aseguradora        = trim((string) $hoja->getCell("M{$fila}")->getValue());
        $noSolicitud        = trim((string) $hoja->getCell("N{$fila}")->getValue());
        $multipleRaw        = strtolower(trim((string) $hoja->getCell("O{$fila}")->getValue()));
        $multiple           = ($multipleRaw === 'si' || $multipleRaw === 'sí') ? 'Si' : 'No';

        $stmt->bind_param(
            'issdddddsssssss',
            $noContrato,
            $direccion,
            $ciudad,
            $vrCanon,
            $vrAdministracion,
            $vrComision,
            $valorIva,
            $canonTotal,
            $propietario,
            $cedulaPropietario,
            $arrendatario,
            $cedulaArrendatario,
            $aseguradora,
            $noSolicitud,
            $multiple
        );

        if ($stmt->execute()) {
            if ($stmt->affected_rows === 1) {
                $insertados++;
            } else {
                $actualizados++;
            }

            if ($cedulaPropietario !== '' && is_numeric($cedulaPropietario)
                && !isset($cedulasProcesadas[$cedulaPropietario])) {
                $cedulasProcesadas[$cedulaPropietario] = true;
                $usernameInt  = (int) $cedulaPropietario;
                $passwordHash = password_hash($cedulaPropietario, PASSWORD_BCRYPT);
                $nombreUser   = $propietario ?: 'Sin nombre';
                $stmtUsuario->bind_param('isss', $usernameInt, $passwordHash, $nombreUser, $hoy);
                if ($stmtUsuario->execute() && $stmtUsuario->affected_rows > 0) {
                    $usuariosCreados++;
                }
            }
        } else {
            $msg = "Fila {$fila} (contrato #{$noContrato}): " . $stmt->error;
            $errores[] = $msg;
            logImport('ERROR: ' . $msg);
        }
    }

    $stmtUsuario->close();
    $stmt->close();
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);

    $totalTime = round(microtime(true) - $startTime, 2);
    $mensaje = sprintf(
        'Finalizado en %ss | Insertados=%d Actualizados=%d Usuarios=%d Omitidas=%d Errores=%d | MemPeak=%.2fMB',
        $totalTime, $insertados, $actualizados, $usuariosCreados, $omitidas, count($errores),
        round(memory_get_peak_usage() / 1024 / 1024, 2)
    );
    logImport($mensaje);
    logImport('========== FIN IMPORTACION ==========' . PHP_EOL);

    ob_end_clean();
    echo json_encode([
        'success'         => true,
        'insertados'      => $insertados,
        'actualizados'    => $actualizados,
        'usuariosCreados' => $usuariosCreados,
        'omitidas'        => $omitidas,
        'total'           => $filas - 1,
        'tiempo'          => $totalTime,
        'errores'         => $errores,
    ]);

} catch (Throwable $e) {
    logImport('EXCEPCION: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    logImport('========== FIN IMPORTACION CON ERROR ==========' . PHP_EOL);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
