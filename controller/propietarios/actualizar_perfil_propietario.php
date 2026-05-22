<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || (int)$_SESSION['rol'] !== 6) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$email     = trim($data['email']     ?? '');
$genero    = trim($data['genero']    ?? '');
$telefono  = trim($data['telefono']  ?? '');
$direccion = trim($data['direccion'] ?? '');
$edad      = (int)($data['edad']     ?? 0);
$password  = $data['password']       ?? '';

// ── Validaciones de servidor ────────────────────────────────────────────────
if ($email === '' || $genero === '' || $telefono === '' || $direccion === '' || $edad === 0 || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido.']);
    exit;
}
if ($edad < 18 || $edad > 100) {
    echo json_encode(['success' => false, 'message' => 'La edad debe estar entre 18 y 100 años.']);
    exit;
}
// Contraseña: mínimo 8 chars, 1 mayúscula, 1 número, 1 carácter especial
if (strlen($password) < 8
    || !preg_match('/[A-Z]/', $password)
    || !preg_match('/[0-9]/', $password)
    || !preg_match('/[^a-zA-Z0-9]/', $password)
) {
    echo json_encode(['success' => false, 'message' => 'La contraseña no cumple los requisitos de seguridad.']);
    exit;
}

require_once __DIR__ . '/../conexion.php';

$username     = (int)$_SESSION['username'];
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "UPDATE users SET email=?, genero=?, telefono=?, direccion=?, edad=?, password=? WHERE username=?"
);
// s s s s i s i
$stmt->bind_param('ssssisi', $email, $genero, $telefono, $direccion, $edad, $passwordHash, $username);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $stmt->error]);
}
$stmt->close();
