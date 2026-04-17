<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Solo roles administrativos (1, 2, 3, 4)
if (!in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

require_once __DIR__ . "/../conexion.php";

$nit = $_POST['nit_propietario'] ?? '';
$nombre = $_POST['nombre_tercero'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$mes = $_POST['mes'] ?? '';
$concepto = $_POST['concepto'] ?? '';
$detalle = $_POST['detalle'] ?? '';
$debito = floatval($_POST['debito'] ?? 0);
$credito = floatval($_POST['credito'] ?? 0);
$codigo = intval($_POST['codigo_inmueble'] ?? 0);
$anio = intval($_POST['anio'] ?? date('Y'));
$esGiro = isset($_POST['es_giro']) ? 1 : 0;

if (empty($nit) || empty($fecha) || empty($mes) || empty($detalle) || $codigo <= 0) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

$sql = "INSERT INTO cartera_propietario (nit_propietario, nombre_tercero, fecha, mes, concepto, detalle, debito, credito, codigo_inmueble, anio, es_giro) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssddiis", $nit, $nombre, $fecha, $mes, $concepto, $detalle, $debito, $credito, $codigo, $anio, $esGiro);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Movimiento guardado correctamente', 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $conn->error]);
}
