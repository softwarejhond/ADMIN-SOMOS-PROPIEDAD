<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['data' => [], 'error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . "/../conexion.php";

$sql = "SELECT id, code, code_concept, created_by, creation_date, updated_by, update_date FROM reparation_concepts ORDER BY code ASC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['data' => [], 'error' => 'Error de base de datos: ' . $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);
