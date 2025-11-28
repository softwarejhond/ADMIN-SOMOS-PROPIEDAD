<?php
require_once 'conexion.php';

header('Content-Type: application/json');

function getDepartamento()
{
  $mysqli = getConn();
  $query = 'SELECT * FROM `departamentos` ORDER BY departamento ';
  $result = $mysqli->query($query);
  $departamentos = array();
  
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $departamentos[] = array(
      'id_departamento' => $row['id_departamento'],
      'departamento' => $row['departamento']
    );
  }
  return $departamentos;
}

echo json_encode(getDepartamento());
?>