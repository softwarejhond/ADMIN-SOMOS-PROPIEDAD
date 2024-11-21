    <?php

    // Consulta SQL para obtener los tipos de vivienda
    $queryTipo = "SELECT nombre_tipo FROM tipos";  // Asumiendo que la tabla 'tipos' tiene una columna 'tipo'
    $resultTipo = mysqli_query($conn, $queryTipo);

    // Verificar si la consulta tiene resultados
    if (mysqli_num_rows($resultTipo) > 0) {
        while ($row = mysqli_fetch_assoc($resultTipo)) {
            $tipo = $row['nombre_tipo'];
            $id = strtolower(str_replace(' ', '_', $tipo)); // Genera un id único

            // Mostrar cada tipo de vivienda como un radio button
            echo '
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipoInmueble" id="' . $id . '" value="' . ucfirst($tipo) . '" style="text-transform: capitalize;">
            <h6 class="form-check-h6" for="' . $id . '">' . ucfirst($tipo) . '</h6>
        </div>';
        }
    } else {
        echo 'No se encontraron tipos de vivienda en la base de datos.';
    }
    ?>