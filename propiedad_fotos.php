<?php
session_start();
// Verificar si el usuario ha iniciado sesión (asegúrate de tener la misma lógica que en index.php)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}
include("funciones.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $propiedadId = $_GET['id'];
  $propiedad = obtenerPropiedadPorId($propiedadId); // Debes implementar esta función en funciones.php
  if (!$propiedad) {
    header('Location: index.php');
    exit;
  }
} else {
  $propiedadId = 0;
  $propiedad = null;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="css/estilo.css?v=0.0.2">
  <link rel="stylesheet" href="css/slidebar.css?v=0.0.3">
  <link rel="stylesheet" href="css/contadores.css?v=0.7">
  <link rel="stylesheet" href="css/animacion.css?v=0.15">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>SIVP - Fotos</title>
  <link rel="icon" href="img/somosLogo.png" type="image/x-icon">
</head>

<body>
  <?php include("header.php"); ?>
  <?php include("slidebar.php"); ?>
  <div class="container">
    <h1>Gestionar Fotos de la Propiedad: <?php if ($propiedad) echo $propiedad['nombre']; ?></h1>
    <form id="imageUploadForm">
      <div class="mb-3">
        <label for="propiedadId" class="form-label">ID de la Propiedad</label>
        <input type="text" class="form-control" id="propiedadId" value="<?php if ($propiedad) echo $propiedad['codigo']; ?>" required>
      </div>
      <div class="dropzone" id="dropzone-container">
        Arrastra y suelta las fotos aquí o haz clic para seleccionar
        <input type="file" name="images[]" id="images" multiple accept="image/*" style="display: none;">
      </div>
      <div id="image-preview"></div>
      <button type="button" class="btn btn-primary" onclick="uploadImages()">Subir Fotos</button>
    </form>

    <div id="uploaded-images-container" class="mt-4">
    </div>
  </div>
  <script src="js/dropzone.js"></script>
  <script>
    $(document).ready(function() {
      cargarImagenes();
    });

    function uploadImages() {
      var propiedadId = document.getElementById('propiedadId').value;
      const form = document.getElementById('imageUploadForm');
      const formData = new FormData(form);
      if (propiedadId) {
        fetch('controller/subir_fotos.php?action=upload&id=' + propiedadId, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.success);
              cargarImagenes();
            } else {
              alert(data.error);
            }
          }).catch(error => {
            console.log(error);
          })
      } else {
        alert('Por favor, ingrese el ID de la propiedad.');
      }
    }

    function cargarImagenes() {
      var propiedadId = document.getElementById('propiedadId').value;
      if (propiedadId) {
        fetch('controller/subir_fotos.php?action=ver&id=' + propiedadId)
          .then(response => response.json())
          .then(data => {
            const container = document.getElementById('uploaded-images-container');
            container.innerHTML = '';

            if (data.success) {
              if (data && data.imagenes.length > 0) {
                const div = document.createElement('div');
                div.classList.add('row');
                data.imagenes.forEach(imagen => {
                  const imagenDiv = document.createElement('div');
                  imagenDiv.classList.add('col-md-3', 'mb-3');
                  imagenDiv.innerHTML = `
                                                 <div class="card">
                                                     <img src="${imagen.nombre_foto}" class="card-img-top" alt="Imagen">
                                                     <div class="card-body">
                                                      <button class="btn btn-danger btn-sm" onclick="eliminarImagen(${imagen.id})">Eliminar</button>
                                                     </div>
                                               </div>
                                             `;
                  div.appendChild(imagenDiv);
                });
                container.appendChild(div);
              } else {
                container.innerHTML = '<p>No hay imagenes subidas para esta propiedad</p>';
              }
            } else {
              container.innerHTML = '<p>Error al cargar las imagenes</p>';
            }
          })
          .catch(error => {
            console.log('Error:', error);
          });
      } else {
        const container = document.getElementById('uploaded-images-container');
        container.innerHTML = '<p>Por favor, ingrese el ID de la propiedad.</p>';
      }
    }

    function eliminarImagen(id) {
      var propiedadId = document.getElementById('propiedadId').value;
      if (confirm("¿Estás seguro de que quieres eliminar esta imagen?")) {
        fetch('controller/subir_fotos.php?action=eliminar&id=' + id)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.success)
              cargarImagenes(propiedadId);
            } else {
              alert(data.error);
            }
          });
      }
    }
  </script>
  <?php include("controller/botonFlotanteDerecho.php"); ?>
  <?php include("sliderBarBotton.php"); ?>
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
  </ul>
  <?php include("footer.php"); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script type="text/javascript" src="js/departamentos_municipios_barrios.js?v=2"></script>
  <script src="js/real-time-inquilino-proximo-retiro.js?v=0.1"></script>
  <script src="js/real-time-update-contadores.js?v=0.1"></script>
  <script>
    $('#link-dashboard').addClass('pagina-activa');
  </script>
</body>

</html>