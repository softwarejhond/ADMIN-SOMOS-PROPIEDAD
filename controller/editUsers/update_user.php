<?php
include '../../controller/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $id       = $_POST['id']       ?? null;
    $nombre   = trim($_POST['nombre']   ?? '');
    $rol      = $_POST['rol']      ?? null;
    $email    = trim($_POST['email']    ?? '');
    $genero   = $_POST['genero']   ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $edad     = $_POST['edad']     ?? null;

    if (!$id) {
        throw new Exception('ID de usuario no proporcionado');
    }

    $sql  = "UPDATE users SET nombre = ?, rol = ?, email = ?, genero = ?, telefono = ?, direccion = ?, edad = ?";
    $types = "sissssi";
    $params = [$nombre, $rol, $email, $genero, $telefono, $direccion, $edad];

    if (!empty($_POST['password'])) {
        $passwordHashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $passwordHashed;
        $types .= "s";
    }

    $sql .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar consulta: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    } else {
        throw new Exception('Error al ejecutar: ' . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
