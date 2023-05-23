<?php
include 'conexion.php';
require_once 'util/funciones.php';
require_once 'util/log.php';

// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $opcion = $_GET['opcion'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opcion = $_POST['opcion'];
}

writeLog("..:asignaciones_no_entregadas:..");
writeLog("Inicio de operacion op: " . $opcion);

try {
   
    if ($opcion == "CN_ASIGNACIONES") { //CONSULTA ASIGNACIONES

        $SQL = " select CAST(ID AS VARCHAR(15))  ID ,CAST(ID_CURSO AS VARCHAR(15)) ID_CURSO ,
                        TITULO ,INSTRUCCIONES ,FECHA_VENCIMIENTO 
                  from  APP_ASIGNACIONES A;
                ";

        // Ejecutar la sentencia
        $sentencia = $conexion->prepare($SQL);

        $sentencia->execute();

        $resultado = $sentencia->get_result();
        $num_filas = $resultado->num_rows;

        if ($num_filas > 0) {
            $filas = array();

            while ($fila = $resultado->fetch_assoc()) {
                $filas[] = $fila;
                // Convertir la fila a una cadena de texto
                $filaString = json_encode($fila);
            }

            // Crear respuesta
            $respuesta = array(
                'codResponse' => '00',
                'msjResponse' => 'TRANSACCIÓN OK',
                'data' => $filas
            );

        } else {
            // Crear respuesta
            $respuesta = array(
                'codResponse' => '02',
                'msjResponse' => 'NO HAY DATOS',
            );
        }
        $sentencia->close();
        $conexion->close();
    }

} catch (Exception $e) {
    // Capturar la excepción y devolver un mensaje de error
    $respuesta = array(
        'codResponse' => '99',
        'msjResponse' =>  $e->getMessage()
    );
    writeLog("Error: " .  $e->getMessage(), true);
}

devolver_respuesta($respuesta);
