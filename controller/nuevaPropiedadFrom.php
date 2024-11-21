<?php
include 'conexion.php';
$formConfig = include 'procesar_formulario.php'; // Asegúrate de que el archivo de configuración esté correcto.

$tableName = "proprieter";
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
$fieldsPerStep = 10; // Número de campos por paso (5 por cada columna)
?>

<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
    }

    .progress {
        width: 100%;
        height: 5px;
        background-color: #f3f3f3;
    }

    .progress-bar {
        height: 100%;
        background-color: #ec008c;
        width: 0%;
    }

    .form-navigation {
        margin-top: 20px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
    }


    .form-check-inline .form-check {
        margin-right: 15px;
        /* Espaciado entre opciones */
    }

    .form-label {
        display: block;
        /* Asegura que las etiquetas estén en su propia línea */
        margin-bottom: 5px;
        /* Espaciado inferior entre el label y el input */
        font-weight: bold;
        /* Opcional: hace que las etiquetas sean más visibles */
    }

    .form-check-label {
        margin-left: 5px;
        /* Espaciado entre los radio/checkbox y su texto */
    }
</style>

<div class="progress m-1">
    <div id="progress" class="progress-bar"></div>
</div>
<div class="card p-3">
    <form id="multi-step-form">
        <?php
        // Dividir los campos por pasos
        $totalFields = count($includeFields);
        $steps = ceil($totalFields / $fieldsPerStep); // Número de pasos necesarios

        // Generar cada paso
        for ($step = 1; $step <= $steps; $step++) {
            echo "<div class='step p-1' id='step-$step'>";
            echo "<h2>Datos de la propiedad $step</h2>";
            echo "<span class='form-label text-indigo-dark'>Tenga en cuenta que todos los campos marcados con * son obligatorios</span>";
            echo "<br>";

            echo "<div class='row'>";

            // Crear las dos columnas para cada paso
            $startIndex = ($step - 1) * $fieldsPerStep;
            $endIndex = min($startIndex + $fieldsPerStep, $totalFields);
            $fieldsInStep = array_slice($includeFields, $startIndex, $fieldsPerStep);

            // Columna 1
            echo "<div class='col col-md-6 col-sm-12'>";
            $counter = 0;
            foreach ($fieldsInStep as $fieldName) {
                if ($counter < 5) { // Primer conjunto de 5 campos
                    generateField($fieldName);
                }
                $counter++;
            }
            echo "</div>";

            // Columna 2
            echo "<div class='col col-md-6 col-sm-12'>";
            $counter = 0;
            foreach ($fieldsInStep as $fieldName) {
                if ($counter >= 5) { // Segundo conjunto de 5 campos
                    generateField($fieldName);
                }
                $counter++;
            }
            echo "</div>";

            echo "</div>"; // Cerrar la fila
            echo "</div>"; // Cerrar paso
        }

        function generateField($fieldName)
        {
            global $formConfig, $conn;

            if ($fieldName == 'tipoInmueble') {
                // Consulta para obtener los tipos de propiedad
                $queryTipo = "SELECT id, nombre_tipo FROM tipos"; // Ajusta esta consulta si es necesario
                $resultTipo = mysqli_query($conn, $queryTipo);

                echo "<div class='form-group'>";
                echo "<label for='$fieldName'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                echo "<select name='$fieldName' id='$fieldName' class='form-control' required>";

                if (mysqli_num_rows($resultTipo) > 0) {
                    while ($row = mysqli_fetch_assoc($resultTipo)) {
                        $tipoId = $row['id'];
                        $tipoNombre = $row['nombre_tipo'];
                        echo "<option value='$tipoId'>$tipoNombre</option>";
                    }
                } else {
                    echo "<option value=''>No hay tipos disponibles</option>";
                }
                echo "</select>";
                echo "</div>";
            } else if ($fieldName == 'departamento') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'> " . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                echo "<select name='$fieldName' id='lista_departamento' class='form-control' required>";
                echo "<option value=''>Seleccionar</option>";
                echo "</select>";
                echo "</div>";
            } else if ($fieldName == 'municipios') {
                echo "<div class='form-group' >";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                echo "<select name='$fieldName' id='municipios' class='form-control' required>";
                echo "<option value=''>Seleccionar</option>";
                echo "</select>";
                echo "</div>";
            } else if ($fieldName == 'barrio') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                echo "<select name='$fieldName' id='barrios' class='form-control' required>";
                echo "<option value=''>Seleccionar</option>";
                echo "</select>";
                echo "</div>";
            } else if ($fieldName == 'estrato') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                $estratos = [1, 2, 3, 4, 5, 6]; // Lista de opciones para el estrato
                foreach ($estratos as $value) {
                    echo "<div class='form-check-inline'>";
                    echo "<input type='radio' class='form-check-input' name='$fieldName' id='estrato_$value' value='$value' required>";
                    echo "<label class='form-check-label' for='estrato_$value'> $value</label>";
                    echo "</div>";
                }
                echo "</div>";
            } else if ($fieldName == 'terraza') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='terraza_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label ' for='terraza_switch'> Sí (Si su propiedad no necesita marcarlo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'parqueadero') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                echo "<div class='form-check-inline'>"; // Contenedor para las opciones en línea
                $opcionesParqueadero = ['Sin parqueadero', 'Público', 'Privado']; // Opciones para parqueadero
                foreach ($opcionesParqueadero as $opcion) {
                    $value = strtolower(str_replace(' ', '_', $opcion)); // Convertir a minúsculas y reemplazar espacios por guiones bajos para el valor
                    echo "<div class='form-check form-check-inline'>";
                    echo "<input type='radio' class='form-check-input' name='$fieldName' id='parqueadero_$value' value='$value' required>";
                    echo "<label class='form-check-label' for='parqueadero_$value'>$opcion</label>";
                    echo "</div>";
                }
                echo "</div>"; // Cerrar contenedor en línea
                echo "</div>";
            } else if ($fieldName == 'ascensor') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='ascensor_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='ascensor_switch'> Sí (Si su propiedad tiene ascensor, márquelo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'patio') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='patio_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='patio_switch'> Sí (Si su propiedad tiene patio, márquelo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'cuarto_util') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='cuarto_util_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='cuarto_util_switch'> Sí (Si su propiedad cuenta con cuarto útil, márquelo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'habitaciones') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                $habitaciones = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // Lista de opciones para habitaciones
                foreach ($habitaciones as $value) {
                    echo "<div class='form-check-inline'>";
                    echo "<input type='radio' class='form-check-input' name='$fieldName' id='habitaciones_$value' value='$value' required>";
                    echo "<label class='form-check-label' for='habitaciones_$value'> $value</label>";
                    echo "</div>";
                }
                echo "</div>";
            } else if ($fieldName == 'closet') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

                // Lista de opciones para closet de 0 a 10
                $closetOpciones = range(0, 10); // Genera un array del 0 al 10

                foreach ($closetOpciones as $value) {
                    echo "<div class='form-check-inline'>";
                    echo "<input type='radio' class='form-check-input' name='$fieldName' id='closet_$value' value='$value' required>";
                    echo "<label class='form-check-label' for='closet_$value'> $value</label>";
                    echo "</div>";
                }

                echo "</div>";
            } else if ($fieldName == 'sala') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='sala_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='sala_switch'> Sí (Si su propiedad tiene sala, márquelo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'sala_comedor') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='sala_comedor_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='sala_comedor_switch'> Sí (Si su propiedad tiene sala comedor, márquelo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'comedor') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";
                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='comedor_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='comedor_switch'> Sí (Si su propiedad tiene comedor, márquelo aquí)</label>";
                echo "</div>";
                echo "</div>";
            } else if ($fieldName == 'vista') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

                // Lista de opciones para vista
                $opcionesVista = ['venta', 'ventanal', 'balcon', 'apartamento interno', 'sotano', 'finca', 'lote', 'puerta garage', 'puerta enrollable'];

                foreach ($opcionesVista as $value) {
                    echo "<div class='form-check-inline'>";
                    echo "<input type='radio' class='form-check-input' name='$fieldName' id='vista_$value' value='$value' required>";
                    echo "<label class='form-check-label' for='vista_$value'> " . ucfirst($value) . "</label>";
                    echo "</div>";
                }

                echo "</div>";
            } else if ($fieldName == 'servicios') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

                // Lista de opciones para servicios (del 0 al 10)
                $opcionesServicios = range(0, 10); // Genera un array del 0 al 10

                foreach ($opcionesServicios as $value) {
                    echo "<div class='form-check-inline'>";
                    echo "<input type='radio' class='form-check-input' name='$fieldName' id='servicios_$value' value='$value' required>";
                    echo "<label class='form-check-label' for='servicios_$value'> $value</label>";
                    echo "</div>";
                }

                echo "</div>";
            } else if ($fieldName == 'CuartoServicios') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";

                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='cuarto_servicios_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='cuarto_servicios_switch'> Sí (Si su propiedad cuenta con cuarto de servicios, márquelo aquí)</label>";
                echo "</div>";

                echo "</div>";
            } else if ($fieldName == 'ZonaRopa') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

                // Campo oculto para el valor "no"
                echo "<input type='hidden' name='$fieldName' value='no'>";

                // Switch
                echo "<div class='form-switch'>";
                echo "<input type='checkbox' class='form-check-input mr-3' id='zona_ropa_switch' name='$fieldName' value='sí'>";
                echo "<label class='form-check-label' for='zona_ropa_switch'> Sí (Si su propiedad cuenta con zona de ropa, márquelo aquí)</label>";
                echo "</div>";

                echo "</div>";
            } else if ($fieldName == 'servicios_publicos') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', 'Servicios públicos')) . "</label>";

                // Lista de opciones para servicios públicos
                $opcionesServicios = ['agua', 'electricidad', 'gas', 'internet', 'television'];

                // Campo de entrada para mostrar los servicios seleccionados
                echo "<input type='text' id='servicios_selected' class='form-control' name='servicios_selected' value='' readonly>";

                // Lista de checkboxes para seleccionar los servicios
                foreach ($opcionesServicios as $value) {
                    echo "<div class='form-check form-check-inline'>";
                    echo "<input type='checkbox' class='form-check-input' name='$fieldName' id='servicio_$value' value='$value' onchange='updateServicios()'>";
                    echo "<label class='form-check-label' for='servicio_$value'> " . ucfirst($value) . "</label>";
                    echo "</div>";
                }

                echo "</div>";

                // JavaScript para actualizar el campo de texto
                echo "
    <script>
        function updateServicios() {
            var selectedServices = [];
            var checkboxes = document.querySelectorAll('input[name=\"$fieldName\"]:checked');
            checkboxes.forEach(function(checkbox) {
                selectedServices.push(checkbox.value);
            });
            // Actualizar el campo de texto con los servicios seleccionados
            document.getElementById('servicios_selected').value = selectedServices.join(', ');
        }
    </script>";
            } else if ($fieldName == 'otras_caracteristicas') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";

                // Campo de texto área con límite de 255 caracteres
                echo "<textarea class='form-control' id='$fieldName' name='$fieldName' rows='4' maxlength='255' placeholder='Escriba las características adicionales aquí' oninput='updateCharacterCount()'></textarea>";

                // Contador de caracteres
                echo "<small id='charCount' class='form-text text-muted'>Caracteres restantes: <span id='remainingChars'>255</span></small>";

                echo "</div>";

                // JavaScript para el contador de caracteres
                echo "
    <script>
        function updateCharacterCount() {
            var textArea = document.getElementById('$fieldName');
            var remainingChars = 255 - textArea.value.length;
            document.getElementById('remainingChars').textContent = remainingChars;
        }
    </script>";
            } else if ($fieldName == 'direccion') {
                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', 'Dirección')) . "</label>";
                // Campo para la dirección
                echo "<input type='text' id='direccion' name='$fieldName' class='form-control' placeholder='Escriba la dirección' autocomplete='off'>";
                echo "</div>";
            } else {
                // Para campos estándar
                $fieldType = isset($formConfig[$fieldName]['type']) ? $formConfig[$fieldName]['type'] : 'text';
                $attributes = isset($formConfig[$fieldName]['attributes']) ? generateAttributes($formConfig[$fieldName]['attributes']) : '';

                echo "<div class='form-group'>";
                echo "<label for='$fieldName' class='form-label text-magenta-dark'>" . ucfirst(str_replace('_', ' ', $fieldName)) . "</label>";
                echo "<input type='$fieldType' name='$fieldName' id='$fieldName' $attributes>";
                echo "</div>";
            }
        }
        ?>

        <!-- Botones de navegación -->
        <div class="form-navigation">
            <button type="button" class="btn bg-indigo-dark text-white" id="prevBtn" style="display:none;"><i class="bi bi-chevron-double-left"></i> Anterior</button>
            <button type="button" class="btn bg-magenta-dark text-white" id="nextBtn">Siguiente <i class="bi bi-chevron-double-right"></i></button>
        </div>
    </form>
</div>

<script>
    // Función para mostrar el paso actual y actualizar la barra de progreso
    let currentStep = 1;
    const steps = document.querySelectorAll('.step');
    const progressBar = document.getElementById('progress');

    function showStep() {
        steps.forEach((step, index) => {
            if (index + 1 === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        const progress = (currentStep - 1) / (steps.length - 1) * 100;
        progressBar.style.width = progress + '%';

        // Mostrar/ocultar botones de navegación
        if (currentStep == 1) {
            document.getElementById('prevBtn').style.display = 'none';
        } else {
            document.getElementById('prevBtn').style.display = 'inline';
        }
        if (currentStep == steps.length) {
            document.getElementById('nextBtn').innerHTML = '<i class="bi bi-floppy-fill"></i> Guardar datos';

        } else {
            document.getElementById('nextBtn').innerHTML = 'Siguiente <i class="bi bi-chevron-double-right"></i>';
        }
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentStep < steps.length) {
            currentStep++;
            showStep();
        } else {
            document.getElementById('multi-step-form').submit(); // Enviar el formulario si llegamos al último paso
        }
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep();
        }
    });

    showStep(); // Mostrar el primer paso al cargar la página
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA98OpvjlfBwdRXdIVsGCyNM2ak5o-WYYs&libraries=places&callback=initAutocomplete" async defer></script>
<script>
    let autocomplete;

    // Función para inicializar el autocompletado
    function initAutocomplete() {
        // Crear el objeto del campo de texto
        const input = document.getElementById('direccion');

        // Inicializar Autocomplete con la API de Places
        autocomplete = new google.maps.places.Autocomplete(input);

        // Opcionalmente, se puede añadir un listener para manejar eventos, como el de selección de una dirección.
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            console.log(place); // Aquí obtienes los detalles de la dirección seleccionada.
        });
    }
</script>