<?php
include 'conexion.php';


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

        $SQL = " SELECT MAX(ID) +1 ID_NEXT
		         FROM db_app_dislexia.app_mensaje_relacion R ";


        // Ejecutar la sentencia
        $sentencia   = $conexion->prepare($SQL);
        $sentencia->execute();
        $resultado   = $sentencia->get_result();

        $fila = $resultado->fetch_assoc();

        $id_next = $fila["ID_NEXT"];

        // Preparar las consultas para la inserción en ambas tablas
        $SQL1 = " INSERT INTO app_mensaje_relacion (ID, ID_PERSONA1,ID_PERSONA2) VALUES(?,?,?)";

        // Preparar las sentencias
        $stmt1 = $conexion->prepare($SQL1);
        // Vincular los parámetros
        $stmt1->bind_param('sss', $id_next, $id_persona1, $id_persona2);
        // Ejecutar las consultas
        $stmt1->execute();
        // Cerrar las sentencias
        $stmt1->close();


        $id_duenio = $_POST['id_duenio'];
        $mensaje = $_POST['mensaje'];

        // Preparar las consultas para la inserción en ambas tablas
        $SQL2 = " INSERT INTO app_mensaje (ID_MENSAJE_RELACION, ID_DUENIO_SMS, MENSAJE)  VALUES(?,?,?)";

        // Preparar las sentencias
        $stmt2 = $conexion->prepare($SQL2);
        // Vincular los parámetros
        $stmt2->bind_param('sss', $id_next, $id_duenio, $mensaje);
        // Ejecutar las consultas
        $stmt2->execute();
        // Cerrar las sentencias
        $stmt2->close();

        // Crear respuesta
        $respuesta = array(
            'codResponse' => '00',
            'msjResponse' => 'TRANSACCIÓN OK'
        );


        $sentencia->close();
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
} catch (Exception $e) {
    // Capturar la excepción y devolver un mensaje de error
    $respuesta = array(
        'codResponse' => '99',
        'msjResponse' =>  $e->getMessage()
    );
}


// Devolver respuesta en formato JSON
header('Content-Type: application/json');

function recursive_utf8_encode($input)
{
    if (is_array($input)) {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = recursive_utf8_encode($value);
            } elseif (is_string($value)) {
                $value = utf8_encode($value);
            }
        }
        unset($value);
    } elseif (is_string($input)) {
        $input = utf8_encode($input);
    }
    return $input;
}


$respuesta = recursive_utf8_encode($respuesta);

$json = json_encode($respuesta, JSON_UNESCAPED_UNICODE);

echo $json;
//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
