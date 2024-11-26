<?php
require_once 'conexion.php';

// Verificar la conexión a la base de datos
if (!$conn) {
    die('Error de conexión: ' . mysqli_connect_error());
}

// Obtener la fecha y hora actual
$fechaHoraActual = date('Y-m-d H:i:s');

// Consultar citas desde la base de datos y ordenar por fecha y hora
$sql = "SELECT id, tipoCita, fecha, hora, nombre, telefono, codigoPropiedad, estado 
        FROM citas 
        WHERE estado=0 
        ORDER BY fecha, hora";

$result = mysqli_query($conn, $sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die('Error de consulta: ' . mysqli_error($conn));
}

// Crear un array para almacenar las citas
$citas = array();

// Iterar sobre las citas y marcar las pendientes de cambio de estado
while ($row = mysqli_fetch_assoc($result)) {
    // Verificar si la fecha y hora son válidas
    if ($row['fecha'] == '0000-00-00' || $row['hora'] == '00:00:00') {
        continue; // Saltar esta cita si la fecha o la hora son inválidas
    }

    $fechaHoraCita = $row['fecha'] . ' ' . $row['hora'];

    // Verificar si la hora de la cita ya pasó
    if ($fechaHoraActual > $fechaHoraCita) {
        // Calcular la diferencia de tiempo en minutos
        $diferenciaMinutos = strtotime($fechaHoraActual) - strtotime($fechaHoraCita);
        $diferenciaMinutos = round($diferenciaMinutos / 60); // Convertir a minutos

        // Si la diferencia es menor o igual a 10 minutos, marcar la cita para cambio de estado
        if ($diferenciaMinutos <= 10) {
            $row['estado'] = '<button class="btn bg-magenta-dark text-white p-1 w-100"><i class="fa-solid fa-arrows-rotate fa-spin"></i> 
            <br><small>Tiene 10 minutos para actualizar</small></button>';
        }
    }

    // Almacenar la cita en el array
    $citas[] = $row;
}

// Devolver las citas en formato JSON
echo json_encode($citas);
