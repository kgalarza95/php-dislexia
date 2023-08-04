<?php

include 'conexion.php';
require_once 'util/funciones.php';
require_once 'util/log.php';

// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

writeLog("..:cons_estudiantes:..");

$cursoId = $_GET['curso_id'];
writeLog("Estudiante por curso: " . $cursoId);
// Consulta de estudiantes por curso
$estudiantes = array();

$query = "SELECT P.ID, E.USUARIO, P.NOMBRES, P.APELLIDOS, P.SEXO
            FROM APP_USUARIOS_SISTEMA E
            JOIN APP_DATOS_PERSONALES P ON P.ID_USUARIO = E.ID
            JOIN APP_ROL R ON R.ID = P.ID_ROL AND R.ID = 3
            JOIN APP_ESTUD_X_CURSO X ON X.ID_ESTUDIANTE = E.ID
            where X.ID_CURSO = $cursoId";


$resultado = $conexion->query($query);

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $estudiantes[] = $row;
    }
}

// Cerrar la conexión
$conexion->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($estudiantes);
writeLog("respuesta ok: " . $cursoId);
?>
