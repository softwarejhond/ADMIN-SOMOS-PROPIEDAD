<?php
require('conexion.php');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Página actual
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Número de resultados por página

// Verificar otros parámetros de filtro
$tipoInmueble = isset($_GET['tipoInmueble']) ? mysqli_real_escape_string($conn, $_GET['tipoInmueble']) : '';
$estado = isset($_GET['estado']) ? mysqli_real_escape_string($conn, $_GET['estado']) : '';
$habitaciones = isset($_GET['habitaciones']) ? mysqli_real_escape_string($conn, $_GET['habitaciones']) : '';
$piso = isset($_GET['piso']) ? mysqli_real_escape_string($conn, $_GET['piso']) : '';
$municipios = isset($_GET['municipios']) ? mysqli_real_escape_string($conn, $_GET['municipios']) : '';
$codigo = isset($_GET['codigo']) ? mysqli_real_escape_string($conn, $_GET['codigo']) : '';

// Calcular el offset basado en la página actual y el límite
$offset = ($page - 1) * $limit;

// Construir la consulta SQL base con paginación
$sql = "SELECT proprieter.*, 
        GROUP_CONCAT(fotos.nombre_foto) AS fotos
        FROM proprieter 
        LEFT JOIN municipios ON proprieter.Municipio = municipios.id_municipio
        LEFT JOIN fotos ON fotos.codigoPropiedad = proprieter.codigo 
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
    $sql .= " AND proprieter.codigo = '$codigo'";
}

// Agregar aleatoriedad a la consulta
$sql .= " GROUP BY proprieter.codigo ORDER BY RAND() LIMIT $limit OFFSET $offset";

// Ejecutar la consulta SQL
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $response = array();
    while ($row = $result->fetch_assoc()) {
        // Si hay fotos, convertirlas en un array
        $row['fotos'] = !empty($row['fotos']) ? explode(',', $row['fotos']) : [];
        $response[] = $row;
    }
    echo json_encode($response);
} else {
    echo json_encode(array('message' => 'No se encontraron resultados.'));
}
?>
