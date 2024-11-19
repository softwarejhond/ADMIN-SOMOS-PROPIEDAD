<?php
require('conexion.php');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Página actual
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Número de resultados por página

// Verificar otros parámetros de filtro
$tipoInmueble = isset($_GET['tipoInmueble']) ? $_GET['tipoInmueble'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$habitaciones = isset($_GET['habitaciones']) ? $_GET['habitaciones'] : '';
$piso = isset($_GET['piso']) ? $_GET['piso'] : '';
$municipios = isset($_GET['municipios']) ? $_GET['municipios'] : '';
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';
// Calcular el offset basado en la página actual y el límite
$offset = ($page - 1) * $limit;

// Construir la consulta SQL base con paginación
$sql = "SELECT * FROM proprieter 
        INNER JOIN municipios ON proprieter.Municipio = municipios.id_municipio 
        WHERE estadoPropietario = 'ACTIVO' AND nombre_inquilino = '' ";

// Aplicar filtros si se han proporcionado
if (!empty($tipoInmueble)) {
    $sql .= " AND tipoInmueble = '$tipoInmueble'";
}
if (!empty($estado)) {
    $sql .= " AND condicion = '$estado'";
}
if (!empty($habitaciones)) {
    $sql .= " AND alcobas = '$habitaciones'";
}
if (!empty($piso)) {
    $sql .= " AND nivel_piso = '$piso'";
}
if (!empty($municipios)) {
    $sql .= " AND proprieter.Municipio = '$municipios'";
}
if (!empty($codigo)) {
    $sql .= " AND codigo = '$codigo'";
}
// Agregar aleatoriedad a la consulta
$sql .= " ORDER BY RAND() LIMIT $limit OFFSET $offset";

// Ejecutar la consulta SQL
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
    echo json_encode($response);
} else {
    echo json_encode(array('message' => 'No se encontraron resultados.'));
}

