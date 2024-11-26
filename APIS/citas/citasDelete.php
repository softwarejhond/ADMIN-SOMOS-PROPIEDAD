<?php
require_once 'conexion.php';
// Verifica si se ha enviado un ID válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Incluye el archivo de conexión a la base de datos
    require_once "conexion.php";

    // Prepara la declaración SQL para eliminar el registro
    $stmt = $conn->prepare("DELETE FROM citas WHERE id = ?");

    // Vincula los parámetros
    $stmt->bind_param("i", $_GET['id']);

    // Ejecuta la declaración
    if ($stmt->execute()) {
        // Si la eliminación fue exitosa, responde con un mensaje de éxito
        echo json_encode(array("message" => "Cita eliminada correctamente."));
    } else {
        // Si hubo un error, responde con un mensaje de error
        echo json_encode(array("message" => "Error al intentar eliminar la cita."));
    }

} else {
    // Si no se proporcionó un ID válido, responde con un mensaje de error
    echo json_encode(array("message" => "ID de cita no válido."));
}