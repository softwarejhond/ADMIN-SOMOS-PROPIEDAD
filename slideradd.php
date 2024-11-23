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

// Obtiene el total de registros de las tablas
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
    <link rel="stylesheet" href="css/contadores.css?v=0.7">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>SIVP - Admin</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
</head>

<body>
    <?php include("header.php"); ?>
    <?php include("slidebar.php"); ?>
    <?php include("modals/nuevoTipoPropiedad.php"); ?>
    <?php include("modals/nuevoUsuarioAdministrador.php"); ?>
    <?php include("modals/nuevoReparador.php"); ?>
    <?php include("modals/nuevaReporteReparacion.php"); ?>
    <div id="mt-3">
        <div class="mt-3">
            <br><br>
            <div id="dashboard">
                <div class="position-relative">
                    <h2 class="position-absolute top-0 start-0 "><i class="bi bi-image"></i> Añadir nueva imagen</h2>

                    <?php include("controller/notificacioRetiroInquilino.php"); ?>
                </div>
                <h6 class="text-aling-rigth"></h6>
                <hr>
                <div class="row">
                    <div class="col col-sm-12 col-md-6 col-lg-6">
                        <?php
                        if (isset($_POST['guardarImg'])) {
                            // Cargar la imagen
                            $nombreimg1 = $_FILES['imagen']['name'];
                            $archivo1 = $_FILES['imagen']['tmp_name'];
                            $ruta1 = "img/carousel/" . $nombreimg1;

                            // Intentar mover el archivo a la ubicación de destino
                            if (move_uploaded_file($archivo1, $ruta1)) {
                                // Insertar la imagen en la base de datos
                                $insert = mysqli_query($conn, "INSERT INTO slider (url_image, estado) VALUES ('$nombreimg1', '0')");

                                if ($insert) {
                                    echo '
            <div class="toastPaciente" style="position: absolute; top: 0; right: 0;" data-delay="4000">
                <div class="toast-header">
                    <strong class="mr-auto"><i class="fa fa-bell" aria-hidden="true" style=color:green></i> Notificación</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toastPaciente-body alert-success">
                    <h5><b>Imagen Registrada Correctamente</b></h5>
                </div>
            </div>';
                                } else {
                                    echo '
            <div class="toast" style="position: absolute; top: 0; right: 0;" data-delay="4000">
                <div class="toast-header">
                    <strong class="mr-auto"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="color:red"></i> Notificación</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body alert-danger">
                    <h5><b>Error al insertar en la base de datos: ' . mysqli_error($conn) . '</b></h5>
                </div>
            </div>';
                                }
                            } else {
                                echo '
        <div class="toast" style="position: absolute; top: 0; right: 0;" data-delay="4000">
            <div class="toast-header">
                <strong class="mr-auto"><i class="fa fa-exclamation-triangle" aria-hidden="true" style="color:red"></i> Notificación</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body alert-danger">
                <h5><b>Error al mover el archivo. Verifica permisos de la carpeta</b></h5>
            </div>
        </div>';
                            }
                        }

                        ?>



                    </div>
                    <div class="d-flex justify-content-center align-items-center" style="height: 50vh;">
                        <div class="col col-lg-6 col-md-8 col-sm-10 px-2 mt-1 text-center">
                            <form method="post" enctype="multipart/form-data" class="was-validated">
                                <!-- Contenedor centrado para la vista previa de la imagen -->
                                <div class="d-flex justify-content-center">
                                    <img id="vistaPrevia" src="img/icons/upload.png" alt="Vista previa de la imagen" style="max-width: 50%; height: auto; margin-top: 10px;" />
                                </div>
                                <br>
                                <div class="input-group mb-3">
                                    <input type="file" name="imagen" class="btn w-100 bg-magenta-light" style="color:#ffffff;" onchange="mostrarVistaPrevia(event)" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="guardarImg" class="btn btn bg-magenta-dark text-white" value="Registrar">
                                    <input type="reset" class="btn bg-gray-light border-magenta-dark" value="Cancelar" onclick="resetVistaPrevia()" />
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                        function mostrarVistaPrevia(event) {
                            const archivo = event.target.files[0];
                            const vistaPrevia = document.getElementById('vistaPrevia');

                            if (archivo) {
                                const lector = new FileReader();
                                lector.onload = function(e) {
                                    vistaPrevia.src = e.target.result;
                                    vistaPrevia.style.display = 'block';
                                };
                                lector.readAsDataURL(archivo);
                            }
                        }

                        function resetVistaPrevia() {
                            // Cambia la imagen de vista previa a la imagen predeterminada
                            document.getElementById('vistaPrevia').src = 'img/icons/upload.png';
                        }
                    </script>
                </div>

            </div>
        </div>
        <?php include("controller/botonFlotanteDerecho.php"); ?>
        <?php include("sliderBarBotton.php"); ?>
        <?php include("footer.php"); ?>

        <script src="js/real-time-inquilino-proximo-retiro.js?v=0.2"></script>
        <script>
            $('#link-dashboard').addClass('pagina-activa');
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
        <script>
            $(document).ready(function() {
                $(".toast").toast('show');
            });
        </script>
</body>

</html>