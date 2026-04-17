<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if (!in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

require_once __DIR__ . "/../conexion.php";

$nitFiltro = $_POST['nit_propietario'] ?? '';
$mes = $_POST['mes'] ?? '';
$anio = intval($_POST['anio'] ?? date('Y'));
$fechaGiro = $_POST['fecha_giro'] ?? date('Y-m-d');

if (empty($mes) || $anio <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mes y año son obligatorios']);
    exit;
}

$meses = [
    '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
];
$mesesLargo = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$mesNombreCorto = $meses[$mes] ?? $mes;
$mesNombreLargo = $mesesLargo[$mes] ?? $mes;

// Obtener inmuebles ocupados
$whereInm = "doc_inquilino != '' AND doc_inquilino IS NOT NULL";
$params = [];
$types = "";

if ($nitFiltro !== 'TODOS' && !empty($nitFiltro)) {
    $whereInm .= " AND doc_propietario = ?";
    $params[] = $nitFiltro;
    $types .= "s";
}

$sqlInm = "SELECT * FROM proprieter WHERE $whereInm ORDER BY doc_propietario, codigo";
$stmtInm = $conn->prepare($sqlInm);
if (!empty($params)) {
    $stmtInm->bind_param($types, ...$params);
}
$stmtInm->execute();
$resInm = $stmtInm->get_result();

if ($resInm->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No se encontraron inmuebles ocupados para generar cartera']);
    exit;
}

$totalGenerados = 0;
$totalOmitidos = 0;

while ($inm = $resInm->fetch_assoc()) {
    $codigoInm = $inm['codigo'];
    $nitProp = $inm['doc_propietario'];
    $nombreProp = strtoupper($inm['nombre_propietario']);
    $direccion = strtoupper($inm['direccion']);
    $valorCanonStr = str_replace(['.', ','], '', $inm['valor_canon']);
    $valorCanon = floatval($valorCanonStr);
    $comisionPct = floatval(str_replace(['%', ','], ['', '.'], $inm['comision']));
    $ivaPct = floatval($inm['iva']);

    if ($valorCanon <= 0) continue;

    // Verificar si ya existe cartera para este inmueble en este mes/año
    $sqlCheck = "SELECT COUNT(*) as total FROM cartera_propietario WHERE codigo_inmueble = ? AND mes = ? AND anio = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("isi", $codigoInm, $mes, $anio);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result()->fetch_assoc();
    
    if ($resCheck['total'] > 0) {
        $totalOmitidos++;
        continue;
    }

    // Calcular vigencia del mes
    $primerDia = "$anio-$mes-01";
    $ultimoDia = date('Y-m-t', strtotime($primerDia));
    $diaInicioFormato = date('d', strtotime($primerDia)) . '/' . $mesNombreCorto . '/' . $anio;
    $diaFinFormato = date('d', strtotime($ultimoDia)) . '/' . $mesNombreCorto . '/' . $anio;

    // Mes siguiente para el rango "desde X hasta Y"
    $mesNum = intval($mes);
    $anioSig = $anio;
    $mesSig = $mesNum + 1;
    if ($mesSig > 12) { $mesSig = 1; $anioSig++; }
    $mesSigStr = str_pad($mesSig, 2, '0', STR_PAD_LEFT);
    $mesSigNombre = $meses[$mesSigStr] ?? '';

    // Calcular valores
    $ivaArrendamiento = $valorCanon * ($ivaPct / 100);
    $comisionCanon = $valorCanon * ($comisionPct / 100);
    $ivaComision = $comisionCanon * ($ivaPct / 100);

    // 1. Canon de arrendamiento (CRÉDITO)
    $detalleCanon = "Canon de  desde {$diaInicioFormato}/{$anio} hasta {$diaFinFormato}/{$anio}  {$direccion}";
    $sqlIns = "INSERT INTO cartera_propietario (nit_propietario, nombre_tercero, fecha, mes, concepto, detalle, debito, credito, codigo_inmueble, anio, es_giro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmtIns = $conn->prepare($sqlIns);
    $conceptoCanon = "Canon de arrendamiento";
    $debitoZero = 0.00;
    $stmtIns->bind_param("ssssssddis", $nitProp, $nombreProp, $fechaGiro, $mes, $conceptoCanon, $detalleCanon, $debitoZero, $valorCanon, $codigoInm, $anio);
    $stmtIns->execute();

    // 2. IVA Arrendamiento (CRÉDITO)
    $detalleIva = "IVA ARRENDAMIENTO {$mesNombreLargo}/{$anio}  {$direccion}";
    $conceptoIva = "IVA ARRENDAMIENTO";
    $stmtIns2 = $conn->prepare($sqlIns);
    $stmtIns2->bind_param("ssssssddis", $nitProp, $nombreProp, $fechaGiro, $mes, $conceptoIva, $detalleIva, $debitoZero, $ivaArrendamiento, $codigoInm, $anio);
    $stmtIns2->execute();

    // 3. Comisión/Canon (DÉBITO)
    $detalleComi = "Comi/Canon desde {$diaInicioFormato}/{$anio} hasta {$diaFinFormato}/{$anio}  {$direccion}";
    $conceptoComi = "Comisión/Canon";
    $creditoZero = 0.00;
    $stmtIns3 = $conn->prepare($sqlIns);
    $stmtIns3->bind_param("ssssssddis", $nitProp, $nombreProp, $fechaGiro, $mes, $conceptoComi, $detalleComi, $comisionCanon, $creditoZero, $codigoInm, $anio);
    $stmtIns3->execute();

    // 4. Iva/Comisión (DÉBITO)
    $detalleIvaCom = "Iva/Comision desde {$diaInicioFormato}/{$anio} hasta {$diaFinFormato}/{$anio}  {$direccion}";
    $conceptoIvaCom = "Iva/Comisión";
    $stmtIns4 = $conn->prepare($sqlIns);
    $stmtIns4->bind_param("ssssssddis", $nitProp, $nombreProp, $fechaGiro, $mes, $conceptoIvaCom, $detalleIvaCom, $ivaComision, $creditoZero, $codigoInm, $anio);
    $stmtIns4->execute();

    // 5. Giro Renta (DÉBITO = neto a pagar)
    $giroNeto = ($valorCanon + $ivaArrendamiento) - $comisionCanon - $ivaComision;
    $detalleGiro = "Giro Renta  " . substr($nombreProp, 0, 1) . " " . $nombreProp;
    $conceptoGiro = "Giro Renta";
    $sqlGiro = "INSERT INTO cartera_propietario (nit_propietario, nombre_tercero, fecha, mes, concepto, detalle, debito, credito, codigo_inmueble, anio, es_giro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmtGiro = $conn->prepare($sqlGiro);
    $stmtGiro->bind_param("ssssssddis", $nitProp, $nombreProp, $fechaGiro, $mes, $conceptoGiro, $detalleGiro, $giroNeto, $creditoZero, $codigoInm, $anio);
    $stmtGiro->execute();

    $totalGenerados++;
}

$mensaje = "Cartera generada: $totalGenerados inmuebles procesados.";
if ($totalOmitidos > 0) {
    $mensaje .= " $totalOmitidos inmuebles omitidos (ya tenían cartera para ese período).";
}

echo json_encode(['success' => true, 'message' => $mensaje, 'generados' => $totalGenerados, 'omitidos' => $totalOmitidos]);
