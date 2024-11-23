<?php
// Incluir la conexión a la base de datos y la configuración del formulario
include 'conexion.php';
$formConfig = include 'procesar_formulario_reparador.php'; // Cargar el archivo de configuración del formulario

$tableName = "repairmen"; // Nombre de la tabla donde se guardarán los datos
$columnsQuery = "SHOW COLUMNS FROM $tableName"; // Consultar las columnas de la tabla
$columnsResult = $conn->query($columnsQuery); // Ejecutar la consulta

// Verificar si hubo un error al obtener las columnas
if (!$columnsResult) {
    die("Error al obtener columnas: " . $conn->error); // Mostrar mensaje de error si la consulta falla
}

// Función para generar los atributos HTML de los campos dinámicamente
function generateAttributes($attributes)
{
    $html = '';
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" "; // Crear una cadena de atributos HTML
    }
    return $html;
}

// Obtener los campos que deben ser incluidos en el formulario desde la configuración
$includeFields = isset($formConfig['include_fields']) ? $formConfig['include_fields'] : [];
?>

<style>
    /* Estilos personalizados para el formulario */
    .form-check-inline .form-check {
        margin-right: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #30336b;
        text-align: left;
    }

    .form-check-label {
        margin-left: 5px;
    }
</style>

<div class="card p-3">
    <?php
    // Comprobar si el formulario ha sido enviado mediante POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['guardarReparador'])) {
            // Recoger los datos de los campos del formulario y escapar los valores para evitar inyecciones SQL
            $formData = [];
            foreach ($includeFields as $fieldName) {
                if (isset($_POST[$fieldName])) {
                    $formData[$fieldName] = $conn->real_escape_string($_POST[$fieldName]); // Escapar cada campo
                }
            }

            // Agregar estado y fecha de creación a los datos del formulario
            $formData['estado'] = 'ACTIVO'; // Valor predeterminado para el campo 'estado'
            $formData['fecha_creacion'] = date('Y-m-d H:i:s'); // Fecha y hora actual para el campo 'fecha_creacion'

            // Verificar si el reparador ya existe en la base de datos usando la identificación
            $identificacion = $formData['identificacion'];
            $queryCheck = "SELECT * FROM $tableName WHERE identificacion = '$identificacion'"; // Consulta para verificar existencia
            $result = $conn->query($queryCheck); // Ejecutar la consulta

            // Si el reparador ya existe, mostrar una alerta
            if ($result->num_rows > 0) {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    alert('El reparador ya existe.'); // Alerta si ya existe
                });
            </script>";
            } else {
                // Si el reparador no existe, generar la consulta de inserción
                $columns = implode(", ", array_keys($formData)); // Unir los nombres de las columnas
                $values = "'" . implode("', '", array_values($formData)) . "'"; // Unir los valores con comillas simples

                // Consulta de inserción para guardar los datos en la base de datos
                $queryInsert = "INSERT INTO $tableName ($columns) VALUES ($values)";

                // Ejecutar la consulta de inserción
                if ($conn->query($queryInsert) === TRUE) {
                    // Si la inserción es exitosa, mostrar una alerta
                    echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        alert('Registro exitoso.'); // Alerta de éxito
                    });
                </script>";
                } else {
                    // Si hay un error al insertar, mostrar un mensaje de error
                    echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        alert('Error al registrar: " . $conn->error . "'); // Alerta de error
                    });
                </script>";
                }
            }
        }
    }
    ?>

    <!-- Formulario de entrada -->
    <form method="POST">
        <div class="row">
            <?php
            // Recorrer los campos definidos en 'include_fields' y generar el HTML para cada uno
            foreach ($includeFields as $fieldName) {
                generateField($fieldName); // Llamar a la función que genera cada campo
            }

            // Función para generar los campos del formulario
            function generateField($fieldName)
            {
                global $formConfig;

                echo "<div class='form-group col-md-12'>"; // Iniciar el contenedor del campo

                // Obtener el label configurado para el campo, si no existe, usar un nombre por defecto
                $label = isset($formConfig[$fieldName]['label']) ? $formConfig[$fieldName]['label'] : ucfirst(str_replace('_', ' ', $fieldName));
                $type = isset($formConfig[$fieldName]['type']) ? $formConfig[$fieldName]['type'] : 'text'; // Tipo de campo
                $attributes = isset($formConfig[$fieldName]['attributes']) ? generateAttributes($formConfig[$fieldName]['attributes']) : ''; // Atributos del campo

                // Mostrar el label del campo
                echo "<label for='$fieldName' class='form-label'>$label</label>";

                // Generar los campos HTML según el tipo configurado
                if ($type == 'text' || $type == 'email') {
                    // Si es un campo de texto o correo electrónico, generar un input
                    echo "<input type='$type' class='form-control' name='$fieldName' id='$fieldName' $attributes required>";
                } elseif ($type == 'select') {
                    // Si es un campo de selección (select), generar el select y las opciones
                    echo "<select class='form-select' name='$fieldName' id='$fieldName' $attributes required>";
                    echo "<option value='' disabled selected>Seleccione una opción</option>";
                    foreach ($formConfig[$fieldName]['options'] as $option) {
                        echo "<option value='$option'>$option</option>"; // Generar las opciones dinámicamente
                    }
                    echo "</select>";
                }

                echo "</div>"; // Cerrar el contenedor del campo
            }

            ?>
        </div>
        <div class="mt-3">
            <!-- Botón para enviar el formulario -->
            <button type="submit" class="btn bg-magenta-dark text-white" name="guardarReparador"><i class="bi bi-floppy-fill"></i> Guardar</button>
        </div>
    </form>
</div>