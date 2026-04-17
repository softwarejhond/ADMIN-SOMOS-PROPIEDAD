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

$id = intval($_POST['id'] ?? 0);
$fecha = $_POST['fecha'] ?? '';
$mes = $_POST['mes'] ?? '';
$concepto = $_POST['concepto'] ?? '';
$detalle = $_POST['detalle'] ?? '';
$debito = floatval($_POST['debito'] ?? 0);
$credito = floatval($_POST['credito'] ?? 0);
$anio = intval($_POST['anio'] ?? date('Y'));
$esGiro = isset($_POST['es_giro']) ? 1 : 0;

if ($id <= 0 || empty($fecha) || empty($mes) || empty($detalle)) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

$sql = "UPDATE cartera_propietario SET fecha = ?, mes = ?, concepto = ?, detalle = ?, debito = ?, credito = ?, anio = ?, es_giro = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssddiis", $fecha, $mes, $concepto, $detalle, $debito, $credito, $anio, $esGiro, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Movimiento actualizado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}
