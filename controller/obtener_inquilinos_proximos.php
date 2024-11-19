<?php
include("conexion.php"); // Incluye el archivo de conexión

function obtenerInquilinosProximos()
{
    global $conn; // Asegúrate de que $conn sea accesible dentro de la función

    $fecha_actual = date("Y-m-d");
    $fecha15Dias = date('Y-m-d', strtotime($fecha_actual . ' + 15 days'));

    $sqlContar = "SELECT COUNT(*) AS total_registros FROM retiredTenants WHERE fechaRetiro BETWEEN '$fecha_actual' AND '$fecha15Dias'";
    $resultContar = mysqli_query($conn, $sqlContar);

    if (!$resultContar) {
        error_log("Error en la consulta de conteo: " . mysqli_error($conn));
        echo json_encode(['error' => "Error en la consulta."]);
        exit;
    }

    $totalRegistros = 0;
    if (mysqli_num_rows($resultContar) > 0) {
        $row = mysqli_fetch_assoc($resultContar);
        $totalRegistros = $row['total_registros'];
    }

    $sql = "SELECT IdInquilino, NombreInquilino, codigoPropiedad FROM retiredTenants WHERE fechaRetiro BETWEEN '$fecha_actual' AND '$fecha15Dias'";
    $resultado = mysqli_query($conn, $sql);

    $html = '';
    if (mysqli_num_rows($resultado) > 0) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $html .= '<a class="dropdown-item" href="#" title="Código propiedad: ' . htmlspecialchars($fila["codigoPropiedad"]) . '">'
                . 'ID: ' . htmlspecialchars($fila["IdInquilino"]) . ' - ' . htmlspecialchars($fila["NombreInquilino"])
                . '</a>';
        }
    } else {
        $html = "<div class='dropdown-item text-muted'>No hay inquilinos por retirar</div>";
    }

    echo json_encode([
        'html' => $html,
        'totalRegistros' => $totalRegistros
    ]);
}

obtenerInquilinosProximos();
