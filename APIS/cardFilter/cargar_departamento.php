<?php
require_once 'conexion.php';

function getDepartamento()
{
  $mysqli = getConn();
  $query = 'SELECT * FROM `departamentos` ORDER BY departamento';
  $result = $mysqli->query($query);
  $html = '<option value="">Seleccionar</option>';
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $html .= "<option value='{$row['id_departamento']}'>{$row['departamento']}</option>";
  }
  return $html;
}

echo getDepartamento();
?>