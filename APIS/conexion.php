<?php
//datos del servidor
$server = "localhost";
$username = "u420923412_L0g1nD4t4B4S3";
$password = "u420923412_L0g1nD4t4B4S3";
$bd = "u420923412_S0m0sProp13D4d";
//creamos una conexión
$conn = mysqli_connect($server, $username, $password, $bd);
//Chequeamos la conexión
if (!$conn) {
	die("Conexión fallida:" . mysqli_connect_error());
}

//Chequeamos la conexión
if (!$conn) {
	die("Conexión fallida:" . mysqli_connect_error());
}