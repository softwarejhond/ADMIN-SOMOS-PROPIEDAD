<?php
require_once 'conexion.php';

header('Content-Type: application/json');

function getMunicipios($departamento_id)
{
  $mysqli = getConn();
  $departamento_id = $mysqli->real_escape_string($departamento_id);
  $query = "SELECT * FROM `municipios` WHERE id_departamento = '$departamento_id' ORDER BY municipio";
  $result = $mysqli->query($query);
  $municipios = array();
  
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $municipios[] = array(
      'id_municipio' => $row['id_municipio'],
      'municipio' => $row['municipio']
    );
  }
  return $municipios;
}

$departamento_id = isset($_GET['departamento_id']) ? $_GET['departamento_id'] : '';

if ($departamento_id) {
    echo json_encode(getMunicipios($departamento_id));
} else {
    echo json_encode(array());
}
?>