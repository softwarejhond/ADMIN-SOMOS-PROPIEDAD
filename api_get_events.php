<?php
include 'conexion.php';

$query = "SELECT id, nombre AS title, codigoPropiedad, telefono, fecha AS start, estado FROM citas";
$result = $conn->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $row['extendedProps'] = [
        'codigoPropiedad' => $row['codigoPropiedad'],
        'telefono' => $row['telefono'],
        'estado' => $row['estado']
    ];
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode($events);
