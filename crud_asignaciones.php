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

writeLog("..:crud_asignaciones:..");
writeLog("Inicio de operacion op: " . $opcion);

try {
    if ($opcion == "IN") {

        $conexion->set_charset("utf8");

        $id_curso = $_POST['id_curso'];
        $titulo = $_POST['titulo'];
        $instrucciones = $_POST['instrucciones'];
        $fecha_vencimiento = $_POST['fecha_vencimiento'];

        $SQL1 = " INSERT INTO app_asignaciones (id_curso, titulo, instrucciones, fecha_vencimiento)
                  VALUES (?, ?, ?, STR_TO_DATE(?, '%d/%m/%Y')) ";

        #echo "fecha_vencimiento:=======> ".$fecha_vencimiento;

        // Preparar las sentencias
        $stmt1 = $conexion->prepare($SQL1);
        // Vincular los parámetros
        $stmt1->bind_param('isss', $id_curso, $titulo, $instrucciones, $fecha_vencimiento);
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

    if ($opcion == "CN") { //CONSULTA
        $SQL = " SELECT * FROM app_asignaciones ";

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


    if ($opcion == "CC") { //CONSULTA CURSOS DEL PROFESOR 
        $SQL = " SELECT CAST(ID AS VARCHAR(10)) AS ID, 
                        ID_PROFESOR, NOMBRE, DESCRIPCION, ANIO, ESTADO 
                FROM db_app_dislexia.app_curso 
                WHERE ID_PROFESOR = ?

                ";

        // Preparar la sentencia
        $sentencia = $conexion->prepare($SQL);
        $sentencia->bind_param('i', $idProfesor);

        // Ejecutar la sentencia
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


    if ($opcion == "EN") {
        $conexion->set_charset("utf8");

        $idTarea = $_POST['id_tarea'];
        $idEstudiante = $_POST['id_estudiante'];
        $entrega = $_POST['entrega'];
        $fechaEntrega = $_POST['fecha_entrega'];
        $calificacion = $_POST['calificacion'];

        $ruta = "";

        if (isset($_POST['nombre'])) {
            $base64_string = $_POST['encodedFile'];
            $file_name = $_POST['nombre']; // Especifica el nombre que quieres que tenga el archivo guardado en el servidor
            $ruta_general = "reapldos_archivos/";
            $ruta_absoluta = __DIR__;

            // Decodifica la cadena base64 y guarda el archivo en el servidor
            $decoded = base64_decode($base64_string);
            $file = fopen($ruta_absoluta . "/reapldos_archivos/" . $file_name, 'wb');
            fwrite($file, $decoded);
            fclose($file);

            $ruta = $ruta_absoluta . "/reapldos_archivos/" . $file_name;
        }

        // Preparar las consultas para la inserción en ambas tablas
        $query1 = " INSERT INTO app_entrega_asignacion (id_tarea, id_estudiante, entrega, fecha_entrega, calificacion, ruta_file_tarea)
                    VALUES (?, ?, ?, STR_TO_DATE(?, '%d/%m/%Y'), ?, ?)
                     ON DUPLICATE KEY 
                     UPDATE entrega = ?,
                             fecha_entrega =  STR_TO_DATE(?, '%d/%m/%Y')
        
                  ";

        /* INSERT INTO app_entrega_asignacion (id_tarea, id_estudiante, entrega, fecha_entrega, calificacion)
                    VALUES (?, ?, ?, STR_TO_DATE(?, '%d/%m/%Y'), ?)  */


        // Preparar las sentencias
        $stmt1 = $conexion->prepare($query1);

        // Vincular los parámetros con los marcadores de posición
        $stmt1->bind_param(
            "iissssss",
            $idTarea,
            $idEstudiante,
            $entrega,
            $fechaEntrega,
            $calificacion,
            $ruta,
            $entrega,
            $fechaEntrega
        );


        // Ejecutar las consultas
        $stmt1->execute();


        // Cerrar las sentencias
        $stmt1->close();


        // Crear respuesta
        $respuesta = array(
            'codResponse' => '00',
            'msjResponse' => 'TRANSACCIÓN OK'
        );

        // Devolver respuesta en formato JSON
        header('Content-Type: application/json');
        //echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    }



    if ($opcion == "CNA") { //CONSULTA ASIGNACIONES

        $SQL = " SELECT CAST(E.ID_TAREA AS VARCHAR(15)) ID, E.ENTREGA, E.FECHA_ENTREGA, E.CALIFICACION,
                        A.TITULO, 
                        A.FECHA_VENCIMIENTO, 
                        CONCAT(P.NOMBRES, ' ', P.APELLIDOS) AS NOMBRES,
                        NVL(e.RUTA_FILE_TAREA, ' ') RUTA_FILE_TAREA
                FROM DB_APP_DISLEXIA.APP_ENTREGA_ASIGNACION  E
                JOIN APP_ASIGNACIONES A ON A.ID= E.ID_TAREA
                JOIN APP_DATOS_PERSONALES P ON P.ID = E.ID_ESTUDIANTE  
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
                writeLog("filaString: " . $filaString);
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


    if ($opcion == "CALIF") {
        $conexion->set_charset("utf8");

        $calificacion = $_POST['calificacion'];
        $id_tarea = $_POST['id_tarea'];


        $SQL1 = " UPDATE APP_ENTREGA_ASIGNACION
                  set CALIFICACION = ? 
                  where ID_TAREA = ?  ";

        #echo "fecha_vencimiento:=======> ".$fecha_vencimiento;

        // Preparar las sentencias
        $stmt1 = $conexion->prepare($SQL1);
        // Vincular los parámetros
        $stmt1->bind_param('ii', $calificacion, $id_tarea);
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
} catch (Exception $e) {
    // Capturar la excepción y devolver un mensaje de error
    $respuesta = array(
        'codResponse' => '99',
        'msjResponse' =>  $e->getMessage()
    );
    writeLog("Error: " .  $e->getMessage(), true);
}

devolver_respuesta($respuesta);
