<?php 
require_once 'conexion.php';

function getDepartamento()
{
  $mysqli = getConn();
  $query = 'SELECT * FROM `departamentos` ORDER BY departamento ';
  $result = $mysqli->query($query);
  $departamento = '<option value="0">Seleccionar departamento</option>';
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    // $departamento .= "<option value='$row[id_departamento]'>$row[departamento]</option>";
    $departamento .= "<option value='$row[id_departamento]'>$row[departamento]</option>";
  }
  return $departamento;
}
echo getDepartamento();
?>