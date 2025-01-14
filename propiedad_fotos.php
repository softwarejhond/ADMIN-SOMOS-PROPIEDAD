<?php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

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
include("conexion.php");

// Obtener el código de la propiedad desde la URL
$codigoPropiedad = isset($_GET['codigo']) ? $_GET['codigo'] : 0;

// Verificar que el código de la propiedad es válido
if ($codigoPropiedad) {
  // Recuperar las fotos de la propiedad
  $query = "SELECT * FROM fotos WHERE codigoPropiedad = '$codigoPropiedad'";
  $result = mysqli_query($conn, $query);
  $fotos = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
  echo "Propiedad no encontrada.";
  exit;
}

// Subida de fotos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fotos'])) {
  $reporte = "";
  $file = $_FILES['fotos'];
  $id_ultima_propiedad = $codigoPropiedad;

  // Definir el directorio donde se guardarán las imágenes
  $directorio = 'fotos/' . $id_ultima_propiedad . "/";

  // Verificar si el directorio existe, si no, crearlo
  if (!file_exists($directorio)) {
    if (!mkdir($directorio, 0777, true)) {
      $reporte .= "<p style='color: red'>Error al crear el directorio $directorio.</p>";
      exit;
    }
  }

  for ($x = 0; $x < count($file["name"]); $x++) {
    $nombre = hash('ripemd160', $file["name"][$x] . time()); // Aseguramos nombre único
    $tipo = $file["type"][$x];
    $ruta_provisional = $file["tmp_name"][$x];
    $size = $file["size"][$x];

    // Verificar que el archivo es una imagen válida
    if (!in_array($tipo, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
      $reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen válida.</p>";
    } else {
      $extension = pathinfo($file["name"][$x], PATHINFO_EXTENSION);
      $nombre .= "." . $extension; // Asignar extensión correcta

      // Intentar mover el archivo
      if (move_uploaded_file($file['tmp_name'][$x], $directorio . $nombre)) {
        // Insertar la imagen en la base de datos
        $query = "INSERT INTO fotos (id, codigoPropiedad, nombre_foto) VALUES (NULL, '$id_ultima_propiedad', '$nombre')";
        if (mysqli_query($conn, $query)) {
          $reporte .= "<p style='color: green'>Foto $nombre subida correctamente.</p>";
        } else {
          $reporte .= "<p style='color: red'>No se pudo insertar la imagen $nombre en la base de datos.</p>";
        }
      } else {
        $reporte .= "<p style='color: red'>Error al mover el archivo $nombre.</p>";
      }
    }
  }
}

// Eliminar imagen
if (isset($_GET['eliminar']) && isset($_GET['id_foto'])) {
  $idFoto = $_GET['id_foto'];
  $codigoPropiedad = $_GET['codigo'];

  // Obtener el nombre de la foto desde la base de datos
  $query = "SELECT nombre_foto FROM fotos WHERE id = '$idFoto' AND codigoPropiedad = '$codigoPropiedad'";
  $result = mysqli_query($conn, $query);
  $foto = mysqli_fetch_assoc($result);

  if ($foto) {
    $rutaFoto = 'fotos/' . $codigoPropiedad . '/' . $foto['nombre_foto'];

    // Eliminar la foto del servidor
    if (file_exists($rutaFoto)) {
      unlink($rutaFoto);
    }

    // Eliminar la foto de la base de datos
    $query = "DELETE FROM fotos WHERE id = '$idFoto'";
    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Foto eliminada correctamente'); window.location.href='propiedad_fotos.php?codigo=$codigoPropiedad';</script>";
    } else {
      echo "<script>alert('Error al eliminar la foto de la base de datos'); window.location.href='propiedad_fotos.php?codigo=$codigoPropiedad';</script>";
    }
  } else {
    echo "<script>alert('Foto no encontrada'); window.location.href='propiedad_fotos.php?codigo=$codigoPropiedad';</script>";
  }
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
  <link rel="stylesheet" href="css/estilo.css?v=0.0.1">
  <link rel="stylesheet" href="css/slidebar.css?v=0.0.2">
  <link rel="stylesheet" href="css/contadores.css?v=0.7">
  <link rel="stylesheet" href="css/dataTables.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>SIVP - Admin</title>
  <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
  <style>
    .drag-area {
      width: 100%;
      height: 250px;
      border: 3px dashed #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      background-color: #f9f9f9;
      margin-bottom: 20px;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
    }

    .drag-area p {
      font-size: 18px;
      color: #333;
      text-align: center;
    }

    .drag-area.drag-over {
      border-color: #3b82f6;
      background-color: #e0f3ff;
    }

    .panel-previsualizacion {
      margin-top: 20px;
      border: 1px solid #ddd;
      padding: 15px;
      background-color: #f5f5f5;
    }

    .panel-previsualizacion h3 {
      margin-bottom: 15px;
    }

    .panel-previsualizacion .image-preview {
      display: inline-block;
      position: relative;
      margin-right: 15px;
    }

    .panel-previsualizacion .image-preview img {
      width: 100px;
      height: 100px;
      object-fit: cover;
    }

    .panel-previsualizacion .image-preview .remove-preview {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: red;
      color: white;
      border-radius: 50%;
      padding: 2px 5px;
      font-size: 12px;
      cursor: pointer;
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
    <div class="mt-3 mb-3">
      <br><br><br>
      <div class="dashboard">
        <div class="position-relative">
          <h2 class="position-absolute top-0 start-0 m-3 "><i class="bi bi-image-fill"></i> Subir fotos de la propiedad <?php echo $codigoPropiedad ? "Código $codigoPropiedad" : 'No disponible'; ?></h2>

          <?php include("controller/notificacioRetiroInquilino.php"); ?>
          </h2>
        </div>
        <h6 class="text-aling-rigth"></h6>
                <hr>
        <div class="container">
          <!-- Formulario de subida de fotos -->
          <form method="POST" enctype="multipart/form-data">
            <div class="drag-area" id="drag-area">
              <p>Arrastra y suelta las fotos aquí, o haz clic para seleccionar imágenes.</p>
              <input type="file" class="form-control" id="fotos" name="fotos[]" multiple accept="image/*" style="display:none;">
            </div>

            <!-- Panel de previsualización -->
            <div class="panel-previsualizacion" id="panel-previsualizacion" style="display:none;">
              <h3>Fotos seleccionadas:</h3>
              <div id="image-preview-panel"></div>
            </div>

            <button type="submit" class="btn bg-magenta-dark text-white"><i class="bi bi-cloud-upload-fill"></i> Subir Fotos</button>
          </form>

          <!-- Mostrar reporte de carga -->
          <?php if (isset($reporte)) echo $reporte; ?>

          <!-- Mostrar fotos subidas -->
          <div class="mt-4">
            <h3>Fotos Subidas</h3>
            <div id="image-preview">
              <?php
              if ($fotos) {
                foreach ($fotos as $foto) {
                  echo '<div class="card" style="width: 18rem; display: inline-block; margin: 10px;">
                                <img src="fotos/' . $codigoPropiedad . '/' . $foto['nombre_foto'] . '" class="card-img-top" alt="Imagen">
                                <div class="card-body">
                                    <button class="btn btn-danger btn-sm" onclick="eliminarImagen(' . $foto['id'] . ')">Eliminar</button>
                                </div>
                              </div>';
                }
              } else {
                echo "<p>No hay fotos para esta propiedad.</p>";
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
  <br>
  <?php include("controller/botonFlotanteDerecho.php"); ?>
  <?php include("sliderBarBotton.php"); ?>
  <?php include("footer.php"); ?>
  <script src="js/real-time-inquilino-proximo-retiro.js?v=0.2"></script>
  <script>
    $('#link-dashboard').addClass('pagina-activa');
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


  <script>
    // Función para mostrar las imágenes previas
    const dragArea = document.getElementById("drag-area");
    const fileInput = document.getElementById("fotos");
    const previewZone = document.getElementById("preview-images-zone");
    const panelPrevisualizacion = document.getElementById("panel-previsualizacion");
    const previewPanel = document.getElementById("image-preview-panel");

    dragArea.addEventListener("click", () => fileInput.click());
    fileInput.addEventListener("change", handleFileSelect);
    dragArea.addEventListener("dragover", (e) => e.preventDefault());
    dragArea.addEventListener("drop", handleFileDrop);

    function handleFileDrop(e) {
      e.preventDefault();
      const files = e.dataTransfer.files;
      handleFiles(files);
    }

    function handleFileSelect(e) {
      const files = e.target.files;
      handleFiles(files);
    }

    function handleFiles(files) {
      previewPanel.innerHTML = "";
      panelPrevisualizacion.style.display = "block";

      Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
          const imgElement = document.createElement("img");
          imgElement.src = event.target.result;
          imgElement.classList.add("img-preview");
          const previewDiv = document.createElement("div");
          previewDiv.classList.add("image-preview");
          previewDiv.innerHTML = `<span class="remove-preview" onclick="removePreview(${index})">&times;</span>`;
          previewDiv.appendChild(imgElement);
          previewPanel.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
      });
    }

    function removePreview(index) {
      previewPanel.removeChild(previewPanel.childNodes[index]);
    }

    function eliminarImagen(idFoto) {
      if (confirm('¿Estás seguro de que deseas eliminar esta foto?')) {
        window.location.href = "?eliminar=true&id_foto=" + idFoto + "&codigo=" + <?php echo $codigoPropiedad; ?>;
      }
    }
  </script>
</body>

</html>