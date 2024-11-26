<?php
// Verificar si se recibieron los parámetros id y estado
if (isset($_GET['id']) && isset($_GET['estado'])) {
    // Incluir el archivo de conexión a la base de datos
    require_once "conexion.php";

    // Obtener los valores de id y estado
    $id = $_GET['id'];
    $estado = $_GET['estado'];

    // Construir la consulta SQL para actualizar el estado de la cita
    $sql = "UPDATE citas SET estado = ? WHERE id = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
    $stmt->bind_param("ii", $estado, $id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // La actualización fue exitosa
        $response = array("success" => true, "message" => "El estado de la cita se actualizó correctamente.");
    } else {
        // Hubo un error al ejecutar la consulta
        $response = array("success" => false, "message" => "Error al actualizar el estado de la cita: " . $conn->error);
    }
} else {
    // No se recibieron los parámetros esperados
    $response = array("success" => false, "message" => "Faltan parámetros requeridos.");
}

// Devolver la respuesta como JSON
echo json_encode($response);