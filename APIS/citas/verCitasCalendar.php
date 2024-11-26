<?php
require_once "conexion.php";

// Definir que la respuesta será en JSON
header('Content-Type: application/json');

// Consulta para obtener las citas
$sql = "SELECT id, fecha, hora, tipoCita, nombre, codigoPropiedad, telefono, estado FROM citas";
$result = $conn->query($sql);

$citas = [];

// Mapeo de las citas
while ($row = $result->fetch_assoc()) {
    switch ($row['estado']) {
        case 0:
            $estadoTexto = 'Sin Atender';
            break;
        case 1:
            $estadoTexto = 'Atendido';
            break;
        case 2:
            $estadoTexto = 'Cancelado';
            break;
        default:
            $estadoTexto = 'Desconocido';
    }

    $citas[] = [
        'id' => $row['id'],
        'title' => $row['tipoCita'],
        'name' =>  $row['nombre'],
        'start' => $row['fecha'] . 'T' . $row['hora'], // Formato ISO 8601
        'estado' => $estadoTexto,
        'propiedad' => $row['codigoPropiedad'],
        'telefono' => $row['telefono'],
    ];
}

// Devolver las citas como JSON
echo json_encode($citas);
