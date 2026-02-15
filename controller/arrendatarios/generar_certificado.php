<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Carga DomPDF
require_once __DIR__ . '/../conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Verificar si se recibió el código
if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
    die('Código de propiedad no proporcionado.');
}

$codigo = $_GET['codigo'];

// Consultar información del inquilino
$sql = "SELECT nombre_inquilino, codigo, fecha AS fecha_ingreso, direccion FROM proprieter WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $codigo);
$stmt->execute();
$stmt->bind_result($nombre_inquilino, $codigo_propiedad, $fecha_ingreso, $direccion);
if (!$stmt->fetch()) {
    die('Propiedad no encontrada.');
}
$stmt->close();
$conn->close();

// Configurar DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Para cargar imágenes remotas si es necesario
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
    </style>
</head>
<body>
    <div class="content">
        <h1>CERTIFICADO DE RESIDENCIA</h1>
        <p>Se certifica que el/la señor/a <strong>' . htmlspecialchars($nombre_inquilino) . '</strong>,</p>
        <p>identificado/a con documento de identidad correspondiente, reside actualmente en la propiedad con código <strong>' . htmlspecialchars($codigo_propiedad) . '</strong>,</p>
        <p>ubicada en <strong>' . htmlspecialchars($direccion) . '</strong>, desde la fecha <strong>' . date('d/m/Y', strtotime($fecha_ingreso)) . '</strong>.</p>
        <p>Este certificado se expide a solicitud del interesado para los fines que estime convenientes.</p>
        <p>Dado en [Ciudad], a los ' . date('d') . ' días del mes de ' . date('m') . ' de ' . date('Y') . '.</p>
        <div class="firma">
            <p>______________________________</p>
            <p>Firma Autorizada</p>
            <p>Somos Propiedad</p>
        </div>
    </div>
</body>
</html>
';

// Cargar HTML en DomPDF
$dompdf->loadHtml($html);

// Configurar tamaño de papel (A4)
$dompdf->setPaper('A4', 'portrait');

// Renderizar PDF
$dompdf->render();

// Obtener output en base64
$pdfOutput = $dompdf->output();
echo base64_encode($pdfOutput);
?>