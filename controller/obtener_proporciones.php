<?php
// obtener_proporciones.php
include("conexion.php"); // Incluye el archivo de conexión

function obtenerProporcionesPropiedades()
{
    global $conn;

    $data = [
        'total_en_venta' => 0,
        'total_en_arriendo' => 0,
        'total_alquiler_o_venta' => 0,
        'total_propiedades' => 0,
        'porcentaje_en_venta' => 0,
        'porcentaje_en_arriendo' => 0,
        'porcentaje_alquiler_o_venta' => 0,
    ];

    // Consulta para contar propiedades en venta
    $queryEnVenta = "SELECT COUNT(*) as total_en_venta FROM proprieter WHERE condicion = 'en venta' AND estadoPropietario='ACTIVO'";
    $resultEnVenta = mysqli_query($conn, $queryEnVenta);
    if ($resultEnVenta) {
        $dataEnVenta = mysqli_fetch_assoc($resultEnVenta);
        $data['total_en_venta'] = (int)$dataEnVenta['total_en_venta'];
    }

    // Consulta para contar propiedades en arriendo
    $queryEnArriendo = "SELECT COUNT(*) as total_en_arriendo FROM proprieter WHERE condicion = 'en alquiler' AND estadoPropietario='ACTIVO'";
    $resultEnArriendo = mysqli_query($conn, $queryEnArriendo);
    if ($resultEnArriendo) {
        $dataEnArriendo = mysqli_fetch_assoc($resultEnArriendo);
        $data['total_en_arriendo'] = (int)$dataEnArriendo['total_en_arriendo'];
    }

    // Consulta para contar propiedades en alquiler o venta
    $queryAlquilerOVenta = "SELECT COUNT(*) as total_alquiler_o_venta FROM proprieter WHERE condicion = 'alquiler o venta' AND estadoPropietario='ACTIVO'";
    $resultAlquilerOVenta = mysqli_query($conn, $queryAlquilerOVenta);
    if ($resultAlquilerOVenta) {
        $dataAlquilerOVenta = mysqli_fetch_assoc($resultAlquilerOVenta);
        $data['total_alquiler_o_venta'] = (int)$dataAlquilerOVenta['total_alquiler_o_venta'];
    }

    // Consulta para contar el total de propiedades
    $queryTotal = "SELECT COUNT(*) as total_propiedades FROM proprieter WHERE estadoPropietario='ACTIVO'";
    $resultTotal = mysqli_query($conn, $queryTotal);
    if ($resultTotal) {
        $dataTotal = mysqli_fetch_assoc($resultTotal);
        $data['total_propiedades'] = (int)$dataTotal['total_propiedades'];
    }

    // Calcular los porcentajes
    if ($data['total_propiedades'] > 0) {
        $data['porcentaje_en_venta'] = ($data['total_en_venta'] / $data['total_propiedades']) * 100;
        $data['porcentaje_en_arriendo'] = ($data['total_en_arriendo'] / $data['total_propiedades']) * 100;
        $data['porcentaje_alquiler_o_venta'] = ($data['total_alquiler_o_venta'] / $data['total_propiedades']) * 100;
    }

    header('Content-Type: application/json');
    echo json_encode($data);
}
obtenerProporcionesPropiedades();
