<?php
require_once "conexion.php";

// Obtener la fecha y hora actual en formato MySQL
$fechaHoraActual = date('Y-m-d H:i:s');

// Consulta SQL para contar las citas pendientes y que la fecha y hora de la cita no hayan pasado
$sql = "SELECT COUNT(*) AS totalPendientes 
        FROM citas 
        WHERE estado = 0 
        AND CONCAT(fecha, ' ', hora) > '{$fechaHoraActual}'"; // Comparar fecha y hora con la actual

$resultado = mysqli_query($conn, $sql);

// Verificar si se obtuvieron resultados
if ($resultado) {
    $fila = mysqli_fetch_assoc($resultado);
    $totalPendientes = $fila['totalPendientes'];
    echo $totalPendientes;
} else {
    echo "0"; // Si no hay resultados, mostrar 0
}