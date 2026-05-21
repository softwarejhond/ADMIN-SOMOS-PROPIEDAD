<?php
session_start();

// Solo administradores pueden ejecutar la importación
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
if (!in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/conexion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

// ─── Utilidades ──────────────────────────────────────────────────────────────

/**
 * Limpia un valor monetario del XLS: puede venir como número o como
 * cadena "$ 1.600.000" / "$ 1,600,000".
 */
function limpiarMoneda($valor): float
{
    if (is_numeric($valor)) {
        return (float) $valor;
    }
    // Eliminar símbolo $, espacios y separadores de miles (punto o coma)
    $limpio = preg_replace('/[^0-9,.]/', '', trim((string) $valor));
    // Si tiene coma como separador decimal (ej. 1.600.000 o 1,600,000)
    // Detectar si el último separador es coma o punto
    $ultimaComa  = strrpos($limpio, ',');
    $ultimoPunto = strrpos($limpio, '.');
    if ($ultimaComa !== false && ($ultimoPunto === false || $ultimaComa > $ultimoPunto)) {
        // Formato: 1.600,00 → decimal con coma
        $limpio = str_replace('.', '', $limpio);
        $limpio = str_replace(',', '.', $limpio);
    } else {
        // Formato: 1,600.00 o 1.600.000 → quitar separadores de miles
        $limpio = str_replace(',', '', $limpio);
    }
    return (float) $limpio;
}

// ─── Procesamiento del formulario ────────────────────────────────────────────

$resultados = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_xls'])) {
    $archivo = $_FILES['archivo_xls'];

    // Validar tipo de archivo
    $extensionesPermitidas = ['xls', 'xlsx', 'ods'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if ($archivo['error'] !== UPLOAD_ERR_OK || !in_array($ext, $extensionesPermitidas)) {
        $resultados = ['error' => 'Archivo inválido. Solo se permiten .xls, .xlsx u .ods.'];
    } else {
        $tmpPath = $archivo['tmp_name'];

        try {
            $reader      = IOFactory::createReaderForFile($tmpPath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tmpPath);
            $hoja        = $spreadsheet->getActiveSheet();
            $filas       = $hoja->getHighestDataRow();

            $insertados  = 0;
            $actualizados = 0;
            $errores     = [];

            // Columnas esperadas (basadas en BD-CONTRATOS-SOMOS-PROPIEDAD-20260519.csv):
            // A: NoContrato   B: Direccion       C: Ciudad
            // D: Vr Canon     E: Vr Administracion  F: Valor Iva
            // G: Canon+Admon+IVA  H: Propietario  I: P Cedula
            // J: Arrendatario K: A. Cedula        L: Aseguradora
            // M: NoSolicitud  N: Multiple

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

            // Leer desde fila 1 — las filas con columna A no numérica
            // (encabezados, títulos, vacías) se omiten automáticamente.
            for ($fila = 1; $fila <= $filas; $fila++) {
                $celdaA = trim((string) $hoja->getCell("A{$fila}")->getValue());

                // Saltar filas vacías o no numéricas (encabezados / títulos)
                if (!is_numeric($celdaA) || $celdaA === '') {
                    continue;
                }

                $noContrato = (int) $celdaA;

                if ($noContrato <= 0) {
                    $errores[] = "Fila {$fila}: NoContrato inválido ({$celdaA}), omitida.";
                    continue;
                }

                $direccion          = trim((string) $hoja->getCell("B{$fila}")->getValue());
                $ciudad             = trim((string) $hoja->getCell("C{$fila}")->getValue());
                $vrCanon            = limpiarMoneda($hoja->getCell("D{$fila}")->getValue());
                $vrAdministracion   = limpiarMoneda($hoja->getCell("E{$fila}")->getValue());
                $valorIva           = limpiarMoneda($hoja->getCell("F{$fila}")->getValue());
                $canonTotal         = limpiarMoneda($hoja->getCell("G{$fila}")->getValue());
                $propietario        = trim((string) $hoja->getCell("H{$fila}")->getValue());
                $cedulaPropietario  = trim((string) $hoja->getCell("I{$fila}")->getValue());
                $arrendatario       = trim((string) $hoja->getCell("J{$fila}")->getValue());
                $cedulaArrendatario = trim((string) $hoja->getCell("K{$fila}")->getValue());
                $aseguradora        = trim((string) $hoja->getCell("L{$fila}")->getValue());
                $noSolicitud        = trim((string) $hoja->getCell("M{$fila}")->getValue());
                $multipleRaw        = strtolower(trim((string) $hoja->getCell("N{$fila}")->getValue()));
                $multiple           = ($multipleRaw === 'si' || $multipleRaw === 'sí') ? 'Si' : 'No';

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
                    // affected_rows = 1 → insert, = 2 → update (ON DUPLICATE KEY)
                    if ($stmt->affected_rows === 1) {
                        $insertados++;
                    } else {
                        $actualizados++;
                    }
                } else {
                    $errores[] = "Fila {$fila} (contrato #{$noContrato}): " . $stmt->error;
                }
            }

            $stmt->close();
            $resultados = compact('insertados', 'actualizados', 'errores');

        } catch (Throwable $e) {
            $resultados = ['error' => htmlspecialchars($e->getMessage())];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Contratos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
</head>
<body class="bg-light">


<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                        Importar contratos desde XLS / XLSX
                    </h5>
                </div>
                <div class="card-body">

                    <?php if ($resultados !== null): ?>
                        <?php if (isset($resultados['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle me-2"></i><?= $resultados['error'] ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <strong><i class="bi bi-check-circle me-2"></i>Importación completada</strong><br>
                                <span class="text-success">Insertados: <strong><?= $resultados['insertados'] ?></strong></span> &nbsp;|&nbsp;
                                <span class="text-primary">Actualizados: <strong><?= $resultados['actualizados'] ?></strong></span>
                            </div>
                            <?php if (!empty($resultados['errores'])): ?>
                                <div class="alert alert-warning">
                                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Filas con advertencias:</strong>
                                    <ul class="mb-0 mt-2">
                                        <?php foreach ($resultados['errores'] as $err): ?>
                                            <li><?= htmlspecialchars($err) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <p class="text-muted small mb-4">
                        El archivo debe tener los encabezados en la <strong>fila 1</strong> y los datos a partir de la <strong>fila 2</strong>,
                        con las columnas en este orden:
                        <code>NoContrato, Direccion, Ciudad, Vr Canon, Vr Administracion, Valor Iva,
                        Canon+Admon+IVA, Propietario, P Cedula, Arrendatario, A. Cedula, Aseguradora,
                        NoSolicitud, Multiple</code>.
                        Los registros existentes (<code>no_contrato</code>) serán <strong>actualizados</strong>.
                    </p>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="archivo_xls" class="form-label fw-semibold">
                                <i class="bi bi-upload me-1"></i> Seleccionar archivo
                            </label>
                            <input type="file"
                                   id="archivo_xls"
                                   name="archivo_xls"
                                   class="form-control"
                                   accept=".xls,.xlsx,.ods"
                                   required>
                            <div class="form-text">Formatos aceptados: .xls, .xlsx, .ods</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">
                                <i class="bi bi-cloud-upload me-2"></i>Importar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="index.php" class="text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                </a>
            </div>

        </div>
    </div>
</div>

<?php include("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
