<?php
session_start();

// Verificar que solo usuarios autorizados puedan crear arrendatarios
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] == 5) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

// Función para crear directorio de arrendatarios si no existe
function crearDirectorioArrendatarios() {
    $directory = 'img/fotosUsuarios/arrendatarios/';
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    return $directory;
}

// Función para validar que la contraseña sea alfanumérica
function validarPasswordAlfanumerica($password) {
    return preg_match('/^[a-zA-Z0-9]+$/', $password) && strlen($password) >= 6;
}

// Función para procesar y renombrar la imagen
function procesarImagenArrendatario($archivo, $username) {
    $directorioDestino = crearDirectorioArrendatarios();
    
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error al subir la imagen'];
    }
    
    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($archivo['type'], $tiposPermitidos)) {
        return ['success' => false, 'message' => 'Formato de imagen no permitido. Solo JPG y PNG'];
    }
    
    if ($archivo['size'] > 6 * 1024 * 1024) { // 6MB
        return ['success' => false, 'message' => 'La imagen es demasiado grande (máximo 6MB)'];
    }
    
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreArchivo = $username . '_arrendatario.' . $extension;
    $rutaCompleta = $directorioDestino . $nombreArchivo;
    
    if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
        return ['success' => true, 'ruta' => $rutaCompleta];
    } else {
        return ['success' => false, 'message' => 'Error al guardar la imagen'];
    }
}

// Función principal para crear arrendatario
function crearNuevoArrendatario($datos, $archivo = null) {
    include("../../conexion.php");
    
    // Validar que la cédula no exista
    $username = mysqli_real_escape_string($conn, $datos['username']);
    $queryVerificar = "SELECT username FROM users WHERE username = '$username'";
    $resultadoVerificar = mysqli_query($conn, $queryVerificar);
    
    if (mysqli_num_rows($resultadoVerificar) > 0) {
        return ['success' => false, 'message' => 'Ya existe un usuario con esta cédula'];
    }
    
    // Validar contraseña alfanumérica
    $password = $datos['password'];
    if (!validarPasswordAlfanumerica($password)) {
        return ['success' => false, 'message' => 'La contraseña debe ser alfanumérica y tener al menos 6 caracteres'];
    }
    
    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Procesar imagen si se subió
    $rutaFoto = 'img/fotosUsuarios/default_user.png'; // Imagen por defecto
    if ($archivo && $archivo['size'] > 0) {
        $resultadoImagen = procesarImagenArrendatario($archivo, $username);
        if ($resultadoImagen['success']) {
            $rutaFoto = $resultadoImagen['ruta'];
        } else {
            return $resultadoImagen;
        }
    }
    
    // Preparar datos para insertar
    $nombre = mysqli_real_escape_string($conn, $datos['nombre']);
    $email = mysqli_real_escape_string($conn, $datos['email']);
    $genero = mysqli_real_escape_string($conn, $datos['genero']);
    $telefono = mysqli_real_escape_string($conn, $datos['telefono']);
    $direccion = mysqli_real_escape_string($conn, $datos['direccion']);
    $edad = intval($datos['edad']);
    $rol = 5; // Rol fijo para arrendatario
    $fechaCreacion = date('Y-m-d');
    $orden = 0; // Valor por defecto
    
    // Insertar en la base de datos
    $query = "INSERT INTO users (username, password, nombre, rol, foto, orden, fechaCreacionUser, email, genero, telefono, direccion, edad) 
              VALUES ('$username', '$passwordHash', '$nombre', '$rol', '$rutaFoto', '$orden', '$fechaCreacion', '$email', '$genero', '$telefono', '$direccion', '$edad')";
    
    if (mysqli_query($conn, $query)) {
        return [
            'success' => true, 
            'message' => "Arrendatario creado exitosamente",
            'username' => $username
        ];
    } else {
        return ['success' => false, 'message' => 'Error al guardar en la base de datos: ' . mysqli_error($conn)];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $datos = [
        'username' => $_POST['username'] ?? '',
        'nombre' => $_POST['nombre'] ?? '',
        'email' => $_POST['email'] ?? '',
        'genero' => $_POST['genero'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'edad' => $_POST['edad'] ?? 0,
        'password' => $_POST['password'] ?? ''
    ];
    
    // Validar datos básicos
    if (empty($datos['username']) || empty($datos['nombre']) || empty($datos['email']) || empty($datos['password'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
        exit;
    }
    
    // Validar confirmación de contraseña
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    if ($datos['password'] !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
        exit;
    }
    
    // Procesar archivo de imagen si existe
    $archivo = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $archivo = $_FILES['foto'];
    }
    
    // Intentar crear el arrendatario
    $resultado = crearNuevoArrendatario($datos, $archivo);
    echo json_encode($resultado);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>