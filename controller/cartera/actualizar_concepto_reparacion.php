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

$id = intval($_POST['id'] ?? 0);
$code_concept = trim($_POST['code_concept'] ?? '');
$updated_by = $_SESSION['username'];

if ($id <= 0 || empty($code_concept)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

$code_concept = mb_strtoupper($code_concept, 'UTF-8');

$stmt = $conn->prepare("UPDATE reparation_concepts SET code_concept = ?, updated_by = ?, update_date = NOW() WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $conn->error]);
    exit;
}
$stmt->bind_param("ssi", $code_concept, $updated_by, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Concepto actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
}
$stmt->close();
