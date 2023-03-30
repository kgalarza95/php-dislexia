<?php
include 'conexion.php';
require_once 'util/funciones.php';

// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $opcion = $_GET['opcion'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opcion = $_POST['opcion'];
}


try {

    if ($opcion == "IN") {

        $id_usuario = $_POST['id_usuario'];
        $score = $_POST['score'];
        $id_juego = $_POST['id_juego'];

        //echo "score ======>  ".$score;
        $SQL1 = " INSERT INTO app_score (id_usuario, score, id_juego)
                  VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE score = CASE WHEN score < ? THEN ? ELSE score END 
                ";

        // Preparar las sentencias
        $stmt1 = $conexion->prepare($SQL1);
        // Vincular los parámetros
        $stmt1->bind_param('siiii', $id_usuario, $score, $id_juego, $score, $score);
        // Ejecutar las consultas
        $stmt1->execute();
        // Cerrar las sentencias
        $stmt1->close();

        // Crear respuesta
        $respuesta = array(
            'codResponse' => '00',
            'msjResponse' => 'TRANSACCIÓN OK'
        );

        $conexion->close();
    }


    if ($opcion == "CP") { //CONSULTA

        $SQL = " SELECT CAST(ID AS VARCHAR(10)) as ID, PALABRA_INC, PALABRA, OP1, OP2, OP3, OP4, OP_CORRECTA
                 FROM app_jg_palabras
                
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

    if ($opcion == "CF") { //CONSULTA

        $SQL = " SELECT CAST(ID AS VARCHAR(10)) as ID, FRASE, PALABRA_ERRADA, PALABRA_CORRECTA
                 FROM app_jg_farses
                
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

    if ($opcion == "SC") { //CONSULTA

       /* $SQL = " SELECT CAST(U.ID AS VARCHAR(10)) as ID, U.USUARIO,  
                        CONCAT(D.NOMBRES, ' ', D.APELLIDOS) AS NOMBRES, 
                        S.SCORE
                FROM APP_SCORE S
                JOIN APP_USUARIOS_SISTEMA U ON U.ID = S.ID_USUARIO
                JOIN APP_DATOS_PERSONALES D ON D.ID_USUARIO = U.ID
                "; */

        
        $SQL = "    SELECT CAST(U.ID AS VARCHAR(10)) as ID, U.USUARIO,  
                            CONCAT(D.NOMBRES, ' ', D.APELLIDOS) AS NOMBRES, 
                            S.SCORE,
                            S.ID_JUEGO,
                            J.NOMBRE_JUEGO
                    FROM APP_SCORE S
                    JOIN APP_USUARIOS_SISTEMA U ON U.ID = S.ID_USUARIO
                    JOIN APP_DATOS_PERSONALES D ON D.ID_USUARIO = U.ID
                    JOIN APP_LISTA_JUEGOS J ON J.ID = S.ID_JUEGO
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
}


devolver_respuesta($respuesta);
