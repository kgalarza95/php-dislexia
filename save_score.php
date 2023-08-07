<?php

include 'conexion.php';
require_once 'util/funciones.php';
require_once 'util/log.php';


// Configuración de las cabeceras HTTP para permitir CORS
header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");

writeLog("..:save_score:..");
writeLog("Inicio de operación");

try {
  
    $data = json_decode(file_get_contents('php://input'), true);

    writeLog("entrada: " . json_encode($data));
    $idEstudiante = isset($data['id_estudiante']) ? $data['id_estudiante'] : '';
    $curso = isset($data['curso']) ? $data['curso'] : '';
    $unidad = isset($data['unidad']) ? $data['unidad'] : '';
    $juego = isset($data['juego']) ? $data['juego'] : '';
    $puntaje = isset($data['puntaje']) ? $data['puntaje'] : '';
    $mensajeResp;

    if ($idEstudiante != '' && $curso != '' && $unidad != '' && $juego != '') {
        // Preparar la sentencia SQL
        $sql = "INSERT INTO app_historial_juego (ID_ESTUDIANTE, CURSO, UNIDAD, JUEGO, PUNTAJE) 
            VALUES ('$idEstudiante', '$curso', '$unidad', '$juego', '$puntaje')";

        if ($conexion && $conexion->query($sql) === TRUE) {
            $mensajeResp = "TRANSACCIÓN OK";
        } else {
            $mensajeResp = "Error al insertar datos: " . $conexion->error;
        }

        // Crear respuesta
        $respuesta = array(
            'codResponse' => '00',
            'msjResponse' => $mensajeResp
        );
    } else {
        $respuesta = array(
            'codResponse' => '99',
            'msjResponse' => 'Faltan datos'
        );
    }
} catch (Exception $e) {
    $respuesta = array(
        'codResponse' => '99',
        'msjResponse' =>  $e->getMessage()
    );
    writeLog("Error: " .  $e->getMessage(), true);
}

devolver_respuesta($respuesta);
