<?php
session_start();
require_once __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Verificar sesión y rol
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] != 6) {
    http_response_code(403);
    exit('Acceso denegado');
}

$doc_propietario = $_SESSION['username'] ?? '';

$filtroMes      = $_GET['mes']          ?? '';
$filtroAnio     = $_GET['anio']         ?? '';
$filtroCodInm   = $_GET['cod_inmueble'] ?? '';

// Construir WHERE dinámico
$where  = "c.nit_propietario = ?";
$params = [$doc_propietario];
$types  = "s";

if (!empty($filtroMes)) {
    $where   .= " AND c.mes = ?";
    $params[] = $filtroMes;
    $types   .= "s";
}
if (!empty($filtroAnio)) {
    $where   .= " AND c.anio = ?";
    $params[] = (int)$filtroAnio;
    $types   .= "i";
}
if (!empty($filtroCodInm) && $filtroCodInm !== 'todos') {
    $where   .= " AND c.codigo_inmueble = ?";
    $params[] = $filtroCodInm;
    $types   .= "s";
}

$sql  = "SELECT c.*, csp.direccion
         FROM cartera_propietario c
         LEFT JOIN contratos_somos_propiedad csp ON c.codigo_inmueble = csp.no_contrato
         WHERE $where
         ORDER BY c.codigo_inmueble, c.es_giro ASC, c.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res  = $stmt->get_result();

// Agrupar por inmueble
$carteraAgrupada = [];
$totales         = ['debito' => 0, 'credito' => 0];
$nombreTercero   = '';

$meses = [
    '01' => 'Enero',     '02' => 'Febrero',   '03' => 'Marzo',     '04' => 'Abril',
    '05' => 'Mayo',      '06' => 'Junio',      '07' => 'Julio',     '08' => 'Agosto',
    '09' => 'Septiembre','10' => 'Octubre',    '11' => 'Noviembre', '12' => 'Diciembre'
];

while ($mov = $res->fetch_assoc()) {
    $codigo = $mov['codigo_inmueble'];
    if (empty($nombreTercero)) $nombreTercero = $mov['nombre_tercero'];

    if (!isset($carteraAgrupada[$codigo])) {
        $carteraAgrupada[$codigo] = [
            'direccion'      => $mov['direccion'] ?? 'N/A',
            'movimientos'    => [],
            'subtotalDebito' => 0,
            'subtotalCredito'=> 0,
        ];
    }
    $carteraAgrupada[$codigo]['movimientos'][]     = $mov;
    $carteraAgrupada[$codigo]['subtotalDebito']  += $mov['debito'];
    $carteraAgrupada[$codigo]['subtotalCredito'] += $mov['credito'];
    $totales['debito']  += $mov['debito'];
    $totales['credito'] += $mov['credito'];
}

// Si no hay movimientos, devolver JSON de error en lugar de PDF vacío
if (empty($carteraAgrupada)) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(422);
    echo json_encode(['error' => true, 'message' => 'No hay movimientos de cartera para el período seleccionado.']);
    exit;
}

// Texto descriptivo del período
if (!empty($filtroMes) && !empty($filtroAnio)) {
    $periodoTexto = ($meses[$filtroMes] ?? $filtroMes) . ' ' . $filtroAnio;
} elseif (!empty($filtroAnio)) {
    $periodoTexto = 'Año ' . $filtroAnio;
} elseif (!empty($filtroMes)) {
    $periodoTexto = $meses[$filtroMes] ?? $filtroMes;
} else {
    $periodoTexto = 'Todos los períodos';
}

// Logo como base64 para evitar problemas de rutas en DomPDF
$logoPath   = __DIR__ . '/../../img/somosLogo.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// Construir filas de la tabla
$rowsHtml = '';
foreach ($carteraAgrupada as $codInm => $dataInm) {
    $rowsHtml .= '
    <tr class="inmueble-header">
        <td colspan="9"><strong>Inmueble: ' . htmlspecialchars($codInm) . ' &mdash; ' . htmlspecialchars($dataInm['direccion']) . '</strong></td>
    </tr>';

    foreach ($dataInm['movimientos'] as $mov) {
        $rowStyle   = $mov['es_giro'] ? 'background-color:#c8e6c9;font-weight:bold;' : '';
        $debitoStr  = $mov['debito']  > 0 ? '$ ' . number_format($mov['debito'],  2, ',', '.') : '$ 0,00';
        $creditoStr = $mov['credito'] > 0 ? '$ ' . number_format($mov['credito'], 2, ',', '.') : '$ 0,00';
        $mesNombre  = $meses[$mov['mes']] ?? $mov['mes'];
        $detalle    = $mov['es_giro']
            ? '[GIRO] ' . htmlspecialchars($mov['detalle'])
            : htmlspecialchars($mov['detalle']);

        $rowsHtml .= '
        <tr style="' . $rowStyle . '">
            <td>' . htmlspecialchars($mov['nit_propietario'])  . '</td>
            <td>' . htmlspecialchars($mov['nombre_tercero'])   . '</td>
            <td>' . date('d/m/Y', strtotime($mov['fecha']))    . '</td>
            <td>' . $mov['anio'] . ' &ndash; ' . $mesNombre   . '</td>
            <td>' . $detalle                                   . '</td>
            <td class="text-right">' . $debitoStr             . '</td>
            <td class="text-right">' . $creditoStr            . '</td>
            <td class="text-center">' . htmlspecialchars($mov['codigo_inmueble']) . '</td>
            <td>' . htmlspecialchars($mov['anio'])             . '</td>
        </tr>';
    }

    $rowsHtml .= '
    <tr class="subtotal-row">
        <td colspan="5" class="text-right"><em>Subtotal inmueble ' . htmlspecialchars($codInm) . ':</em></td>
        <td class="text-right">$ ' . number_format($dataInm['subtotalDebito'],  2, ',', '.') . '</td>
        <td class="text-right">$ ' . number_format($dataInm['subtotalCredito'], 2, ',', '.') . '</td>
        <td colspan="2"></td>
    </tr>';
}

$neto      = $totales['credito'] - $totales['debito'];
$netoColor = $neto >= 0 ? 'color:#1a6e1a;' : 'color:#b00020;';

// HTML del documento PDF
$logoImg     = $logoBase64 ? '<img src="' . $logoBase64 . '" alt="Logo">' : '';
$watermarkImg= $logoBase64 ? '<img class="watermark" src="' . $logoBase64 . '" alt="">' : '';

$html = '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page {
        size: letter portrait;
        margin: 18mm 12mm 24mm 12mm;
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 8pt;
        color: #222;
    }
    /* Marca de agua centrada */
    .watermark {
        position: fixed;
        top: 65mm;
        left: 50%;
        margin-left: -175px;
        width: 350px;
        opacity: 0.08;
        z-index: -1000;
    }
    /* Pie de página fijo */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        border-top: 1px solid #bbb;
        padding-top: 3px;
        text-align: center;
        font-size: 6.5pt;
        color: #666;
    }
    /* Encabezado */
    .doc-header {
        text-align: center;
        margin-bottom: 6px;
    }
    .doc-header img {
        height: 48px;
    }
    .doc-title {
        text-align: center;
        margin-bottom: 6px;
    }
    .doc-title h2 {
        font-size: 12pt;
        margin: 2px 0;
        color: #1a5c2c;
    }
    .doc-title p {
        margin: 1px 0;
        font-size: 8.5pt;
        color: #444;
    }
    .info-bar {
        font-size: 7.5pt;
        border-bottom: 1.5px solid #4caf50;
        padding-bottom: 4px;
        margin-bottom: 6px;
    }
    /* Tabla */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7pt;
    }
    thead tr {
        background-color: #d4edda;
    }
    th {
        border: 1px solid #999;
        padding: 4px 4px;
        text-align: left;
    }
    td {
        border: 1px solid #ddd;
        padding: 2px 4px;
    }
    .text-right  { text-align: right; }
    .text-center { text-align: center; }
    .inmueble-header td {
        background-color: #e8f5e9;
        border-top: 2px solid #4caf50;
        font-size: 7.5pt;
    }
    .subtotal-row td {
        background-color: #f1f8e9;
        font-style: italic;
    }
    .total-row td {
        background-color: #fff9c4;
        font-weight: bold;
        border-top: 2px solid #555;
    }
    /* Resumen */
    .summary-box {
        margin-top: 8px;
        border: 1px solid #ccc;
        padding: 6px 8px;
        font-size: 8pt;
        background-color: #fafafa;
    }
    .summary-box table {
        font-size: 8pt;
        border: none;
    }
    .summary-box td {
        border: none;
        padding: 2px 6px;
    }
</style>
</head>
<body>

' . $watermarkImg . '

<div class="footer">
    SOMOS PROPIEDAD S.A.S. &mdash; Estado de Cuenta Cartera &mdash; Generado el ' . date('d/m/Y H:i') . '
</div>

<div class="doc-header">' . $logoImg . '</div>

<div class="doc-title">
    <h2>SOMOS PROPIEDAD S.A.S.</h2>
    <p>Estado de Cuenta &mdash; Cartera Propietario</p>
    <p>Per&iacute;odo: <strong>' . htmlspecialchars($periodoTexto) . '</strong></p>
</div>

<div class="info-bar">
    <strong>NIT / CC:</strong> ' . htmlspecialchars($doc_propietario) . ' &nbsp;&nbsp;
    <strong>Nombre:</strong> ' . htmlspecialchars($nombreTercero) . ' &nbsp;&nbsp;
    <strong>Cuenta:</strong> 28150501
</div>

' . (empty($carteraAgrupada) ? '<p style="text-align:center;color:#666;margin-top:30px;">No hay movimientos para el período seleccionado.</p>' : '
<table>
    <thead>
        <tr>
            <th style="width:8%">Nit / CC</th>
            <th style="width:18%">Nombre Tercero</th>
            <th style="width:8%">Fecha</th>
            <th style="width:10%">Mes</th>
            <th>Detalle</th>
            <th class="text-right" style="width:10%">D&eacute;bito</th>
            <th class="text-right" style="width:10%">Cr&eacute;dito</th>
            <th class="text-center" style="width:7%">No. Inm</th>
            <th style="width:5%">A&ntilde;o</th>
        </tr>
    </thead>
    <tbody>
        ' . $rowsHtml . '
        <tr class="total-row">
            <td colspan="5" class="text-right">TOTALES GENERALES:</td>
            <td class="text-right">$ ' . number_format($totales['debito'],  2, ',', '.') . '</td>
            <td class="text-right">$ ' . number_format($totales['credito'], 2, ',', '.') . '</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

<div class="summary-box">
    <table>
        <tr>
            <td><strong>Total Cr&eacute;ditos (a favor):</strong></td>
            <td style="color:#1a6e1a;font-weight:bold;">$ ' . number_format($totales['credito'], 2, ',', '.') . '</td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td><strong>Total D&eacute;bitos (retenciones):</strong></td>
            <td style="color:#b00020;font-weight:bold;">$ ' . number_format($totales['debito'], 2, ',', '.') . '</td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td><strong>Neto (Cr&eacute;ditos &minus; D&eacute;bitos):</strong></td>
            <td style="' . $netoColor . 'font-weight:bold;">$ ' . number_format($neto, 2, ',', '.') . '</td>
        </tr>
    </table>
</div>') . '

</body>
</html>';

// Generar PDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');
$dompdf->render();

$filename = 'cartera_' . preg_replace('/[^a-zA-Z0-9_]/', '', $doc_propietario) . '_' . date('Ymd') . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
