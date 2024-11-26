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
    <link rel="stylesheet" href="css/estilo.css?v=0.0.1">
    <link rel="stylesheet" href="css/slidebar.css?v=0.0.2">
    <link rel="stylesheet" href="css/contadores.css?v=0.7">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'dayGridMonth',
                events: 'APIS/citas/verCitasCalendar.php', // URL inicial para obtener los eventos
                eventDidMount: function(info) {
                    var estado = info.event.extendedProps.estado;
                    var colorTooltip = '';

                    // Determinar el color de fondo del tooltip según el estado
                    switch (estado) {
                        case 'Sin Atender':
                            colorTooltip = 'tooltip-warning'; // Amarillo para "Sin Atender"
                            break;
                        case 'Atendido':
                            colorTooltip = 'tooltip-success'; // Verde para "Atendido"
                            break;
                        case 'Cancelado':
                            colorTooltip = 'tooltip-danger'; // Rojo para "Cancelado"
                            break;
                        default:
                            colorTooltip = 'tooltip-info'; // Azul para otros estados
                    }

                    // Crear el tooltip con estilo
                    var tooltip = new bootstrap.Tooltip(info.el, {
                        title: `
                    <ul class="list-group" style="padding-left: 20px;">
                        <li class="list-group-item text-capitalize"><h2 class="text-left"><i class="bi bi-caret-right-fill"></i> ${info.event.title}</h2></li>
                        <li class="list-group-item text-capitalize"> <i class="bi bi-person-circle"></i> Nombre: <b>${info.event.extendedProps.name}</b></li>
                        <li class="list-group-item"> <i class="bi bi-houses-fill"></i> Propiedad: <b>${info.event.extendedProps.propiedad}</b> </li>
                        <li class="list-group-item"> <i class="bi bi-telephone-fill"></i> Teléfono: <b>${info.event.extendedProps.telefono}</b> </li>
                        <li class="list-group-item"> <i class="bi bi-question-diamond-fill"></i> Estado: <b>${estado}</b> </li>
                        
                        <b class="text-center text-white"><i class="bi bi-alarm"></i> Información en tiempo real</b>
                    </ul>
                   
                `,
                        html: true,
                        placement: 'top',
                        trigger: 'hover',
                        template: `
                    <div class="tooltip ${colorTooltip}" role="tooltip">
                        <div class="tooltip-arrow"></div>
                        <div class="tooltip-inner" style="white-space: normal; word-wrap: break-word; min-width: 150px;"></div>
                    </div>
                `
                    });

                    // Configurar el temporizador para cerrar el tooltip a los 4 segundos
                    var tooltipTimeout;
                    info.el.addEventListener('mouseenter', function() {
                        tooltipTimeout = setTimeout(function() {
                            var tooltipInstance = bootstrap.Tooltip.getInstance(info.el);
                            if (tooltipInstance) {
                                tooltipInstance.hide(); // Cerrar el tooltip
                            }
                        }, 4000); // Espera 4 segundos antes de cerrar el tooltip
                    });

                    // Cancelar el temporizador si el mouse sale antes de los 4 segundos
                    info.el.addEventListener('mouseleave', function() {
                        clearTimeout(tooltipTimeout);
                    });
                }
            });

            calendar.render();

            // Función para actualizar los eventos usando AJAX
            function updateEvents() {
                $.ajax({
                    url: 'APIS/citas/verCitasCalendar.php', // URL del archivo que devuelve los eventos
                    method: 'GET',
                    success: function(data) {
                        // Imprime la respuesta del servidor para ver qué está devolviendo
                        console.log('Respuesta del servidor:', data);

                        try {
                            // Asegurarse de que la respuesta sea un JSON válido
                            var events;
                            // Si la respuesta ya es un objeto, no es necesario hacer JSON.parse
                            if (typeof data === 'string') {
                                events = JSON.parse(data); // Si la respuesta es un string, parsearlo
                            } else {
                                events = data; // Si ya es un objeto, no lo parseamos
                            }

                            // Actualizar los eventos del calendario
                            calendar.removeAllEvents(); // Elimina los eventos actuales
                            calendar.addEventSource(events); // Agrega los nuevos eventos

                            // Re-renderizar el calendario con los eventos actualizados
                            calendar.render();
                        } catch (e) {
                            console.error('Error al analizar la respuesta JSON:', e);
                        }
                    },
                    error: function(error) {
                        console.error('Error al obtener eventos:', error);
                    }
                });
            }

            // Función para cerrar todos los tooltips antes de la actualización
            function closeTooltips() {
                var tooltips = document.querySelectorAll('.tooltip');
                tooltips.forEach(function(tooltip) {
                    var tooltipInstance = bootstrap.Tooltip.getInstance(tooltip); // Obtener instancia del tooltip
                    if (tooltipInstance) {
                        tooltipInstance.hide(); // Cerrar el tooltip
                    }
                });
            }

            // Función para actualizar los eventos cada 5 segundos sin bloquear la vista
            function autoUpdateEvents() {
                setInterval(function() {
                    // Cerrar todos los tooltips antes de la actualización
                    closeTooltips();

                    // Recargar los eventos del calendario
                    updateEvents(); // Solo recarga los eventos
                }, 5000); // Cada 5 segundos (ajustar según necesidad)
            }

            autoUpdateEvents(); // Inicia la actualización periódica
        });
    </script>





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
                                    <?php
                                    require_once "conexion.php";

                                    // Función para verificar la disponibilidad de la cita
                                    function verificarDisponibilidad($fecha, $hora, $conn)
                                    {
                                        $sql = "SELECT * FROM citas WHERE fecha = '$fecha' AND hora = '$hora'";
                                        $result = $conn->query($sql);
                                        return $result->num_rows == 0; // Devuelve true si no hay citas en esa fecha y hora
                                    }

                                    // Procesar el formulario de reserva de cita
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        $tipoCita = $_POST["tipoCita"];
                                        $nombre = $_POST["nombre"];
                                        $telefono = $_POST["telefono"];
                                        $propiedad = $_POST["propiedad"];
                                        $fecha = $_POST["fecha"];
                                        $hora = $_POST["hora"];

                                        // Verificar la disponibilidad de la cita
                                        if (verificarDisponibilidad($fecha, $hora, $conn)) {
                                            // Insertar la cita en la base de datos si está disponible
                                            $sql = "INSERT INTO citas (tipoCita, nombre, codigoPropiedad, telefono, fecha, hora, estado) VALUES ('$tipoCita', '$nombre', '$propiedad','$telefono', '$fecha', '$hora', 0)";
                                            if ($conn->query($sql) === TRUE) {
                                                echo '<script>alert("Cita programada con éxito.");</script>';
                                            } else {
                                                echo "Error al reservar la cita: " . $conn->error;
                                            }
                                        } else {
                                            // Mostrar mensaje de error si la cita ya existe
                                            echo '<script>alert("¡Error! Ya existe una cita programada para esa fecha y hora.");</script>';
                                        }
                                    }
                                    ?>

                                    <br>
                                    <div class="row">
                                        <div class="col col-lg-4 col-md-12 col-sm-12 px-2 mt-1 p-1">
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
                                        <div class="col col-lg-4 col-md-12 col-sm-12 px-2 mt-1 p-3">
                                            <h2><i class="bi bi-bookmark-plus-fill"></i> Añadir nueva cita</h2>
                                            <p>Complete este formulario para registrar una cita, tenga en cuenta que todos los campos son
                                                obligatorios.
                                            </p>
                                            <form method="post" class="was-validated"
                                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                <div class="form-group">
                                                    <label for="tipoCita">Tipo de cita:</label>
                                                    <select class="form-select mb-3 " id="tipoCita"
                                                        name="tipoCita" required>
                                                        <option value="">SELECCIONAR</option>
                                                        <option value="RECIBIR">RECIBIR</option>
                                                        <option value="ENTREGAR">ENTREGAR</option>
                                                        <option value="MOSTRAR">MOSTRAR</option>
                                                        <option value="REPARACIONES">REPARACIONES</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nombre">Código de propiedad:</label>
                                                    <input type="text" class="form-control is-invalid" id="propiedad"
                                                        name="propiedad" placeholder="Ingrese el codigo de la propiedad"
                                                        required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Nombre:</label>
                                                    <input type="text" class="form-control is-invalid" name="nombre"
                                                        placeholder="Ingrese su nombre" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Teléfono:</label>
                                                    <input type="tel" class="form-control is-invalid"
                                                        placeholder="Ingrese su teléfono" name="telefono" minlength="7"
                                                        maxlength="15" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="fecha">Fecha:</label>
                                                    <input type="date" class="form-control is-invalid" id="fecha" name="fecha"
                                                        placeholder="Ingrese la fecha de la cita" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="hora">Hora:</label>
                                                    <input type="time" class="form-control is-invalid" id="hora" name="hora"
                                                        placeholder="Ingrese la hora de la cita" required>
                                                </div>
                                                <br>
                                                <input type="submit" class="btn bg-magenta-dark text-white" value="Programar">
                                                <input type="reset" class="btn bg-indigo-dark text-white" value="Cancelar">
                                            </form>
                                        </div>

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
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"> </script>
        <script src="js/gestionAgenda.js?v=0.1"></script>

</body>

</html>