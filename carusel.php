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
    <div id="mt-3">
        <div class="mt-3">
            <br><br>
            <div id="dashboard">
                <div class="position-relative">
                    <h2 class="position-absolute top-0 start-0 "><i class="bi bi-images"></i> Carousel</h2>

                    <?php include("controller/notificacioRetiroInquilino.php"); ?>
                    </h2>
                </div>
                <h6 class="text-aling-rigth"></h6>
                <hr>

                <div class="row">

                    <div class="col col-sm-12 col-md-12 col-lg-12">
                        <div class="p-3">

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 px-2 mt-1">
                                    <div class=" text-center">
                                        <a href='slideradd.php' class="btn bg-magenta-dark btn-lg text-white"><span class="glyphicon glyphicon-plus"></span><i class="fa-solid fa-image"></i> Agregar foto para el carousel</a>
                                    </div>
                                    <br>
                                    <div id="loader" class="text-center"> <span><img src="img/icons/ajax-loader.gif"></span></div>
                                    <div class="outer_div"></div><!-- Datos ajax Final -->

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include("footer.php"); ?>
        <script src="js/real-time-inquilino-proximo-retiro.js?v=0.2"></script>
        <script>
            $('#link-dashboard').addClass('pagina-activa');
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

        <script>
            $(document).ready(function() {
                load(1);
            });

            function load(page) {
                var parametros = {
                    "action": "ajax",
                    "page": page
                };
                $.ajax({
                    url: 'ajax/slider_ajax.php',
                    data: parametros,
                    beforeSend: function(objeto) {
                        $("#loader").html("<img src='img/icons/ajax-loader.gif'>");
                    },
                    success: function(data) {
                        $(".outer_div").html(data).fadeIn('slow');
                        $("#loader").html("");
                    }
                })
            }

            function eliminar_slide(id) {
                page = 1;
                var parametros = {
                    "action": "ajax",
                    "page": page,
                    "id": id
                };
                if (confirm('Esta acción  eliminará de forma permanente el slide \n\n Desea continuar?')) {
                    $.ajax({
                        url: 'ajax/slider_ajax.php',
                        data: parametros,
                        beforeSend: function(objeto) {
                            $("#loader").html("<img src='img/icons/ajax-loader.gif'>");
                        },
                        success: function(data) {
                            $(".outer_div").html(data).fadeIn('slow');
                            $("#loader").html("");
                        }
                    })
                }
            }
        </script>
</body>

</html>