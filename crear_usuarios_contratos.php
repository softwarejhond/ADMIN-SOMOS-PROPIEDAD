<?php
/**
 * Script de uso único: crea usuarios en la tabla `users`
 * a partir de los propietarios registrados en `contratos_somos_propiedad`.
 *
 * - username  = cedula_propietario (int)
 * - password  = password_hash(cedula_propietario, PASSWORD_BCRYPT)
 * - rol       = 6 (propietario)
 * - Usa INSERT IGNORE para no duplicar si el usuario ya existe.
 */

// ── Seguridad mínima: solo ejecutar desde CLI o con clave ──────────────────
if (php_sapi_name() !== 'cli') {
    $clave = $_GET['clave'] ?? '';
    if ($clave !== 'somos2026') {
        http_response_code(403);
        die('Acceso denegado. Agrega ?clave=somos2026 a la URL.');
    }
}

require_once __DIR__ . '/conexion.php';

// ── 1. Obtener propietarios únicos ─────────────────────────────────────────
$sql = "SELECT DISTINCT cedula_propietario, propietario
        FROM contratos_somos_propiedad
        WHERE cedula_propietario IS NOT NULL
          AND cedula_propietario <> ''";

$resultado = $conn->query($sql);
if (!$resultado) {
    die("Error al consultar contratos: " . $conn->error);
}

$hoy         = date('d/m/Y');
$insertados  = 0;
$omitidos    = 0;
$errores     = [];

// ── 2. Preparar INSERT IGNORE ──────────────────────────────────────────────
$stmtInsert = $conn->prepare(
    "INSERT IGNORE INTO users
        (username, password, nombre, rol, foto, orden,
         fechaCreacionUser, email, genero, telefono, direccion, edad)
     VALUES (?, ?, ?, 6, 'default.png', 0, ?, '', 'No especificado', '', '', 0)"
);
if (!$stmtInsert) {
    die("Error preparando sentencia: " . $conn->error);
}

// ── 3. Procesar cada propietario ───────────────────────────────────────────
while ($fila = $resultado->fetch_assoc()) {
    $cedulaRaw = trim($fila['cedula_propietario']);
    $nombre    = trim($fila['propietario']) ?: 'Sin nombre';

    // username es int(11) → la cédula debe ser numérica
    if (!is_numeric($cedulaRaw)) {
        $omitidos++;
        $errores[] = "Cédula no numérica omitida: \"{$cedulaRaw}\" ({$nombre})";
        continue;
    }

    $username = (int) $cedulaRaw;
    $password = password_hash($cedulaRaw, PASSWORD_BCRYPT);

    $stmtInsert->bind_param('isss', $username, $password, $nombre, $hoy);

    if ($stmtInsert->execute()) {
        if ($stmtInsert->affected_rows > 0) {
            $insertados++;
        } else {
            $omitidos++; // ya existía (IGNORE)
        }
    } else {
        $errores[] = "Error al insertar cédula {$cedulaRaw}: " . $stmtInsert->error;
    }
}

$stmtInsert->close();
$conn->close();

// ── 4. Mostrar resultado ───────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear usuarios - Somos Propiedad</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; }
        h2   { color: #2c7a2c; }
        .ok  { background: #d4edda; border: 1px solid #c3e6cb; padding: 12px; border-radius: 6px; }
        .err { background: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 6px; margin-top: 16px; }
        ul   { margin: 8px 0; padding-left: 20px; }
        li   { font-size: 0.9em; }
    </style>
</head>
<body>
<h2>Creacion de usuarios desde contratos_somos_propiedad</h2>
<div class="ok">
    <strong>Usuarios creados:</strong> <?= $insertados ?><br>
    <strong>Omitidos (ya existian o cedula no numerica):</strong> <?= $omitidos ?>
</div>
<?php if (!empty($errores)): ?>
<div class="err">
    <strong>Advertencias / errores:</strong>
    <ul>
        <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
</body>
</html>
