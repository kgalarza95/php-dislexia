<?php

include 'conexion.php';
require_once 'util/funciones.php';
require_once 'util/log.php';


// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

writeLog("..:save_score:..");
writeLog("Inicio de operación");

try {
    echo 'hola mundo';

    // Crear respuesta
    $respuesta = array(
        'codResponse' => '00',
        'msjResponse' => 'TRANSACCIÓN OK'
    );
} catch (Exception $e) {
    // Capturar la excepción y devolver un mensaje de error
    /* header('Content-Type: application/json');
	echo json_encode(array(
		'codResponse' => '99',
		'msjResponse' => $e->getMessage()
	), JSON_UNESCAPED_UNICODE); */

    $respuesta = array(
        'codResponse' => '99',
        'msjResponse' =>  $e->getMessage()
    );
    writeLog("Error: " .  $e->getMessage(), true);
}



devolver_respuesta($respuesta);
