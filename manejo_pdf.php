<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if (isset($_POST['opcion'])) {

        if ($_POST['opcion'] == "CN") {
            $SQL = " SELECT * 
                     FROM db_app_dislexia.app_material_x_curso ";

            // Ejecutar la sentencia
            $sentencia   = $conexion->prepare($SQL);
            //$sentencia->bind_param('s', $id_curso);
            $sentencia->execute();
            $resultado   = $sentencia->get_result();

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
        } else if ($_POST['opcion'] == "IN") {

            if (isset($_POST['pdf'])) {
                $base64_string = $_POST['pdf'];
                $file_name = $_POST['nombre_pdf']; // Especifica el nombre que quieres que tenga el archivo guardado en el servidor
                $ruta_general = "reapldos_archivos/";
                $ruta_absoluta = __DIR__;

                // Decodifica la cadena base64 y guarda el archivo en el servidor
                $decoded = base64_decode($base64_string);
                $file = fopen($ruta_absoluta . "/reapldos_archivos/" . $file_name, 'wb');
                fwrite($file, $decoded);
                fclose($file);


                $ruta_guardar = $ruta_absoluta . "/reapldos_archivos/";

                // Preparar las consultas para la inserción en ambas tablas
                $query1 = " INSERT INTO app_material_x_curso 
                             (ID_CURSO,NOMBRE,RUTA) 
                            values(1,?,?) ";

                // Preparar las sentencias
                $stmt1 = $conexion->prepare($query1);
                // Vincular los parámetros
                $stmt1->bind_param('ss', $file_name, $ruta_guardar);
                // Ejecutar las consultas
                $stmt1->execute();
                // Cerrar las sentencias
                $stmt1->close();
                //echo 'Archivo guardado exitosamente';

                // Crear respuesta
                $respuesta = array(
                    'codResponse' => '00',
                    'msjResponse' => 'TRANSACCIÓN OK',
                );
            } else {
                // echo 'No se ha enviado ningún archivo';
                // Crear respuesta
                $respuesta = array(
                    'codResponse' => '02',
                    'msjResponse' => 'NO SE HA ENVIADO NINGÚN ARCHIVO.',
                );
            }
        } else if ($_POST['opcion'] == "DE") { //DESCARGAR


            $archivo = $_POST['ruta_file'];

            // Leer el archivo PDF
            $data = file_get_contents($archivo);

            // Convertir los datos a base64
            $base64 = base64_encode($data);

            // Crear respuesta
            $respuesta = array(
                'codResponse' => '00',
                'msjResponse' => 'TRANSACCIÓN OK',
                'data' => $base64
            );
        }


        //quitar despues de que se inserte
        if ($_POST['opcion'] == "CN") {
            $sentencia->close();
            $conexion->close();
        }
    } else {
        $respuesta = array(
            'codResponse' => '02',
            'msjResponse' => 'OPCIÓN NO CONFIGURADA.',
        );
    }
} else {
    //echo 'Método de solicitud no válido';
    // Crear respuesta
    $respuesta = array(
        'codResponse' => '02',
        'msjResponse' => 'MÉTODO DE SOLICITUD NO VÁLIDO.',
    );
}



// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
