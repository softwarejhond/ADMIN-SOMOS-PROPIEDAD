<?php
require_once "conexion.php";

// Función para verificar la disponibilidad de la cita
function verificarDisponibilidad($fecha, $hora, $conn)
{
    $sql = "SELECT * FROM citas WHERE fecha = '$fecha' AND hora = '$hora'";
    $result = $conn->query($sql);
    return $result->num_rows == 0; // Devuelve true si no hay citas en esa fecha y hora
}

// Procesar el formulario de reserva de cita
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipoCita = $_POST["tipoCita"];
    $nombre = $_POST["nombre"];
    $telefono = $_POST["telefono"];
    $propiedad = $_POST["propiedad"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];

    // Verificar la disponibilidad de la cita
    if (verificarDisponibilidad($fecha, $hora, $conn)) {
        // Insertar la cita en la base de datos si está disponible
        $sql = "INSERT INTO citas (tipoCita, nombre, codigoPropiedad, telefono, fecha, hora, estado) VALUES ('$tipoCita', '$nombre', '$propiedad','$telefono', '$fecha', '$hora', 0)";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("Cita programada con éxito.");</script>';
        } else {
            echo "Error al reservar la cita: " . $conn->error;
        }
    } else {
        // Mostrar mensaje de error si la cita ya existe
        echo '<script>alert("¡Error! Ya existe una cita programada para esa fecha y hora.");</script>';
    }
}
