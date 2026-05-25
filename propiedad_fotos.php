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

// Verificar acceso hermético
verificarAccesoHermetico();

$empresas = obtenerEmpresas();
$infoUsuario = obtenerInformacionUsuario(); // Obtén la información del usuario
$rol = $infoUsuario['rol'];
include("conexion.php");

// Obtener el código de la propiedad desde la URL
$codigoPropiedad = isset($_GET['codigo']) ? $_GET['codigo'] : 0;

// Verificar que el código de la propiedad es válido
if ($codigoPropiedad) {
  // Recuperar las fotos de la propiedad ordenadas
  $query = "SELECT * FROM fotos WHERE codigoPropiedad = '$codigoPropiedad' ORDER BY orden ASC, id ASC";
  $result = mysqli_query($conn, $query);
  $fotos = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
  echo "Propiedad no encontrada.";
  exit;
}

// Guardar orden de fotos (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['formType']) && $_POST['formType'] === 'ordenarFotos') {
  header('Content-Type: application/json');
  $ids = json_decode($_POST['ids'] ?? '[]', true);
  if (!is_array($ids) || empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'Sin datos']);
    exit;
  }
  $ok = true;
  foreach ($ids as $orden => $id) {
    $id    = intval($id);
    $orden = intval($orden) + 1;
    if (!mysqli_query($conn, "UPDATE fotos SET orden=$orden WHERE id=$id")) {
      $ok = false;
    }
  }
  echo json_encode(['success' => $ok]);
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
      height: 220px;
      border: 3px dashed #ccc;
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background-color: #fafafa;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
    }
    .drag-area:hover, .drag-area.drag-over {
      border-color: #ec008c;
      background-color: #fff0f8;
    }
    .drag-area i { font-size: 3rem; color: #ccc; }
    .drag-area.drag-over i { color: #ec008c; }
    .drag-area p { margin: 8px 0 0; color: #888; font-size: 15px; }

    .preview-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 15px;
    }
    .preview-thumb {
      position: relative;
      width: 100px;
      height: 100px;
      border-radius: 8px;
      overflow: hidden;
      border: 2px solid #ddd;
    }
    .preview-thumb img {
      width: 100%; height: 100%; object-fit: cover;
    }
    .preview-thumb .remove-preview {
      position: absolute; top: 3px; right: 3px;
      background: rgba(220,53,69,0.85);
      color: white; border: none; border-radius: 50%;
      width: 20px; height: 20px; font-size: 12px;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    .preview-thumb { cursor: grab; }
    .preview-thumb:active { cursor: grabbing; }
    .preview-ghost { opacity: .4; }

    /* Galería sortable */
    .sortable-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }
    .sortable-item {
      position: relative;
      width: 140px;
      cursor: grab;
      border-radius: 10px;
      overflow: hidden;
      border: 2px solid #dee2e6;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,.08);
      transition: box-shadow .2s;
    }
    .sortable-item:active { cursor: grabbing; }
    .sortable-item.sortable-ghost { opacity: .4; }
    .sortable-item img {
      width: 100%; height: 110px; object-fit: cover; display: block;
    }
    .sortable-item .foto-footer {
      padding: 4px 6px;
      display: flex; align-items: center; justify-content: space-between;
      background: #f8f9fa;
    }
    .sortable-item .drag-handle {
      color: #aaa; cursor: grab; font-size: 16px;
    }
    .sortable-item .orden-badge {
      font-size: 11px; font-weight: 700;
      background: #ec008c; color: #fff;
      border-radius: 10px; padding: 1px 7px;
    }
    .sortable-item .btn-del {
      background: none; border: none; color: #dc3545;
      cursor: pointer; font-size: 15px; padding: 0;
    }
    .foto-principal-badge {
      position: absolute; top: 6px; left: 6px;
      background: #ec008c; color: #fff;
      font-size: 10px; font-weight: 700;
      border-radius: 8px; padding: 2px 6px;
    }
    .upload-card, .gallery-card {
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0,0,0,.07);
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
        <div class="container-fluid px-4">

          <div class="row g-4">

            <!-- ── COLUMNA IZQUIERDA: subir fotos ── -->
            <div class="col-lg-4">
              <div class="card upload-card p-3 h-100">
                <h5 class="mb-3"><i class="bi bi-cloud-upload-fill text-magenta-dark"></i> Subir nuevas fotos</h5>
                <form method="POST" enctype="multipart/form-data" id="upload-form">
                  <input type="hidden" name="formType" value="subirFotos">

                  <div class="drag-area mb-3" id="drag-area">
                    <i class="bi bi-images"></i>
                    <p>Arrastra imágenes aquí</p>
                    <small class="text-muted">o haz clic para seleccionar</small>
                    <input type="file" id="fotos" name="fotos[]" multiple accept="image/*" style="display:none;">
                  </div>

                  <!-- Previsualización -->
                  <div id="preview-container" style="display:none;">
                    <p class="fw-bold mb-1">Seleccionadas:</p>
                    <div class="preview-grid" id="preview-grid"></div>
                    <hr>
                  </div>

                  <button type="submit" class="btn bg-magenta-dark text-white w-100 mt-2">
                    <i class="bi bi-cloud-upload-fill"></i> Subir Fotos
                  </button>
                </form>

                <?php if (isset($reporte)) echo "<div class='mt-3'>$reporte</div>"; ?>
              </div>
            </div>

            <!-- ── COLUMNA DERECHA: galería + reordenar ── -->
            <div class="col-lg-8">
              <div class="card gallery-card p-3">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                  <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill text-indigo-dark"></i>
                    Fotos de la propiedad
                    <span class="badge bg-secondary ms-1"><?php echo count($fotos); ?></span>
                  </h5>
                  <button class="btn btn-sm bg-indigo-dark text-white" id="btn-guardar-orden" style="display:none;"
                    onclick="guardarOrden()">
                    <i class="bi bi-floppy-fill"></i> Guardar orden
                  </button>
                </div>

                <p class="text-muted small mb-3">
                  <i class="bi bi-info-circle"></i>
                  Arrastra las fotos para cambiar el orden en que aparecerán. La primera foto será la principal.
                </p>

                <?php if ($fotos): ?>
                  <div class="sortable-grid" id="sortable-gallery">
                    <?php foreach ($fotos as $i => $foto): ?>
                      <div class="sortable-item" data-id="<?php echo $foto['id']; ?>">
                        <?php if ($i === 0): ?>
                          <span class="foto-principal-badge"><i class="bi bi-star-fill"></i> Principal</span>
                        <?php endif; ?>
                        <img src="fotos/<?php echo $codigoPropiedad; ?>/<?php echo $foto['nombre_foto']; ?>"
                             alt="Foto <?php echo $i + 1; ?>"
                             onerror="this.src='img/no-image.png'">
                        <div class="foto-footer">
                          <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
                          <span class="orden-badge">#<span class="num"><?php echo $i + 1; ?></span></span>
                          <button class="btn-del" onclick="confirmarEliminar(<?php echo $foto['id']; ?>)"
                                  title="Eliminar">
                            <i class="bi bi-trash3-fill"></i>
                          </button>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <div class="text-center py-5 text-muted">
                    <i class="bi bi-images" style="font-size:3rem;"></i>
                    <p class="mt-2">No hay fotos para esta propiedad.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>

          </div><!-- /row -->
        </div><!-- /container -->
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
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

  <script>
    // ── Drag & drop zona de subida ──────────────────────────────
    const dragArea   = document.getElementById('drag-area');
    const fileInput  = document.getElementById('fotos');
    const previewContainer = document.getElementById('preview-container');
    const previewGrid      = document.getElementById('preview-grid');

    dragArea.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', e => handleFiles(e.target.files));
    dragArea.addEventListener('dragover', e => { e.preventDefault(); dragArea.classList.add('drag-over'); });
    dragArea.addEventListener('dragleave', () => dragArea.classList.remove('drag-over'));
    dragArea.addEventListener('drop', e => {
      e.preventDefault();
      dragArea.classList.remove('drag-over');
      fileInput.files = e.dataTransfer.files;
      handleFiles(e.dataTransfer.files);
    });

    let previewSortable = null;

    function handleFiles(files) {
      previewGrid.innerHTML = '';
      previewContainer.style.display = files.length ? 'block' : 'none';
      Array.from(files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = ev => {
          const div = document.createElement('div');
          div.className = 'preview-thumb';
          div.dataset.name = file.name;
          div.innerHTML = `<img src="${ev.target.result}" alt="">
            <button class="remove-preview" onclick="this.parentElement.remove()" title="Quitar">&times;</button>`;
          previewGrid.appendChild(div);
        };
        reader.readAsDataURL(file);
      });
      // Activar Sortable en el preview-grid
      if (!previewSortable) {
        previewSortable = Sortable.create(previewGrid, {
          animation: 150,
          ghostClass: 'preview-ghost'
        });
      }
    }

    // ── SortableJS en galería existente ────────────────────────
    const gallery = document.getElementById('sortable-gallery');
    const btnGuardar = document.getElementById('btn-guardar-orden');

    if (gallery) {
      Sortable.create(gallery, {
        animation: 200,
        ghostClass: 'sortable-ghost',
        onEnd: () => {
          // Actualizar badges de número y badge "Principal"
          gallery.querySelectorAll('.sortable-item').forEach((el, i) => {
            el.querySelector('.num').textContent = i + 1;
            const badge = el.querySelector('.foto-principal-badge');
            if (i === 0) {
              if (!badge) {
                el.insertAdjacentHTML('afterbegin',
                  '<span class="foto-principal-badge"><i class="bi bi-star-fill"></i> Principal</span>');
              }
            } else {
              if (badge) badge.remove();
            }
          });
          btnGuardar.style.display = 'inline-block';
        }
      });
    }

    // ── Guardar nuevo orden vía AJAX ────────────────────────────
    function guardarOrden() {
      const ids = Array.from(gallery.querySelectorAll('.sortable-item')).map(el => el.dataset.id);
      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'formType=ordenarFotos&ids=' + JSON.stringify(ids)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          btnGuardar.style.display = 'none';
          btnGuardar.insertAdjacentHTML('afterend',
            '<span class="text-success ms-2 small"><i class="bi bi-check-circle-fill"></i> Orden guardado</span>');
          setTimeout(() => document.querySelector('.text-success.ms-2')?.remove(), 3000);
        } else {
          alert('Error al guardar el orden: ' + (data.message || ''));
        }
      });
    }

    // ── Eliminar foto ───────────────────────────────────────────
    function confirmarEliminar(idFoto) {
      if (confirm('¿Eliminar esta foto?')) {
        window.location.href = '?eliminar=true&id_foto=' + idFoto + '&codigo=<?php echo $codigoPropiedad; ?>';
      }
    }
  </script>
</body>

</html>