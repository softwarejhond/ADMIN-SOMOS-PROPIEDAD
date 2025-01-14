<?php
session_start();
// Verificar si el usuario ha iniciado sesión (asegúrate de tener la misma lógica que en index.php)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}
include("../funciones.php"); // Asegúrate de que la ruta sea correcta
header('Content-Type: application/json'); //Indica que la respuesta es json
$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'upload':
                $response = uploadImage();
                echo json_encode($response);
                exit;
            case 'eliminar':
                 $response = eliminarImagen();
                  echo json_encode($response);
                exit;
            case 'ver':
                  $response = verImagenes();
                  echo json_encode($response);
                  exit;
          default:
                $response = ['error' => "No se ha especificado una acción válida"];
                echo json_encode($response);
                exit;
        }
    } else {
         $response = ['error' => "No se ha especificado una acción"];
           echo json_encode($response);
           exit;
    }
}

function uploadImage()
  {
      global $conn;
       $response = [];
      if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
         $response = ['error' => 'El id de la propiedad no es válido.'];
         return $response;
        }
    $propiedadId = $_GET['id'];
    if(isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
        $totalImages = count($_FILES['images']['tmp_name']);
          if($totalImages > 60)
          {
             $response = ['error' => "No puedes subir mas de 60 imagenes"];
             return $response;
        }
     $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp', 'image/jpg']; // Tipos de archivo permitidos
         for ($i = 0; $i < $totalImages; $i++) {
        if ($_FILES['images']['error'][$i] == UPLOAD_ERR_OK) {
              $mimeType = mime_content_type($_FILES['images']['tmp_name'][$i]);
            if (!in_array($mimeType, $allowedMimeTypes)) {
                  $response = ['error' => 'El tipo de archivo no está permitido para la imagen: '.$_FILES['images']['name'][$i]];
                    return $response;
              }
           $imageName = uniqid() . '_' . $_FILES['images']['name'][$i];
             $imagePath = '../images/propiedades/' . $propiedadId . '/' . $imageName;
          $uploadDir = '../images/propiedades/'.$propiedadId.'/';
          if (!is_dir($uploadDir)) {
               mkdir($uploadDir, 0777, true);
         }
           if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $imagePath))
             {
               $exito = guardarImagenPropiedad($propiedadId, $imagePath);
                if(!$exito) {
                  $response = ['error' => "Error al guardar las imagenes"];
                      return $response;
                }
        }else {
               $response = ['error' => "Error al subir la imagen: ".$_FILES['images']['name'][$i]];
              return $response;
         }
        }else{
            $response = ['error' => "Error en la subida de la imagen: ".$_FILES['images']['name'][$i]];
             return $response;
      }
     }
         $response = ['success' => "Imagenes subidas exitosamente"];
        die(json_encode($response)); //  <--AQUI DEBE IR EL CODIGO
        return $response;
     } else {
        $response = ['error' => "Debes seleccionar al menos una imagen"];
          return $response;
   }
}
function eliminarImagen()
{
  global $conn;
    $response = [];
      if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $response = ['error' => 'El id de la imagen no es válido.'];
       return $response;
      }
  $imagenId = $_GET['id'];
    $imagen = obtenerImagenPropiedadPorId($imagenId);
  if ($imagen) {
       $rutaImagen = $imagen['nombre_foto'];
         if (file_exists($rutaImagen)) {
            if (unlink($rutaImagen)) {
                if(eliminarImagenPropiedad($imagenId)){
                     $response = ['success' => "Imagen eliminada correctamente"];
                      return $response;
                }else{
                     $response = ['error' => "Error al eliminar la imagen de la base de datos"];
                     return $response;
                }
           } else {
                   $response = ['error' => "Error al eliminar la imagen del servidor"];
                     return $response;
                }
        }else{
            $response = ['error' => "La imagen no existe en el servidor"];
             return $response;
        }
    } else {
       $response = ['error' => "La imagen no existe"];
       return $response;
   }
}
function verImagenes()
{
  global $conn;
  $response = [];
    if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $response = ['error' => 'El id de la propiedad no es válido.'];
       return $response;
     }
  $propiedadId = $_GET['id'];
    $imagenes = obtenerImagenesPropiedad($propiedadId);
      if ($imagenes)
       {
          $response = [
            'success' => true,
           'imagenes' => $imagenes
            ];
           return $response;
       } else {
         $response = ['error' => 'No hay imagenes para esta propiedad'];
         return $response;
      }
}
?>