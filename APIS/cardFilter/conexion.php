<?php
function getConn(): mysqli
{
	$host = 'localhost';
	$user = 'root';
	$password = '';
	$database = 'somos_propiedad';

	$mysqli = new mysqli($host, $user, $password, $database);

	if ($mysqli->connect_errno) {
		throw new RuntimeException("Error al conectar la base de datos: " . $mysqli->connect_error);
	}

	$mysqli->set_charset('utf8');

	return $mysqli;
}
