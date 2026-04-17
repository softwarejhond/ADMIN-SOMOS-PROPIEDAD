<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . "/../conexion.php";

$nit = $_GET['nit'] ?? '';
if (empty($nit)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT codigo, direccion, tipoInmueble, valor_canon FROM proprieter WHERE doc_propietario = ? ORDER BY codigo";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nit);
$stmt->execute();
$result = $stmt->get_result();

$inmuebles = [];
while ($row = $result->fetch_assoc()) {
    $inmuebles[] = $row;
}

header('Content-Type: application/json');
echo json_encode($inmuebles);
