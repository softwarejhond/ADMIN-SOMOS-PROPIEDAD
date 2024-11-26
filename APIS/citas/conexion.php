<?php
/*Datos de conexion a la base de datos*/

$db_host = "localhost";
$db_user = "u420923412_L0g1nD4t4B4S3";
$db_pass = "u420923412_L0g1nD4t4B4S3";
$db_name = "u420923412_S0m0sProp13D4d";
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
date_default_timezone_set('America/Bogota');
mysqli_set_charset($conn, 'utf8'); //Muy importante esta linea, guardara el contenido que contenga acentos de manera correcta configurando la bd con el UTF-8 spanis ci
if (mysqli_connect_errno()) {
    echo 'No se pudo conectar a la base de datos : ' . mysqli_connect_error();
}