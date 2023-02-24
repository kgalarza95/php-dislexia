<?php
include 'conexion.php';



// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $opcion = $_GET['opcion'];
    $id_usuario = $_GET['id_usuario'];
    $nombre = $_GET['nombre'];
    $descripcion = $_GET['descripcion'];
    $anio = $_GET['anio'];
    $estado = $_GET['estado'];

    $rol = $_GET['rol'];
    $cedula = $_GET['cedula'];
    $nombres = $_GET['nombres'];
    $apellidos = $_GET['apellidos'];
    $edad = $_GET['edad'];
    $usuario = $_GET['usuario'];
    $contrasenia = $_GET['contrasenia'];
    $sexo = $_GET['esMasculino'];
    $solicitarPass = $_GET['solicitarPass'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opcion = $_POST['opcion'];
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $anio = $_POST['anio'];
    $estado = $_POST['estado'];
}





try {

    if ($opcion == "IN") {

        $SQL = " SELECT COUNT(1) EXISTE
			FROM db_app_dislexia.app_curso C
			WHERE C.id_usuario  = ?
			AND NOMBRE           = ? ";


        // Ejecutar la sentencia
        $sentencia   = $conexion->prepare($SQL);
        $sentencia->bind_param('ss', $id_usuario, $nombre);
        $sentencia->execute();
        $resultado   = $sentencia->get_result();

        $fila = $resultado->fetch_assoc();

        $existe = $fila["EXISTE"];

        if ($existe == "0") {


            // Preparar las consultas para la inserción en ambas tablas
            $query1 = " INSERT INTO app_curso 
										   (ID, id_usuario, NOMBRE, DESCRIPCION, ANIO, ESTADO) 
									VALUES ((SELECT NVL(max(id),0)+1
											 FROM db_app_dislexia.app_curso C),
											 ?, ?, ?, ?, ?) ";


            // Preparar las sentencias
            $stmt1 = $conexion->prepare($query1);

            // Vincular los parámetros
            $stmt1->bind_param('issss', $id_usuario, $nombre, $descripcion, $anio, $estado);

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
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        } else {

            // Crear respuesta
            $respuesta = array(
                'codResponse' => '02',
                'msjResponse' => 'EL REGISTRO YA SE ENCUENTRA REGISTRADO',
            );

            // Devolver respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        }
        $sentencia->close();
        $conexion->close();
    }


    if ($opcion == "CN") {

        $SQL = " SELECT CAST(ID AS VARCHAR(10)) as ID, id_usuario, NOMBRE, DESCRIPCION, ANIO, ESTADO
				 FROM db_app_dislexia.app_curso 
				 where id_usuario = ? ";

        // Ejecutar la sentencia
        $sentencia = $conexion->prepare($SQL);

        $sentencia->bind_param('i', $id_usuario);
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

            // Devolver respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        } else {

            // Crear respuesta
            $respuesta = array(
                'codResponse' => '02',
                'msjResponse' => 'NO HAY DATOS',
            );

            // Devolver respuesta en formato JSON
            header('Content-Type: application/json');
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        }
        $sentencia->close();
        $conexion->close();
    }
} catch (Exception $e) {
    // Capturar la excepción y devolver un mensaje de error
    header('Content-Type: application/json');
    echo json_encode(array(
        'codResponse' => '99',
        'msjResponse' => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
