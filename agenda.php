<?php
session_start();

ini_set('display_errors', 1);
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
    <link rel="stylesheet" href="css/estilo.css?v=0.0.4">
    <link rel="stylesheet" href="css/slidebar.css?v=0.0.2">
    <link rel="stylesheet" href="css/contadores.css?v=0.7">
    <link rel="stylesheet" href="css/dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>SIVP - Admin</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
    <style>
        .tooltip-warning .tooltip-inner {
            background-color: #e67300 !important;
            color: #fff !important;
            text-align: left;
        }

        .tooltip-success .tooltip-inner {
            background-color: #66cc00 !important;
            color: #000 !important;
            text-align: left;
        }

        .tooltip-danger .tooltip-inner {
            background-color: #ec008c !important;
            color: #fff !important;
            text-align: left;
        }

        .tooltip-info .tooltip-inner {
            background-color: #30336b !important;
            color: #fff !important;
            text-align: left;
        }

        .tooltip-inner {
            min-width: 150px;
            /* Ancho mínimo */
            max-width: 300px;
            /* Ancho máximo */
            white-space: pre-wrap;
            /* Asegura que el texto largo se ajuste en varias líneas */
            padding: 10px;

        }
    </style>
</head>

<body>
    <?php include("header.php"); ?>
    <?php include("slidebar.php"); ?>
    <?php include("modals/nuevoTipoPropiedad.php"); ?>
    <?php include("modals/nuevoUsuarioAdministrador.php"); ?>
    <?php include("modals/nuevoReparador.php"); ?>
    <?php include("modals/nuevaReporteReparacion.php"); ?>
    <?php include("modals/actualizarIPC.php"); ?>
    <?php include("modals/actualizarIPC_locales.php"); ?>
    <div id="mt-3">
        <div class="mt-3">
            <br><br>
            <div id="dashboard">
                <div class="position-relative">
                    <h2 class="position-absolute top-0 start-0 "><i class="bi bi-journal-bookmark-fill"></i> Agenda</h2>

                    <?php include("controller/notificacioRetiroInquilino.php"); ?>
                    </h2>
                </div>
                <hr>

                <div class="container-fluid rounded">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 px-2 mt-1">
                            <?php //muy importante
                            ?>
                            <div class="card border-indigo-dark shadow p-3 mb-5  rounded">
                                <div class="p-3">
                                    <?php include 'controller/microConsultas/addCita.php'; ?>
                                    <br>
                                    <div class="row">
                                        <div class="col col-lg-4 col-md-12 col-sm-12 px-2 mt-1 p-1">
                                             <!-- EL SIGUIENTE DIV CARGA EL CALENDARIO-->
                                            <div id='calendar'></div>
                                        </div>
                                        <div class="col col-lg-4 col-md-12 col-sm-12 px-2 mt-1 p-1">
                                            <div class="table-responsive">
                                                <table id="citas-table" class="display">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha_Cita</th>
                                                            <th>Hora_Cita</th>
                                                            <th>Tipo Cita</th>
                                                            <th>Nombre</th>
                                                            <th>Propiedad</th>
                                                            <th>Teléfono</th>
                                                            <th>Estado</th>
                                                            <th class="text-center"><i class="bi bi-toggles2"></i></th>
                                                            <th class="text-center"><i class="bi bi-trash3-fill"></i> </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <?php include 'controller/agenda/agendaForm.php'; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        </section>
        <!-- Incluir jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Incluir Bootstrap JS importante para los TOAST -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <!--MUY IMPORTANTE PARA EL TIEMPO REAL DE LAS CONSULTAS-->
        <script src="js/dataTables.js?v=0.1"> </script>
        <script src="js/gestionAgenda.js?v=0.1"></script>
        <script src='js/fullCalendar.js?v=0.1'></script>
        <script src="js/real-time-calendar.js?v=0.1"></script>
</body>

</html>