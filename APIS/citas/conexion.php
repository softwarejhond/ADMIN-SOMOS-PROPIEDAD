<?php
/*Datos de conexion a la base de datos*/

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "somos_propiedad";
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
date_default_timezone_set('America/Bogota');
mysqli_set_charset($conn, 'utf8'); //Muy importante esta linea, guardara el contenido que contenga acentos de manera correcta configurando la bd con el UTF-8 spanis ci
if (mysqli_connect_errno()) {
    echo 'No se pudo conectar a la base de datos : ' . mysqli_connect_error();
}