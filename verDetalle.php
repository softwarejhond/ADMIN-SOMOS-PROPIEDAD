<!DOCTYPE html>
<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Inicializar variables
$new_password = "";
$new_password_err = "";
$confirm_password_err = "";
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
$usaurio = htmlspecialchars($_SESSION["username"]);


?>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/estilo.css?v=0.0.1">
    <link rel="stylesheet" href="css/slidebar.css?v=0.0.2">
    <link rel="stylesheet" href="css/contadores.css?v=0.7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>SIVP - Admin</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">

</head>

<?php include("header.php"); ?>
<?php include("slidebar.php"); ?>
<?php include("modals/nuevoTipoPropiedad.php"); ?>
<?php include("modals/nuevoUsuarioAdministrador.php"); ?>
<?php include("modals/nuevoReparador.php"); ?>
<?php include("modals/actualizarIPC.php"); ?>
<?php include("modals/actualizarIPC_locales.php"); ?>

<div id="mt-3 ">
    <div class="mt-3 ">
        <br><br>
        <div id="dashboard">
            <div class="position-relative">
                <h2><i class="bi bi-buildings-fill"></i> Detalles de la propiedad</h2>

                <?php include("controller/notificacioRetiroInquilino.php"); ?>

                <hr>
                <div class="row bg-transparent">

                    <div class="">
                        <div class="container">

                            <?php include("controller/propiedades/detalles.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Toast -->


<?php include("controller/botonFlotanteDerecho.php"); ?>
<?php include("sliderBarBotton.php"); ?>
<?php include("footer.php"); ?>

<script src="js/real-time-inquilino-proximo-retiro.js?v=0.1"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastPas = document.getElementById('toastPas');
        if (toastPas) { // Verifica que toastPas no sea null
            if (toastPas.style.display === 'block') {
                const toastBootstrap = new bootstrap.Toast(toastPas);
                toastBootstrap.show();
            }
        } else {
            console.error("No se encontró el elemento con el ID 'toastPas'.");
        }
    });
</script>
</body>

</html>