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

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM reparation_concepts WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Concepto eliminado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
}
$stmt->close();
