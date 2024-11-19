<?php
function getConn(): mysqli
{
	$host = 'localhost';
	$user = 'u420923412_L0g1nD4t4B4S3';
	$password = 'u420923412_L0g1nD4t4B4S3';
	$database = 'u420923412_S0m0sProp13D4d';

	$mysqli = new mysqli($host, $user, $password, $database);

	if ($mysqli->connect_errno) {
		throw new RuntimeException("Error al conectar la base de datos: " . $mysqli->connect_error);
	}

	$mysqli->set_charset('utf8');

	return $mysqli;
}
