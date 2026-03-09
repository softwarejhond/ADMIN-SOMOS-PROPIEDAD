<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

header('Content-Type: application/json');

if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Código de propiedad no proporcionado.']);
    exit;
}

$codigo = (int)$_GET['codigo'];

// Verificar si ya existe un certificado para esta propiedad
$sqlExist = "SELECT consecutivo, ruta_archivo FROM certificados_residencia WHERE codigo_propiedad = ? ORDER BY id DESC LIMIT 1";
$stmtExist = $conn->prepare($sqlExist);
$stmtExist->bind_param("i", $codigo);
$stmtExist->execute();
$stmtExist->bind_result($consecutivoExist, $rutaRelativaExist);
$existe = $stmtExist->fetch();
$stmtExist->close();

if ($existe) {
    $rutaCompletaExist = __DIR__ . '/../../' . $rutaRelativaExist;
    if (file_exists($rutaCompletaExist)) {
        $pdfContent = file_get_contents($rutaCompletaExist);
        echo json_encode([
            'pdf'         => base64_encode($pdfContent),
            'consecutivo' => $consecutivoExist
        ]);
        $conn->close();
        exit;
    }
}

// Consultar información del inquilino
$sql = "SELECT nombre_inquilino, codigo, fecha AS fecha_ingreso, direccion, doc_inquilino FROM proprieter WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $codigo);
$stmt->execute();
$stmt->bind_result($nombre_inquilino, $codigo_propiedad, $fecha_ingreso, $direccion, $doc_inquilino);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Propiedad no encontrada.']);
    exit;
}
$stmt->close();

// Generar consecutivo único (8 caracteres: prefijo CR + 6 alfanuméricos)
function generarConsecutivo(mysqli $conn): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    do {
        $aleatorio = '';
        for ($i = 0; $i < 6; $i++) {
            $aleatorio .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $consecutivo = 'CR' . $aleatorio;
        $check = $conn->prepare("SELECT id FROM certificados_residencia WHERE consecutivo = ?");
        $check->bind_param("s", $consecutivo);
        $check->execute();
        $check->store_result();
        $esUnico = $check->num_rows === 0;
        $check->close();
    } while (!$esUnico);
    return $consecutivo;
}

$consecutivo = generarConsecutivo($conn);

// Rutas de guardado
$dirCertificados = __DIR__ . '/../../certficados/residencia/';
if (!is_dir($dirCertificados)) {
    mkdir($dirCertificados, 0755, true);
}
$nombreArchivo  = $consecutivo . '.pdf';
$rutaCompleta   = $dirCertificados . $nombreArchivo;
$rutaRelativa   = 'certficados/residencia/' . $nombreArchivo;

// Configurar DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// HTML del certificado
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Residencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            text-align: justify;
        }
        .content {
            padding: 40px;
            margin: 50px auto;
            max-width: 600px;
            background-color: white;
        }
        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            font-size: 12px;
            line-height: 1.5;
            margin: 10px 0;
            text-align: justify;
        }
        .firma {
            margin-top: 40px;
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="footer">
        Código de verificación: <strong>' . htmlspecialchars($consecutivo) . '</strong>
        &nbsp;|&nbsp; Generado el ' . date('d/m/Y H:i') . '
        &nbsp;|&nbsp; Somos Propiedad
    </div>
    <div class="content">
        <h1>CERTIFICADO DE RESIDENCIA</h1>
        <p>Se certifica que el/la señor/a <strong>' . htmlspecialchars($nombre_inquilino) . '</strong>,</p>
        <p>identificado/a con documento de identidad correspondiente, reside actualmente en la propiedad con código <strong>' . htmlspecialchars($codigo_propiedad) . '</strong>,</p>
        <p>ubicada en <strong>' . htmlspecialchars($direccion) . '</strong>, desde la fecha <strong>' . date('d/m/Y', strtotime($fecha_ingreso)) . '</strong>.</p>
        <p>Este certificado se expide a solicitud del interesado para los fines que estime convenientes.</p>
        <p>Dado en [Ciudad], a los ' . date('d') . ' días del mes de ' . date('F') . ' de ' . date('Y') . '.</p>
        <div class="firma">
            <p>______________________________</p>
            <p>Firma Autorizada</p>
            <p>Somos Propiedad</p>
        </div>
    </div>
</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdfOutput = $dompdf->output();

// Guardar archivo en disco
file_put_contents($rutaCompleta, $pdfOutput);

// Registrar en base de datos
$sqlInsert = "INSERT INTO certificados_residencia (codigo_propiedad, doc_inquilino, consecutivo, ruta_archivo) VALUES (?, ?, ?, ?)";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("isss", $codigo_propiedad, $doc_inquilino, $consecutivo, $rutaRelativa);
$stmtInsert->execute();
$stmtInsert->close();

$conn->close();

echo json_encode([
    'pdf'         => base64_encode($pdfOutput),
    'consecutivo' => $consecutivo
]);