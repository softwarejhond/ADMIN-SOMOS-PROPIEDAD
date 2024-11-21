<?php
include 'conexion.php';  // Asegúrate de incluir tu archivo de conexión

$formConfig = include 'procesar_formulario.php';

$tableName = "proprieter"; // Tu tabla de propiedades
$columnsQuery = "SHOW COLUMNS FROM $tableName";
$columnsResult = $conn->query($columnsQuery);

if (!$columnsResult) {
    die("Error al obtener columnas: " . $conn->error);
}

// Función para generar atributos HTML
function generateAttributes($attributes)
{
    $html = '';
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" ";
    }
    return $html;
}

// Iniciar el formulario
echo '<form action="procesar_formulario.php" method="POST" enctype="multipart/form-data">';

// Iterar sobre las columnas para generar campos
while ($column = $columnsResult->fetch_assoc()) {
    $fieldName = $column['Field'];

    if (!in_array($fieldName, $formConfig['include_fields'])) {
        continue; // Excluir campos no deseados
    }

    $fieldType = isset($formConfig[$fieldName]['type']) ? $formConfig[$fieldName]['type'] : 'text'; // Tipo predeterminado
    $attributes = isset($formConfig[$fieldName]['attributes']) ? generateAttributes($formConfig[$fieldName]['attributes']) : '';

    echo "<div class='form-group'>";
    echo "<label for='$fieldName'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

    switch ($fieldType) {
        case 'select':
            // Obtener opciones de otra tabla
            $optionsTable = $formConfig[$fieldName]['options_table'];
            $valueColumn = $formConfig[$fieldName]['value_column'];
            $labelColumn = $formConfig[$fieldName]['label_column'];
            $optionsQuery = "SELECT $valueColumn, $labelColumn FROM $optionsTable";
            $optionsResult = $conn->query(query: $optionsQuery);

            echo "<select name='$fieldName' id='$fieldName' $attributes>";
            while ($option = $optionsResult->fetch_assoc()) {
                echo "<option value='{$option[$valueColumn]}'>{$option[$labelColumn]}</option>";
            }
            echo "</select>";
            break;

        case 'radio':
            // Opciones estáticas
            foreach ($formConfig[$fieldName]['options'] as $option) {
                echo "<div class='form-check'>
                        <input type='radio' name='$fieldName' value='$option' $attributes>
                        <label class='form-check-label'>$option</label>
                      </div>";
            }
            break;

        case 'checkbox':
            echo "<input type='checkbox' name='$fieldName' id='$fieldName' $attributes>";
            break;

        case 'date':
        case 'password':
        case 'email':
        case 'number':
        case 'text':
        default:
            echo "<input type='$fieldType' name='$fieldName' id='$fieldName' $attributes>";
            break;
    }

    echo "</div>";
}

// Botón de envío
echo '<button type="submit" class="btn bg-magenta-dark text-white">Enviar</button>';
echo '</form>';
