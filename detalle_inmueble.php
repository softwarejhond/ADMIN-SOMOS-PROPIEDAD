<?php
session_start();
// Habilitar la visualización de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);  // Mostrar todos los errores

// Incluir conexión a la base de datos
include("conexion.php");

// Obtener el código de la propiedad desde la URL
$codigo_propiedad = isset($_GET['codigo']) ? $_GET['codigo'] : null;

if (!$codigo_propiedad) {
    header('Location: index.php');
    exit();
}

// Consulta para obtener los datos de la propiedad
$codigo_propiedad = mysqli_real_escape_string($conn, $codigo_propiedad);
$query = "SELECT p.*, m.municipio 
          FROM proprieter p 
          LEFT JOIN municipios m ON p.Municipio = m.id_municipio 
          WHERE p.codigo = '$codigo_propiedad' AND p.estadoPropietario = 'ACTIVO'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $propiedad = mysqli_fetch_assoc($result);
} else {
    echo "Propiedad no encontrada";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/estilo.css?v=0.0.2">
    <link rel="stylesheet" href="css/slidebar.css?v=0.0.3">
    <link rel="stylesheet" href="css/contadores.css?v=0.7">
    <link rel="stylesheet" href="css/animacion.css?v=0.15">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title><?php echo htmlspecialchars($propiedad['tipoInmueble'] . ' - ' . $propiedad['codigo']); ?> - Somos Propiedad</title>
    <link rel="icon" href="img/somosLogo.png" type="image/x-icon">

    <!-- Meta tags para compartir en redes sociales -->
    <meta property="og:title" content="<?php echo htmlspecialchars($propiedad['tipoInmueble'] . ' - ' . $propiedad['codigo']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($propiedad['direccion'] . ' - $' . number_format($propiedad['valor_canon'])); ?>">
    <meta property="og:image" content="fotos/<?php echo $propiedad['codigo']; ?>/<?php echo $propiedad['foto_principal'] ?? 'default.jpg'; ?>">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
</head>

<body class="" style="background-color:white">
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-house-door"></i> Detalles del Inmueble</h2>
                </div>

                <?php include("APIS/property_details.php"); ?>
            </div>
        </div>
    </div>

    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
    <?php include("footer.php"); ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>