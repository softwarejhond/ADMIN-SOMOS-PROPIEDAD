<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("conexion.php"); // Conexión a la base de datos

// Inicializa el mensaje vacío
$mensaje = '';
$tipo_mensaje = ''; // Para determinar el tipo de mensaje (success, error, etc.)

if (isset($_POST['crearUsuario'])) {
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $rol = $_POST['rol'];
    $foto = '';

    // Validar que las contraseñas coincidan
    if ($password !== $confirmPassword) {
        $mensaje = "Las contraseñas no coinciden";
        $tipo_mensaje = "error";
    } else {
        // Comprobar si el usuario ya existe
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $mensaje = "El usuario ya existe";
            $tipo_mensaje = "error";
        } else {
            // Manejo de la subida de foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $directorioDestino = 'img/fotosUsuarios/';
                $nombreArchivo = time() . '_' . basename($_FILES['foto']['name']); // Añade un prefijo de timestamp para evitar duplicados
                $rutaArchivo = $directorioDestino . $nombreArchivo;

                // Crear el directorio si no existe
                if (!is_dir($directorioDestino)) {
                    mkdir($directorioDestino, 0777, true);
                }

                // Intenta mover el archivo a la carpeta de destino
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaArchivo)) {
                    $foto = $rutaArchivo; // Guarda la ruta de la foto en la BD
                } else {
                    $mensaje = "Error al subir la foto";
                    $tipo_mensaje = "error";
                }
            }

            // Insertar el nuevo usuario si no hay error
            if (empty($mensaje)) {
                $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (username, password, nombre, rol, foto) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'sssis', $usuario, $passwordHashed, $nombre, $rol, $foto);

                if (mysqli_stmt_execute($stmt)) {
                    $mensaje = "Usuario creado correctamente";
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al crear el usuario: " . mysqli_error($conn);
                    $tipo_mensaje = "error";
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Mostrar el mensaje en un toast (en la interfaz)
?>

<!-- Toast Container -->
<div class="toast-container top-0 bottom-0 end-0 p-3">
    <div id="liveToast" class="toast <?php echo $tipo_mensaje === 'success' ? 'bg-lime-light' : 'bg-amber-light'; ?>" role="alert" aria-live="assertive" aria-atomic="true" style="display: <?php echo !empty($mensaje) ? 'block' : 'none'; ?>;">
        <div class="toast-header">
            <strong class="me-auto"><i class="bi bi-exclamation-square-fill"></i> <?php echo $tipo_mensaje === 'success' ? 'Éxito' : 'Error'; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?php echo $mensaje; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalNuevoAdmin" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-magenta-dark">
                <h1 class="modal-title fs-5 text-white" id="exampleModalToggleLabel">
                    <i class="fas fa-plus-circle"></i> Agregar Nuevo Usuario Administrador
                </h1>
                <button type="button" class="btn-close bg-gray-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="nuevo-tipo-propiedad">
                    <img src="img/somosLogo.png" alt="logo" width="120px">
                    <div class="box-nuevo-tipo">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="nombre" placeholder="Nombre completo" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="usuario" placeholder="Nombre de usuario" required> <!-- Cambia 'Número de identificación' a 'Nombre de usuario' -->
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="confirmPassword" placeholder="Repetir contraseña" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" name="rol" required>
                                    <option value="1">Administrador</option>
                                    <option value="2">Operario</option>
                                    <option value="3">Aprobador</option>
                                    <option value="4">Editor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label text-left">Foto de perfil</label>
                                <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
                            </div>
                            <button type="submit" name="crearUsuario" class="btn bg-magenta-dark text-white">Crear Usuario</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gray-light" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar el toast cuando haya un mensaje
    document.addEventListener('DOMContentLoaded', function() {
        const toastEl = document.getElementById('liveToast');
        if (toastEl && toastEl.style.display === 'block') {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
</script>