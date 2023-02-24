
<?php
include 'conexion.php';

// Obtener método de la solicitud
$metodo = $_SERVER['REQUEST_METHOD'];

// Procesar la solicitud según el método
switch ($metodo) {
    case 'GET':
        // Consulta SELECT
        $consulta = "SELECT * FROM app_usuarios_sistema";
        $resultado = mysqli_query($conexion, $consulta);

        // Crear arreglo de usuarios
        $usuarios = array();
        if (mysqli_num_rows($resultado) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $usuarios[] = $fila;
            }
        }

        // Devolver respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($usuarios);
        break;
    default:
        // Devolver error 405 (Método no permitido)
        header("HTTP/1.1 405 Method Not Allowed");
        break;
}

// Cerrar conexión
mysqli_close($conexion);
?>
