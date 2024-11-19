<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no está logueado, redirigir a la página de inicio de sesión
    header('Location: login.php');
    exit;
}

include("funciones.php");

$empresas = obtenerEmpresas();
$infoUsuario = obtenerInformacionUsuario(); // Obtén la información del usuario
$rol = $infoUsuario['rol'];

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/estilo.css?v=0.0.1">
    <link rel="stylesheet" href="css/slidebar.css?v=0.0.2">
    <link rel="stylesheet" href="css/contadores.css?v=0.6">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>SIVP - Admin</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
</head>

<body>
    <?php include("header.php"); ?>
    <?php include("slidebar.php"); ?>
    <?php include("modals/nuevoTipoPropiedad.php"); ?>
    <?php include("modals/nuevoUsuarioAdministrador.php"); ?>

    <div id="mt-3">
        <div class="mt-3">
            <br><br>
            <div id="dashboard">
                <div class="position-relative">
                    <h2 class="position-absolute top-0 start-0 "><i class="bi bi-hdd-fill"></i> Actualizar SMTP</h2>

                    <?php include("controller/notificacioRetiroInquilino.php"); ?>

                    <hr>
                    <form id="smtpForm" action="#" method="POST" class="was-validated" enctype="multipart/form-data">
                        <input type="hidden" name="formType" value="smtpConfig">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    include 'conexion.php';
                                    $query = mysqli_query($conn, "SELECT * FROM smtpConfig WHERE id='1'");
                                    $smtpConfig = mysqli_fetch_array($query);

                                    // Uso de la función en tu código principal
                                    $mensaje = '';
                                    $tipo_mensaje = '';

                                    if (isset($_POST['actualizarSmtp'])) {
                                        $resultado = actualizarSmtpConfig($conn, $_POST, $_FILES);
                                        $mensaje = $resultado['mensaje'];
                                        $tipo_mensaje = $resultado['tipo_mensaje'];
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label>Host</label>
                                        <input type="text" name="host" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['host']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Correo electrónico</label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['email']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Contraseña</label>
                                        <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['password']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Puerto</label>
                                        <input type="number" name="port" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['port']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre de la empresa</label>
                                        <input type="text" name="nameBody" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['nameBody']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Asunto del correo</label>
                                        <input type="text" name="Subject" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['Subject']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Cuerpo del correo</label>
                                        <textarea name="body" class="form-control" required><?php echo htmlspecialchars($smtpConfig['body']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre para el archivo adjunto</label>
                                        <input type="text" name="nameFile" class="form-control" value="<?php echo htmlspecialchars($smtpConfig['nameFile']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Imagen para el cuerpo del correo</label>
                                        <input type="file" name="imagen" class="form-control-file" accept="image/*">
                                        <?php if ($smtpConfig['urlpicture']) : ?>
                                            <img id="currentImage" src="<?php echo htmlspecialchars($smtpConfig['urlpicture']); ?>" width="100px" style="display: block; margin-top: 10px;">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Logo para el encabezado del PDF</label>
                                        <input type="file" name="logo" class="form-control-file" accept="image/*">
                                        <?php if ($smtpConfig['logoEncabezado']) : ?>
                                            <img id="currentLogo" src="<?php echo htmlspecialchars($smtpConfig['logoEncabezado']); ?>" width="100px" style="display: block; margin-top: 10px;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="submit" class="btn bg-magenta-dark text-white" value="Actualizar SMTP" name="actualizarSmtp">
                                        <a class="btn btn-outline-secondary" href="actualizar-smtp.php">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php include("footer.php"); ?>
                    <script src="js/real-time-inquilino-proximo-retiro.js?v=0.2"></script>
                    <!-- Toast Container -->
                    <div class="toast-container top-0 end-0 p-3">
                        <div id="liveToastSmtp" class="toast <?php echo $tipo_mensaje === 'success' ? 'bg-lime-light' : 'bg-amber-light'; ?>" role="alert" aria-live="assertive" aria-atomic="true" style="display: <?php echo !empty($mensaje) ? 'block' : 'none'; ?>;">
                            <div class="toast-header">
                                <strong class="me-auto"><i class="bi bi-exclamation-square-fill"></i> <?php echo $tipo_mensaje === 'success' ? 'Éxito' : 'Error'; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                <?php echo $mensaje; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar el toast
        var toastTrigger = document.getElementById('liveToastSmtp')
        if (toastTrigger) {
            var toastBootstrap = new bootstrap.Toast(toastTrigger)
            toastBootstrap.show()
        }
    </script>
</body>

</html>