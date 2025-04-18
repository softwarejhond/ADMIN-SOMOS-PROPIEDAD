<?php
session_start();
// Conexión a la base de datos
include("conexion.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include("funciones.php");

$empresas = obtenerEmpresas();
$infoUsuario = obtenerInformacionUsuario(); // Obtén la información del usuario
$rol = $infoUsuario['rol'];
$usaurio = htmlspecialchars($_SESSION["username"]);

if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}

// Capturar el ID del slider desde la URL
$id_slide = intval($_GET['id']);

// Consulta SQL para obtener los datos del slider
$sql = mysqli_query($conn, "SELECT * FROM slider WHERE id='$id_slide' LIMIT 1");
if (!$sql) {
    die("Error en la consulta SQL: " . mysqli_error($conn));
}

$count = mysqli_num_rows($sql);
if ($count == 0) {
    header("location: carusel.php");
    exit;
}

$rw = mysqli_fetch_array($sql);
$titulo = $rw['titulo'];
$descripcion = $rw['descripcion'];
$texto_boton = $rw['texto_boton'];
$url_boton = $rw['url_boton'];
$estilo_boton = $rw['estilo_boton'];
$url_image = $rw['url_image'];
$orden = intval($rw['orden']);
$estado = intval($rw['estado']);
$active_config = "active";
$active_slider = "active";
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
    <?php include("modals/actualizarIPC.php"); ?>
    <?php include("modals/actualizarIPC_locales.php"); ?>
    <div id="mt-3">
        <div class="mt-3">
            <br><br>
            <div id="dashboard">
                <div class="position-relative">
                    <h2 class="position-absolute top-0 start-0 "><i class="bi bi-info-circle-fill"></i> Información del carousel</h2>

                    <?php include("controller/notificacioRetiroInquilino.php"); ?>
                </div>
                <h6 class="text-aling-rigth"></h6>
                <hr>
                <div class="row">
                    <div class="align-items-center" style="height: 50vh;">
                        <form class="" id="editar_slide">
                            <div class="container">
                                <div class="row">
                                    <div class="col col-lg-12 col-md-12 col-sm-12 px-2 mt-1 d-flex justify-content-center m-3">
                                        <img src="img/carousel/<?php echo $url_image; ?>" alt="slider" width="40%">
                                    </div>

                                    <div class="col col-lg-6 col-md-6 col-sm-12 px-2 mt-1 ">
                                        <div class="form-group">
                                            <label for="titulo" class="col-sm-3 control-label">Estado</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="titulo" name="titulo" required>
                                                    <option value="EN VENTA" <?php echo ($titulo == 'EN VENTA') ? 'selected' : ''; ?>>EN VENTA</option>
                                                    <option value="EN ALQUILER" <?php echo ($titulo == 'EN ALQUILER') ? 'selected' : ''; ?>>EN ALQUILER</option>
                                                    <option value="EN ALQUILER O VENTA" <?php echo ($titulo == 'EN ALQUILER O VENTA') ? 'selected' : ''; ?>>EN ALQUILER O VENTA</option>
                                                </select>
                                                <input type="hidden" class="form-control" id="id_slide" value="<?php echo intval($id_slide); ?>" name="id_slide">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="descripcion" class="col-sm-3 control-label">Descripción</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control " rows="5" id="descripcion" required name="descripcion"><?php echo $descripcion; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="texto_boton" class="col-sm-3 control-label">Precio</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="texto_boton" name="texto_boton" value="<?php echo $texto_boton ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-lg-6 col-md-6 col-sm-12 px-2 mt-1 ">
                                        <div class="form-group">
                                            <label for="url_boton" class="col-sm-3 control-label">URL del botón</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="url_boton" name="url_boton" value="<?php echo $url_boton; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="texto_boton" class="col-sm-3 control-label">Color del botón</label>
                                            <div class="col-sm-9">
                                                <button type="button" class="btn btn-info btn-sm">
                                                    <input type="radio" name="estilo" value="info" <?php if ($estilo_boton == "info") echo "checked"; ?>>
                                                </button>
                                                <button type="button" class="btn btn-warning btn-sm">
                                                    <input type="radio" name="estilo" value="warning" <?php if ($estilo_boton == "warning") echo "checked"; ?>>
                                                </button>
                                                <button type="button" class="btn btn-primary btn-sm">
                                                    <input type="radio" name="estilo" value="primary" <?php if ($estilo_boton == "primary") echo "checked"; ?>>
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm">
                                                    <input type="radio" name="estilo" value="success" <?php if ($estilo_boton == "success") echo "checked"; ?>>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm">
                                                    <input type="radio" name="estilo" value="danger" <?php if ($estilo_boton == "danger") echo "checked"; ?>>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="orden" class="col-sm-3 control-label">Orden</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" id="orden" name="orden" value="<?php echo $orden; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="estado" class="col-sm-3 control-label">Estado</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="estado" required name="estado">
                                                    <option value="0" <?php if ($estado == 0) echo "selected"; ?>>Inactivo</option>
                                                    <option value="1" <?php if ($estado == 1) echo "selected"; ?>>Activo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div id='loader'></div>
                                    <div class='outer_div'></div>
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <a href="carusel.php" type="button" class="btn bg-magenta-dark text-white"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-custom-class="custom-tooltip"
                                            data-bs-title="Regresar a carousel">
                                            <i class="bi bi-images"></i>
                                        </a>
                                        <button type="submit" class="btn bg-magenta-dark text-white m-1">Actualizar datos</button>
                                        <br><br><br><br><br><br>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("controller/botonFlotanteDerecho.php"); ?>
    <?php include("sliderBarBotton.php"); ?>
    <?php include("footer.php"); ?>
    <script>
        $("#editar_slide").submit(function(e) {
            $.ajax({
                url: "ajax/editar_slide.php",
                type: "POST",
                data: $("#editar_slide").serialize(),
                beforeSend: function(objeto) {
                    $("#loader").html("Cargando...");
                },
                success: function(data) {
                    $(".outer_div").html(data).fadeIn('slow');
                    $("#loader").html("");
                    $(".toast").toast('show');
                }
            });
            e.preventDefault();
        });
    </script>
    <script>
        $('#link-dashboard').addClass('pagina-activa');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
</body>
</html>