<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if (empty($_SESSION['rol']) || !in_array($_SESSION['rol'], [1, 2, 3, 4])) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

require_once __DIR__ . "/../conexion.php";

$code = trim($_POST['code'] ?? '');
$code_concept = trim($_POST['code_concept'] ?? '');
$created_by = $_SESSION['username'];

if (empty($code) || empty($code_concept)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

if (!ctype_digit($code)) {
    echo json_encode(['success' => false, 'message' => 'El código debe ser numérico']);
    exit;
}

$code_concept = mb_strtoupper($code_concept, 'UTF-8');

$check = $conn->prepare("SELECT id FROM reparation_concepts WHERE code = ?");
if (!$check) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $conn->error]);
    exit;
}
$check->bind_param("s", $code);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    echo json_encode(['success' => false, 'message' => 'El código ya existe']);
    exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO reparation_concepts (code, code_concept, created_by, creation_date) VALUES (?, ?, ?, NOW())");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $conn->error]);
    exit;
}
$stmt->bind_param("sss", $code, $code_concept, $created_by);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Concepto guardado correctamente', 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $conn->error]);
}
$stmt->close();
