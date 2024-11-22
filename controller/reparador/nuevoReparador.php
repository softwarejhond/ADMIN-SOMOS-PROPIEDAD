<?php
include 'conexion.php';
$formConfig = include 'procesar_formulario_reparador.php'; // Asegúrate de que el archivo existe y está configurado correctamente.

$tableName = "repairmen";
$columnsQuery = "SHOW COLUMNS FROM $tableName";
$columnsResult = $conn->query($columnsQuery);

if (!$columnsResult) {
    die("Error al obtener columnas: " . $conn->error);
}

function generateAttributes($attributes)
{
    $html = '';
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" ";
    }
    return $html;
}

$includeFields = isset($formConfig['include_fields']) ? $formConfig['include_fields'] : [];
?>

<style>
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identificacion = $conn->real_escape_string($_POST['identificacion']);
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $telefono = $conn->real_escape_string($_POST['telefono']);
        $email = $conn->real_escape_string($_POST['email']);
        $profesion = $conn->real_escape_string($_POST['profesion']);
        $estado = 'ACTIVO';
        $fecha_creacion = date('Y-m-d H:i:s'); // Fecha y hora actual

        // Verificar si el reparador ya existe
        $queryCheck = "SELECT * FROM $tableName WHERE identificacion = '$identificacion'";
        $result = $conn->query($queryCheck);

        if ($result->num_rows > 0) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    alert('El reparador ya existe.');
                });
            </script>";
        } else {
            // Insertar reparador
            $queryInsert = "INSERT INTO $tableName (identificacion, nombre, telefono, email, profesion, estado, fecha_creacion) 
                            VALUES ('$identificacion', '$nombre', '$telefono', '$email', '$profesion', '$estado', '$fecha_creacion')";

            if ($conn->query($queryInsert) === TRUE) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        alert('Registro exitoso.');
                    });
                </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        alert('Error al registrar: " . $conn->error . "');
                    });
                </script>";
            }
        }
    }
    ?>

    <form method="POST">
        <div class="row">
            <?php
            foreach ($includeFields as $fieldName) {
                generateField($fieldName);
            }
            function generateField($fieldName)
            {
                global $formConfig;

                echo "<div class='form-group col-md-12'>";

                // Obtener el label configurado para el campo
                $label = isset($formConfig[$fieldName]['label']) ? $formConfig[$fieldName]['label'] : ucfirst(str_replace('_', ' ', $fieldName));
                $type = isset($formConfig[$fieldName]['type']) ? $formConfig[$fieldName]['type'] : 'text';
                $attributes = isset($formConfig[$fieldName]['attributes']) ? generateAttributes($formConfig[$fieldName]['attributes']) : '';

                // Mostrar el label del campo
                echo "<label for='$fieldName' class='form-label'>$label</label>";

                // Mostrar los campos de acuerdo al tipo
                if ($fieldName == 'identificacion' || $fieldName == 'nombre' || $fieldName == 'telefono' || $fieldName == 'email') {
                    echo "<input type='$type' class='form-control' name='$fieldName' id='$fieldName' $attributes required>";
                } elseif ($fieldName == 'profesion') {
                    echo "<select class='form-select' name='$fieldName' id='$fieldName' required>";
                    echo "<option value='' disabled selected>Seleccione una opción</option>";
                    foreach (['Plomero', 'Electricista', 'Carpintero', 'Doctor', 'Otro'] as $profesion) {
                        echo "<option value='$profesion'>$profesion</option>";
                    }
                    echo "</select>";
                }

                echo "</div>";
            }

            ?>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn bg-magenta-dark text-white">Guardar</button>
        </div>
    </form>
</div>