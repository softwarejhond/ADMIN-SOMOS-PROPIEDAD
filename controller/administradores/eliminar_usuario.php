<?php
// Procesar la eliminación del usuario si se recibe el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    require 'conexion.php'; // Asegúrate de que tienes la conexión a la base de datos aquí

    $username = $conn->real_escape_string($_POST['username']);

    // Eliminar usuario
    $sql = "DELETE FROM users WHERE username = '$username'";

    if ($conn->query($sql) === TRUE) {
        // Mostrar mensaje de éxito
        $message = 'Usuario eliminado con éxito.';
        $messageType = 'success';
    } else {
        // Mostrar mensaje de error
        $message = 'Error al eliminar el usuario: ' . $conn->error;
        $messageType = 'error';
    }
}
