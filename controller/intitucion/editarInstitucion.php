<?php
include 'conexion.php'; // Incluir el archivo de conexión a la base de datos.
$formConfigReport = include 'procesar_formulario_editar_institucion.php'; // Configuración del formulario.
$tableNameReport = "company"; // Nombre de la tabla.

$id = 1;
$recordData = [];

// Si se proporciona un ID, obtener los datos del registro.
if ($id) {
    $queryFetch = "SELECT * FROM $tableNameReport WHERE id = $id";
    $resultFetch = $conn->query($queryFetch);

    if ($resultFetch && $resultFetch->num_rows > 0) {
        $recordData = $resultFetch->fetch_assoc(); // Guardar los datos del registro en un array.
    } else {
        die("Registro no encontrado o error en la consulta.");
    }
} else {
    die("ID no especificado.");
}

// Función para generar atributos HTML de los campos
function generateAttributesReport($attributes)
{
    $html = '';
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" ";
    }
    return $html;
}

$includeFieldsReport = isset($formConfigReport['include_fields']) ? $formConfigReport['include_fields'] : [];

// Para mostrar el estado de la operación
$toastMessage = '';
$toastType = ''; // success o error

// Procesamiento del formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ActualizarEmpresa'])) {
    $formDataReport = [];
    foreach ($includeFieldsReport as $fieldName) {
        if (isset($_POST[$fieldName])) {
            $formDataReport[$fieldName] = $conn->real_escape_string($_POST[$fieldName]);
        }
    }

    // Verificar si se subió una foto
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        // Definir la carpeta donde se guardará la foto
        $uploadDir = 'img/icons/';
        $fileName = basename($_FILES['logo']['name']);
        $filePath = $uploadDir . $fileName;

        // Asegurarse de que el directorio de carga existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Mover el archivo a la carpeta de destino
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
            // Si la foto se subió correctamente, agregar solo el nombre del archivo al array de datos
            $formDataReport['logo'] = $fileName;
        } else {
            // Manejar error en la carga de la imagen
            echo "<script>alert('Error al cargar la foto.');</script>";
        }
    }

    // Actualizar el registro en la base de datos
    $updateFields = [];
    foreach ($formDataReport as $key => $value) {
        $updateFields[] = "$key = '$value'";
    }
    $updateQuery = "UPDATE $tableNameReport SET " . implode(", ", $updateFields) . " WHERE id = $id";

    if ($conn->query($updateQuery) === TRUE) {
        $toastMessage = 'Actualización exitosa.';
        $toastType = 'success';
    } else {
        $toastMessage = 'Error al actualizar: ' . $conn->error;
        $toastType = 'error';
    }
}

?>

<div class="card p-3">
    <!-- Mostrar toast si hay un mensaje -->
    <?php if ($toastMessage): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <div class="toast align-items-center text-bg-<?= $toastType === 'success' ? 'success' : 'danger' ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $toastMessage ?>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center">
        <img src="img/banners/company.png" alt="banner" width="400px" height="200px">
    </div>

    <!-- Formulario para editar datos del registro -->
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <?php
            foreach ($includeFieldsReport as $fieldName) {
                generateFieldReport($fieldName, $recordData);
            }

            // Función para generar los campos del formulario
            function generateFieldReport($fieldName, $recordData)
            {
                global $formConfigReport;
                echo "<div class='form-group col-md-12'>";
                $label = isset($formConfigReport[$fieldName]['label']) ? $formConfigReport[$fieldName]['label'] : ucfirst(str_replace('_', ' ', $fieldName));
                $type = isset($formConfigReport[$fieldName]['type']) ? $formConfigReport[$fieldName]['type'] : 'text';
                $attributes = isset($formConfigReport[$fieldName]['attributes']) ? generateAttributesReport($formConfigReport[$fieldName]['attributes']) : '';
                $value = isset($recordData[$fieldName]) ? htmlspecialchars($recordData[$fieldName]) : '';

                echo "<label for='$fieldName' class='form-label'>$label</label>";
                if ($type == 'text' || $type == 'email') {
                    echo "<input type='$type' class='form-control' name='$fieldName' id='$fieldName' value='$value' $attributes required>";
                } elseif ($type == 'select') {
                    echo "<select class='form-select' name='$fieldName' id='$fieldName' $attributes required>";
                    echo "<option value='' disabled>Seleccione una opción</option>";
                    foreach ($formConfigReport[$fieldName]['options'] as $option) {
                        $selected = $value == $option ? 'selected' : '';
                        echo "<option value='$option' $selected>$option</option>";
                    }
                    echo "</select>";
                } elseif ($type == 'file') {
                    echo "<input type='file' class='form-control' name='$fieldName' id='$fieldName' $attributes>";
                    if (!empty($value)) {
                        echo "<small>Archivo actual: <a href='img/icons/$value' target='_blank'>" . basename($value) . "</a></small>";
                    }
                } elseif ($type == 'textarea') {
                    echo "<textarea class='form-control' name='$fieldName' id='$fieldName' $attributes maxlength='255'>$value</textarea>";
                }
                echo "</div>";
            }
            ?>

            <!-- Campo para foto -->
            <div class="form-group col-md-12">
                <!-- Mostrar miniatura de la foto actual -->
                <?php if (!empty($recordData['logo'])): ?>
                    <div>
                        <img src="img/icons/<?= $recordData['logo']; ?>" alt="Foto actual" width="150" height="auto" class="img-thumbnail mb-2">
                    </div>
                    <small>Foto actual</small>
                <?php else: ?>
                    <small>No hay foto cargada</small>
                <?php endif; ?>
            </div>

        </div>
        <div class="mt-3">
            <button type="submit" class="btn bg-magenta-dark text-white" name="ActualizarEmpresa"><i class="bi bi-arrow-down-up"></i> Actualizar </button>
        </div>
    </form>
</div>

<script>
    // Inicializar toasts de Bootstrap
    document.addEventListener('DOMContentLoaded', () => {
        const toastElements = document.querySelectorAll('.toast');
        toastElements.forEach(toastElement => {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        });
    });
</script>