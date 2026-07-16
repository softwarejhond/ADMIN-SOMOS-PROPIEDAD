<?php
//datos del servidor
$server = "db";
$username = "root";
$password = "root";
$bd = "somos_propiedad";
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
