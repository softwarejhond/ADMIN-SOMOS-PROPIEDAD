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
    <link rel="stylesheet" href="css/contadores.css?v=0.6">
    <title>SIVP - Admin</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
</head>

<?php include("header.php"); ?>
<?php include("slidebar.php"); ?>
<?php include("modals/nuevoTipoPropiedad.php"); ?>
<?php include("modals/nuevoUsuarioAdministrador.php"); ?>


<div id="mt-3">
    <div class="mt-3">
        <br><br>
        <div id="dashboard">
            <div class="position-relative">
                <h2 class=position-absolute top-0 start-0 translate-middle""><i class="bi bi-calendar2-week-fill"></i> Programar retiro</h2>

                <?php include("controller/notificacioRetiroInquilino.php"); ?>

                <hr>

                <div class="card border-magenta-dark shadow p-3 mb-5 bg-white rounded">


                    <div class="p-3">
                        <div class="row">
                            <div class="col col-lg-12 col-md-12 col-sm-12 px-2 mt-1">
                                <div class="card text-center">
                                    <div class="card-header bg-magenta-dark text-white">
                                        <i class="fas fa-home"></i> BUSCAR PROPIEDAD <i class="fas fa-home"></i>
                                    </div>
                                    <!-- Mostrar la imagen solo si no se ha hecho una búsqueda -->
                                    <div class="col col-lg-12 col-md-12 col-sm-12 px-2 mt-1 d-flex justify-content-center m-3">
                                        <?php if (!isset($_GET['search'])): ?>
                                            <img src="img/icons/retiro.png" alt="Imagen por defecto" class="mt-3">
                                        <?php endif; ?>
                                    </div>
                                    <form action="" method="GET">
                                        <div class="input-group  mb-3">
                                            <input type="number" name="search" required value="<?php if (isset($_GET['search'])) {
                                                                                                    echo $_GET['search'];
                                                                                                } ?>" class="form-control text-center " placeholder="CODIGO DE LA PROPIEDAD"><br>
                                            <button type="submit" class="btn bg-magenta-dark text-white" title="Buscar propiedad"><i class="bi bi-search"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col col-lg-12 col-md-12 col-sm-12 px-2 mt-1">
                                <form method="POST">
                                    <?php
                                    //AQUI INICIA EL PROCESO PARA BUSCAR LA PROPIEDAD, EL PROCESO ES SECUENCIAL, ES DECIR PRIMERO BUSCO LA PROPIEDAD Y LUEGO PUEDO SABER 
                                    //CUENTA CON INQUILINO PARA RETIRARLO

                                    $fecha_actual = date('Y-m-d');
                                    if (isset($_GET['search'])) {
                                        $filtervalues = $_GET['search'];
                                        $query = "SELECT * FROM proprieter WHERE codigo LIKE '%$filtervalues%' LIMIT 1 ";
                                        $query_run = mysqli_query($conn, $query);

                                        if (mysqli_num_rows($query_run) > 0) {
                                            foreach ($query_run as $items) {
                                                $codigo = $items["codigo"];
                                                $propietario = $items["nombre_propietario"];
                                                $docInquilinos = $items["doc_inquilino"];
                                                $nombreInquilinos = $items["nombre_inquilino"];
                                                $telefonoInquilinos = $items["telefono_inquilino"];
                                                $emailInquilinos = $items["email_inquilino"];


                                                echo '
                 
                                                <div class="card" style="border:0; color:#ec008c;   border: 2px solid #ec008c;">
                                                  <div class="row">
                                                     <div class="col col-md-12 col-sm-12 col-lg-6 text-center">
                                                        <img src="' . $items['foto1'] . '" alt="avatar" style="width:400px; height:400px"> 
                                                     </div>
                                                     <div class="col col-md-12 col-sm-12 col-lg-6">
                                                       <div class="card-body text-left">
                                                       <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><h3><b>Código propiedad: ' . $codigo . '</b></h3></li>
                                                        <li class="list-group-item"><h6>Propietario: ' . $propietario . '</h6></li>
                                                        <li class="list-group-item"><h6>Identificación inquilino: ' . $docInquilinos . '</h6></li>
                                                        <li class="list-group-item"><h6>Nombre inquilino: ' . $nombreInquilinos . '</h6></li>
                                                        <li class="list-group-item"><h6>Teléfono inquilino: ' . $telefonoInquilinos . '</h6></li>
                                                        <li class="list-group-item"><h6>Email inquilino: ' . $emailInquilinos . '</h6></li>
                                                            <h5 class="text-center">Acciones a realizar</h5>
                                                         <label class="text-center">Selecciona la futura fecha de retiro del inquilino</label>
                                                        <li class="list-group-item"><input type="date" name="fechaRetiro" class="form-control text-center" min="' . $fecha_actual . '"></li>
                                                        <li class="list-group-item"><button type="submit" name="btnAddRetido"  title="Retirar Inquilino" onclick="return confirm(\'Esta seguro de retirar el inquilino ' . $nombreInquilinos . '?\')" class="btn bg-magenta-dark w-100"><i class="fa-solid fa-floppy-disk"></i> REGISTRAR LOS DATOS PARA PROGRAMAR EL RETIRO DEL INQUILINO</button></li>
                                                        <li class="list-group-item"><h6>Fecha actual para el registro: ' . $fecha_actual . '</h6></li>
                                                       </ul>
                                                       </div>
                                                    </div>
                                                 </div>
                                              ';
                                    ?>
                                </form>

                            <?php
                                            }
                                        } else {
                            ?>

                            <tr>
                                <td colspan="4">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    Propiedad no encontrada
                                </td>
                            </tr>
                    <?php
                                        }
                                    }
                    ?>

                            </div>
                        </div>
                    </div>

                    <?php
                    //AQUI INICIA EL PROCESO PARA GUARDAR LOS DATOS DENTRO DE LAS VARIABLES DESPUES DE HABERLAS BUSCADO
                    if (isset($_POST['btnAddRetido'])) {

                        $codigoPropiedad = $codigo; //Escanpando caracteres  
                        $IdInquilino = $docInquilinos; //Escanpando caracteres 
                        $NombreInquilino = $nombreInquilinos; //Escanpando caracteres 
                        $telefonoInquilino = $telefonoInquilinos; //Escanpando caracteres 
                        $emailInquilino = $emailInquilinos; //Escanpando caracteres 
                        $fechaRetiro = mysqli_real_escape_string($conn, (strip_tags($_POST["fechaRetiro"], ENT_QUOTES))); //Escanpando caracteres desde el formulario, este campo no lo traemos de la base de datos 
                        $fechaRegistro = date('Y-m-d H:i:s'); // fecha actual para saber el momento y hora en que se realizo

                        // Crear un arreglo de caracteres alfanuméricos
                        $caracteres = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
                        // Mezclar el arreglo
                        shuffle($caracteres);
                        // Seleccionar los primeros 8 caracteres (o la longitud que desees)
                        $codigoAleatorio = array_slice($caracteres, 0, 8);
                        // Convertir el arreglo en una cadena
                        $codigoAleatorioString = implode("", $codigoAleatorio);

                        $registro = $codigoAleatorioString;
                        $insert = mysqli_query($conn, "INSERT INTO retiredTenants(codigoPropiedad, IdInquilino, NombreInquilino,telefonoInquilino,emailInquilino,fechaRetiro,fechaRegistro,registro) VALUES 
                                    ('$codigoPropiedad','$IdInquilino','$NombreInquilino','$telefonoInquilino','$emailInquilino','$fechaRetiro','$fechaRegistro','$registro')");
                        if ($insert) {

                            echo '
                   <div class="toast toastRetiro align-items-center border-0 position-fixed top-0 end-0 p-3 " data-bs-delay="4000" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1050;">
    <div class="toast-header border-lime-dark">
        <i class="fa fa-bell me-2" aria-hidden="true"></i>
        <strong class="me-auto"><i class="bi bi-exclamation-triangle-fill"></i> Notificación</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-lime-light">
        <b>La fecha del futuro retiro fue registrada con éxito.</b>
    </div>
</div>


                                   ';
                        } else {
                            echo '          <div class="toast toastRetiro align-items-center border-0 position-fixed top-0 end-0 p-3 " data-bs-delay="4000" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1050;">
    <div class="toast-header border-magenta-light">
        <i class="fa fa-bell me-2" aria-hidden="true"></i>
        <strong class="me-auto"><i class="bi bi-exclamation-triangle-fill"></i> Notificación</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-magenta-light">
        <b>Error en el registro del futuro retiro, inteta de nuevo.</b>
    </div>
</div>
                                   ';
                        }
                    }
                    //FINALIZA LA INSTRUCCIÓN DE GUARDAR LOS DATOS EN LA TABLA
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast -->
</div>


<?php include("footer.php"); ?>
<script src="js/real-time-inquilino-proximo-retiro.js?v=0.2"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script>
    $(document).ready(function() {
        $(".toastRetiro").toast('show');
    });
</script>
</body>

</html>