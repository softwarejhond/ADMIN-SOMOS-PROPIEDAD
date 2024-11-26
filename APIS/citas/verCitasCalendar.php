<?php
require_once "conexion.php";

header('Content-Type: application/json');

// Consulta a la base de datos
$sql = "SELECT id, fecha, hora, tipoCita, nombre, codigoPropiedad, telefono, estado FROM citas";
$result = $conn->query($sql);

$citas = [];

// Procesar los resultados
while ($row = $result->fetch_assoc()) {
    // Mapear el estado a un valor legible
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

    // Añadir cada cita al array
    $citas[] = [
        'id' => $row['id'],
        'title' => $row['tipoCita'],
        'nombre' =>  $row['nombre'],
        'start' => $row['fecha'] . 'T' . $row['hora'], // Formato ISO 8601 requerido por FullCalendar
        'estado' => $estadoTexto, // Estado legible
        'propiedad' => $row['codigoPropiedad'], // Código de propiedad
        'telefono' => $row['telefono'], // Teléfono
    ];
}

// Devolver los datos en formato JSON
echo json_encode($citas);
