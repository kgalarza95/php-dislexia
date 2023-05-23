<?php
$hostname = 'localhost';
$database = 'db_app_dislexia';
$username = 'root';
$password = '';

$conexion = new mysqli($hostname, $username, $password, $database);

// Configurar la conexión con el conjunto de caracteres UTF-8
##$conexion->set_charset("utf8");
mysqli_set_charset($conexion, "utf8");

if ($conexion->connect_errno) {
    echo "Lo sentimos, el sitio web está experimentando problemas.";
}
