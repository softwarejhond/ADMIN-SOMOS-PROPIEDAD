<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/conexion.php';

$query = "SELECT id_departamento, departamento FROM departamentos ORDER BY departamento";
$result = $conn->query($query);

$departamentos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departamentos[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($departamentos);
?>