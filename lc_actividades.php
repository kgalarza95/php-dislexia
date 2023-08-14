<?php

include 'conexion.php';
require_once 'util/funciones.php';
require_once 'util/log.php';

// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$estudianteId = $_GET['estudiante_id'];
$unidad = $_GET['idUnidad'];

$actividad = $_GET['actividadId'];


// Consulta de actividades y puntajes por estudiante
$actividades = array();

/* $query = "SELECT id , id_estudiante , curso , unidad , juego , puntaje , fecha  
          FROM app_historial_juego WHERE id_estudiante = $estudianteId
          union
          select a.id, e.id_estudiante, a.id_curso,'Tarea'unidad, a.titulo, e.calificacion,
                    e.fecha_entrega 
          from app_asignaciones a
          join APP_ENTREGA_ASIGNACION e on e.id_tarea = a.id
          where e.id_estudiante = $estudianteId "; */


          $query = " select *
          from(
                  SELECT id , id_estudiante , curso , UPPER(unidad) unidad , UPPER(juego) juego , puntaje , fecha  
                  FROM app_historial_juego 
                  union
                  select a.id, e.id_estudiante, a.id_curso,'TAREA' unidad, UPPER(a.titulo) titulo, e.calificacion,
                            e.fecha_entrega 
                    from app_asignaciones a
                    join APP_ENTREGA_ASIGNACION e on e.id_tarea = a.id
    
          ) a
          WHERE id_estudiante = $estudianteId
          and juego like '%$actividad%'
          and unidad like '%$unidad%' ";


$resultado = $conexion->query($query);

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $actividades[] = $row;
    }
}

// Cerrar la conexión
$conexion->close();

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($actividades);

?>
