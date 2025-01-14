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
include("conexion.php");
if (isset($_GET['id']) && isset($_GET['tabla'])) {
    $id = $_GET['id'];
    $tabla = $_GET['tabla'];

    // Obtener los datos del registro
    $registro = obtenerRegistroPorId($tabla, $id);

    if (!$registro) {
        echo "<div class='alert alert-danger'>No se encontró el registro.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>No se proporcionó el ID o la tabla.</div>";
    exit;
}

// Procesar la actualización del registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'actualizarRegistro') {

      // Variables del formulario
            $codigo = $_POST['codigo'];
            $tipoInmueble = $_POST['tipoInmueble'];
            $nivel_piso = $_POST['nivel_piso'];
            $area = $_POST['area'];
            $estrato = $_POST['estrato'];
            $departamento = $_POST['departamento'];
            $municipios = $_POST['municipios'];
            $terraza = $_POST['terraza'];
            $ascensor = $_POST['ascensor'];
            $patio = $_POST['patio'];
            $parqueadero = $_POST['parqueadero'];
            $cuarto_util = $_POST['cuarto_util'];
            $habitaciones = $_POST['habitaciones'];
            $closet = $_POST['closet'];
            $sala = $_POST['sala'];
            $sala_comedor = $_POST['sala_comedor'];
            $comedor = $_POST['comedor'];
            $cocina = $_POST['cocina'];
            $servicios = $_POST['servicios'];
            $cuartoServicios = $_POST['CuartoServicios'];
            $zonaRopa = $_POST['ZonaRopa'];
            $vista = $_POST['vista'];
            $servicios_publicos = $_POST['servicios_selected']; // Array de servicios seleccionados
            $otras_caracteristicas = $_POST['otras_caracteristicas'];
            $direccion = $_POST['direccion'];
            $latitud = $_POST['latitud'];
            $longitud = $_POST['longitud'];
            $telefonoInmueble = $_POST['TelefonoInmueble'];
            $valor_canon = $_POST['valor_canon'];
            $doc_propietario = $_POST['doc_propietario'];
            $nombre_propietario = $_POST['nombre_propietario'];
            $telefono_propietario = $_POST['telefono_propietario'];
            $email_propietario = $_POST['email_propietario'];
            $banco = $_POST['banco'];
            $tipoCuenta = $_POST['tipoCuenta'];
            $numeroCuenta = $_POST['numeroCuenta'];
            $diaPago = $_POST['diaPago'];
            $fecha = $_POST['fecha'];
            $contrato_EPM = $_POST['contrato_EPM'];
            $condicion = $_POST['condicion'];
            // Foto principal
            $ruta1 = '';
            if (isset($_FILES['url_foto_principal']) && $_FILES['url_foto_principal']['error'] == 0) {
                $ruta1 =  basename($_FILES['url_foto_principal']['name']);
                move_uploaded_file($_FILES['url_foto_principal']['tmp_name'], 'fotos/'.$ruta1);
            }
         // Validación simple
    $validarCampos = ['codigo', 'tabla'];
    foreach ($validarCampos as $campo) {
        if (empty($_POST[$campo])) {
            echo '<div class="alert alert-danger">Faltan campos obligatorios.</div>';
            exit;
        }
    }
        $queryUpdate = "UPDATE proprieter SET 
            tipoInmueble = '$tipoInmueble', nivel_piso = '$nivel_piso', area = '$area', estrato = '$estrato', departamento = '$departamento',
            Municipio = '$municipios', terraza = '$terraza', ascensor = '$ascensor', patio = '$patio', parqueadero = '$parqueadero',
            cuarto_util = '$cuarto_util', alcobas = '$habitaciones', closet = '$closet', sala = '$sala', sala_comedor = '$sala_comedor',
            comedor = '$comedor', cocina = '$cocina', servicios = '$servicios', CuartoServicios = '$cuartoServicios', ZonaRopa = '$zonaRopa',
            vista = '$vista', servicios_publicos = '$servicios_publicos', otras_caracteristicas = '$otras_caracteristicas', direccion = '$direccion',
            latitud = '$latitud', longitud = '$longitud', TelefonoInmueble = '$telefonoInmueble', valor_canon = '$valor_canon',
            doc_propietario = '$doc_propietario', nombre_propietario = '$nombre_propietario', telefono_propietario = '$telefono_propietario',
            email_propietario = '$email_propietario', banco = '$banco', tipoCuenta = '$tipoCuenta', numeroCuenta = '$numeroCuenta',
            diaPago = '$diaPago', fecha = '$fecha', contrato_EPM = '$contrato_EPM',estadoPropietario = '$condicion', 
            url_foto_principal = '$ruta1'
            WHERE codigo = '$codigo'
            ";
        if ($conn->query($queryUpdate) === TRUE) {
          echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast('success', 'Registro actualizado.');
                    });
                  </script>";
                header("Location: index.php");
                 exit;
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast('error', 'Error: " . $conn->error . "');
                    });
                  </script>";
        }

}
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
        <?php include("modals/actualizarIPC.php"); ?>
      <?php include("modals/actualizarIPC_locales.php"); ?>
    <div id="mt-3">
        <div class="mt-3">
            <br><br>
            <div id="dashboard">
                <div class="position-relative">
                    <h2 class="position-absolute top-0 start-0 "><i class="bi bi-pencil-square"></i> Editar propiedad</h2>

                    <?php include("controller/notificacioRetiroInquilino.php"); ?>

                </div>
                <h6 class="text-aling-rigth"></h6>
                <hr>

                <div class="row">

                    <div class="col col-sm-12 col-md-12 col-lg-12">
                        <div class="p-3">

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 px-2 mt-1">

                                   <?php include("controller/editarPropiedadForm.php"); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php include("controller/botonFlotanteDerecho.php"); ?>
              <?php include("sliderBarBotton.php"); ?>
        </div>
           <?php include("footer.php"); ?>
        <script type="text/javascript" src="js/departamentos_municipios_barrios.js?v=2"></script>
        <script>
            $('#link-dashboard').addClass('pagina-activa');
        </script>
        <script src="js/real-time-inquilino-proximo-retiro.js?v=0.1"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

</body>

</html>