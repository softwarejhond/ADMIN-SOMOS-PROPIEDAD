<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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
if (!in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Sin permisos para esta accion.']);
    exit;
}

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
    echo json_encode(['success' => false, 'message' => 'Solicitud invalida.']);
    exit;
}

$archivo = $_FILES['archivo_cartera'];
$extensionesPermitidas = ['xls', 'xlsx', 'ods'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if ($archivo['error'] !== UPLOAD_ERR_OK || !in_array($ext, $extensionesPermitidas)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Archivo invalido. Solo se permiten .xls, .xlsx u .ods.']);
    exit;
}

try {
    $reader      = IOFactory::createReaderForFile($archivo['tmp_name']);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($archivo['tmp_name']);
    $hoja        = $spreadsheet->getActiveSheet();
    $filas       = $hoja->getHighestDataRow();

    $insertados      = 0;
    $actualizados   = 0;
    $usuariosCreados = 0;
    $errores         = [];
    $cedulasProcesadas = [];

    $sql = "INSERT INTO contratos_somos_propiedad
                (no_contrato, direccion, ciudad, vr_canon, vr_administracion,
                 valor_iva, canon_total, propietario, cedula_propietario,
                 arrendatario, cedula_arrendatario, aseguradora, no_solicitud, multiple)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                direccion           = VALUES(direccion),
                ciudad              = VALUES(ciudad),
                vr_canon            = VALUES(vr_canon),
                vr_administracion   = VALUES(vr_administracion),
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

    // Sentencia para crear usuario si no existe (username es PK int)
    $sqlUsuario = "INSERT IGNORE INTO users
        (username, password, nombre, rol, foto, orden,
         fechaCreacionUser, email, genero, telefono, direccion, edad)
     VALUES (?, ?, ?, 6, 'default.png', 0, ?, '', 'No especificado', '', '', 0)";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    if (!$stmtUsuario) {
        throw new RuntimeException('Error preparando sentencia de usuario: ' . $conn->error);
    }
    $hoy = date('d/m/Y');

    for ($fila = 2; $fila <= $filas; $fila++) {
        $celdaA = trim((string) $hoja->getCell("A{$fila}")->getValue());

        if (!is_numeric($celdaA) || $celdaA === '') {
            continue;
        }

        $noContrato = (int) $celdaA;
        if ($noContrato <= 0) {
            $errores[] = "Fila {$fila}: NoContrato invalido ({$celdaA}), omitida.";
            continue;
        }

        $direccion          = trim((string) $hoja->getCell("B{$fila}")->getValue());
        $ciudad             = trim((string) $hoja->getCell("C{$fila}")->getValue());
        $vrCanon            = limpiarMonedaContratos($hoja->getCell("D{$fila}")->getValue());
        $vrAdministracion   = limpiarMonedaContratos($hoja->getCell("E{$fila}")->getValue());
        $valorIva           = limpiarMonedaContratos($hoja->getCell("F{$fila}")->getValue());
        $canonTotal         = limpiarMonedaContratos($hoja->getCell("G{$fila}")->getValue());
        $propietario        = trim((string) $hoja->getCell("H{$fila}")->getValue());
        $cedulaPropietario  = trim((string) $hoja->getCell("I{$fila}")->getValue());
        $arrendatario       = trim((string) $hoja->getCell("J{$fila}")->getValue());
        $cedulaArrendatario = trim((string) $hoja->getCell("K{$fila}")->getValue());
        $aseguradora        = trim((string) $hoja->getCell("L{$fila}")->getValue());
        $noSolicitud        = trim((string) $hoja->getCell("M{$fila}")->getValue());
        $multipleRaw        = strtolower(trim((string) $hoja->getCell("N{$fila}")->getValue()));
        $multiple           = ($multipleRaw === 'si' || $multipleRaw === 'si') ? 'Si' : 'No';

        $stmt->bind_param(
            'issddddsssssss',
            $noContrato,
            $direccion,
            $ciudad,
            $vrCanon,
            $vrAdministracion,
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

            // Crear usuario propietario si la cédula es numérica y aún no fue procesada
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
            $errores[] = "Fila {$fila} (contrato #{$noContrato}): " . $stmt->error;
        }
    }

    $stmtUsuario->close();
    $stmt->close();

    ob_end_clean();
    echo json_encode([
        'success'         => true,
        'insertados'      => $insertados,
        'actualizados'    => $actualizados,
        'usuariosCreados' => $usuariosCreados,
        'errores'         => $errores,
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
