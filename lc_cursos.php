<?php

header("Access-Control-Allow-Origin: *"); // Permite solicitudes desde cualquier origen
header("Content-Type: application/json");

include 'conexion.php';
require_once 'util/funciones.php';
require_once 'util/log.php';


// Consulta de cursos
$cursos = array();

$query = "SELECT CAST(ID AS VARCHAR(10)) as ID, ID_PROFESOR, NOMBRE, DESCRIPCION, ANIO, ESTADO
FROM db_app_dislexia.app_curso 
where ID_PROFESOR = 3";

$resultado = $conexion->query($query);

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $cursos[] = $row;
    }
}

// Cerrar la conexión
$conexion->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($cursos);

?>
