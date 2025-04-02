<?php
include 'conexion.php'; // Incluir el archivo de conexión a la base de datos.

$formConfigReport = include 'procesar_formulario_reporte.php'; // Cargar la configuración del formulario desde un archivo PHP.
$tableNameReport = "report"; // Especifica el nombre de la tabla donde se almacenarán los datos.
$columnsQueryReport = "SHOW COLUMNS FROM $tableNameReport"; // Consulta SQL para obtener las columnas de la tabla 'report'.
$columnsResultReport = $conn->query($columnsQueryReport); // Ejecutar la consulta para obtener la información de las columnas.

if (!$columnsResultReport) {
    die("Error al obtener columnas: " . $conn->error); // Si la consulta falla, mostrar el error.
}

function generateAttributesReport($attributes)
{
    $html = ''; // Inicializar la variable para contener los atributos HTML.
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" "; // Añadir cada atributo con su valor.
    }
    return $html; // Retornar los atributos generados.
}

$includeFieldsReport = isset($formConfigReport['include_fields']) ? $formConfigReport['include_fields'] : []; // Obtener los campos que deben incluirse en el formulario desde la configuración.
?>

<div class="card p-3">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Comprobar si el formulario se ha enviado (método POST).
        if (isset($_POST['guardarReporte'])) { // Comprobar si se presionó el botón "Guardar".
            $formDataReport = []; // Inicializar un array para almacenar los datos del formulario.
            foreach ($includeFieldsReport as $fieldName) { // Iterar sobre los campos definidos en la configuración.
                if (isset($_POST[$fieldName])) {
                    // Escapar los datos antes de insertarlos para evitar inyecciones SQL.
                    $formDataReport[$fieldName] = $conn->real_escape_string($_POST[$fieldName]);
                }
            }

            // Asignar valores predeterminados a los campos adicionales.
            $formDataReport['estadoReporte'] = 'SIN ATENDER'; // Estado por defecto.
            $formDataReport['fechaCreacion'] = date('Y-m-d H:i:s'); // Fecha y hora actuales.

            // Procesar el archivo 'fotoReporte' si se ha cargado un archivo.
            if (isset($_FILES['fotoReporte']) && $_FILES['fotoReporte']['error'] === UPLOAD_ERR_OK) {
                // Obtener detalles del archivo cargado.
                $fileTmpPath = $_FILES['fotoReporte']['tmp_name'];
                $fileName = $_FILES['fotoReporte']['name'];
                $fileSize = $_FILES['fotoReporte']['size'];
                $fileType = $_FILES['fotoReporte']['type'];

                // Validar que el archivo sea una imagen.
                if (in_array($fileType, ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'])) {
                    // Generar un nombre único para el archivo.
                    $uploadDir = 'fotosReportes/';
                    $newFileName = uniqid() . '_' . $fileName;
                    $uploadFilePath = $uploadDir . $newFileName;

                    // Mover el archivo a la carpeta de destino.
                    if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                        // Guardar la ruta del archivo en los datos del formulario.
                        $formDataReport['fotoReporte'] = $uploadFilePath;
                    } else {
                        echo "<script>Swal.fire('Error', 'Error al cargar la imagen.', 'error');</script>"; // Error al mover el archivo.
                    }
                } else {
                    echo "<script>Swal.fire('Error', 'Solo se permiten imágenes (PNG, JPEG, JPG, GIF).', 'error');</script>"; // Validación del tipo de archivo.
                }
            }

            // Comprobar si el código del reporte ya existe en la base de datos.
            $codigoReporte = $formDataReport['codigoReporte'];
            $queryCheckReport = "SELECT * FROM $tableNameReport WHERE codigoReporte = '$codigoReporte'";
            $resultCheckReport = $conn->query($queryCheckReport);

            if ($resultCheckReport->num_rows > 0) {
                echo "<script>Swal.fire('Advertencia', 'El reporte ya existe.', 'warning');</script>"; // Mostrar mensaje si el reporte ya existe.
            } else {
                // Si el reporte no existe, insertar los datos en la base de datos.
                $columnsReport = implode(", ", array_keys($formDataReport)); // Crear la lista de columnas.
                $valuesReport = "'" . implode("', '", array_values($formDataReport)) . "'"; // Crear la lista de valores.
                $queryInsertReport = "INSERT INTO $tableNameReport ($columnsReport) VALUES ($valuesReport)"; // Consulta SQL de inserción.

                if ($conn->query($queryInsertReport) === TRUE) {
                    echo "<script>Swal.fire('Éxito', 'Registro exitoso.', 'success');</script>"; // Mostrar mensaje si la inserción fue exitosa.
                } else {
                    echo "<script>Swal.fire('Error', 'Error al registrar: " . $conn->error . "', 'error');</script>"; // Mostrar mensaje si hay un error.
                }
            }
        }
    }
    ?>

    <!-- Formulario para recolectar los datos del reporte -->
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <?php
            // Generar dinámicamente los campos del formulario según la configuración.
            foreach ($includeFieldsReport as $fieldName) {
                generateFieldReport($fieldName);
            }

            function generateFieldReport($fieldName)
            {
                global $formConfigReport;
                echo "<div class='form-group col-md-12'>";
                // Obtener el label, tipo y atributos del campo desde la configuración.
                $label = isset($formConfigReport[$fieldName]['label']) ? $formConfigReport[$fieldName]['label'] : ucfirst(str_replace('_', ' ', $fieldName));
                $type = isset($formConfigReport[$fieldName]['type']) ? $formConfigReport[$fieldName]['type'] : 'text';
                $attributes = isset($formConfigReport[$fieldName]['attributes']) ? generateAttributesReport($formConfigReport[$fieldName]['attributes']) : '';

                echo "<label for='$fieldName' class='form-label'>$label</label>"; // Etiqueta del campo.
                // Mostrar el campo dependiendo del tipo.
                if ($type == 'text' || $type == 'email') {
                    echo "<input type='$type' class='form-control' name='$fieldName' id='$fieldName' $attributes required>";
                } elseif ($type == 'select') {
                    echo "<select class='form-select' name='$fieldName' id='$fieldName' $attributes required>";
                    echo "<option value='' disabled selected>Seleccione una opción</option>";
                    foreach ($formConfigReport[$fieldName]['options'] as $option) {
                        echo "<option value='$option'>$option</option>";
                    }
                    echo "</select>";
                } elseif ($type == 'file') {
                    echo "<input type='file' class='form-control' name='$fieldName' id='$fieldName' $attributes required>";
                } elseif ($type == 'textarea') {
                    echo "<textarea class='form-control' name='$fieldName' id='$fieldName' $attributes maxlength='255' oninput='updateCharCount()'></textarea>";
                    echo "<small id='$fieldName-count' class='form-text text-muted'>0/255 caracteres</small>"; // Contador de caracteres.
                }
                echo "</div>";
            }
            ?>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn bg-magenta-dark text-white" name="guardarReporte"><i class="bi bi-floppy-fill"></i> Guardar</button>
        </div>
    </form>
</div>

<script>
    function genRandomString(length) {
        var chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
        var charLength = chars.length;
        var result = '';
        for (var i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * charLength)); // Generar una cadena aleatoria.
        }
        return result; // Retornar la cadena aleatoria generada.
    }

    let random = genRandomString(12); // Generar un código aleatorio para el reporte.
    document.getElementById('codigoReporte').value = random; // Asignar el código generado al campo del formulario.
</script>
<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir Popper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybV5DAmDlW5U72dUj5SC2z9z3E6w5pFnidkAY07eMOm1PcHpT" crossorigin="anonymous"></script>

<script>
    // Función para actualizar el contador de caracteres en el campo 'situacionReportada'.
    function updateCharCount() {
        var textarea = document.getElementById('situacionReportada');
        var charCount = textarea.value.length;
        var maxLength = 255;
        var counter = document.getElementById('situacionReportada-count');
        counter.textContent = charCount + '/' + maxLength + ' caracteres'; // Actualizar el contador de caracteres.
    }
</script>

<!-- Incluir SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>