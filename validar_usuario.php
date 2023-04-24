<?php
include 'conexion.php';
require_once 'util/funciones.php';
/*
*/


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$usu_usuario = $_GET['usuario'];
	$usu_password = $_GET['password'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$usu_usuario = $_POST['usuario'];
	$usu_password = $_POST['password'];
}

//$sentencia=$conexion->prepare("SELECT * FROM usuario WHERE usu_usuario=? AND usu_password=?");

try {

	$SQL = " SELECT CAST(U.ID AS VARCHAR(10)) as ID, R.ROL, U.USUARIO, U.CAMBIAR_CONTRASENIA, U.CONTRASENIA
	 		FROM DB_APP_DISLEXIA.APP_USUARIOS_SISTEMA U
	 		JOIN DB_APP_DISLEXIA.APP_DATOS_PERSONALES D ON D.ID_USUARIO = U.ID
			JOIN DB_APP_DISLEXIA.APP_ROL R ON R.ID = D.ID_ROL
			WHERE U.USUARIO = ?
			###AND U.CONTRASENIA = SHA2(?, 256)
			AND U.CONTRASENIA = ?
			AND U.BLOQUEADO = 'N'
			AND U.ESTADO = 'S' ";
	##echo '\n usu_password: '.$usu_password;
	//$texto_encriptado = openssl_encrypt($usu_password, "aes-256-cbc", $clave, 0, $iv);

	##echo '\n texto_encriptado: '.$texto_encriptado;
	//$texto_desencriptado = openssl_decrypt($texto_encriptado, "aes-256-cbc", $clave, 0, $iv);


	//$texto_desencriptado = decrypt($texto_encriptado, $clave);

	//$texto_encriptado = $usu_password;//encrypt($usu_password, $clave);
	$texto_encriptado = encriptar($usu_password, $reemplazos);

	// Ejecutar la sentencia
	$sentencia = $conexion->prepare($SQL);

	$sentencia->bind_param('ss', $usu_usuario, $texto_encriptado);
	//$sentencia->bind_param('s', $usu_usuario);
	$sentencia->execute();

	$resultado = $sentencia->get_result();
	if ($fila = $resultado->fetch_assoc()) {

		/*
			// Devolver respuesta en formato JSON
			  header('Content-Type: application/json');
			  echo json_encode($fila);
			##echo json_encode($fila,JSON_UNESCAPED_UNICODE);
			*/

		// Crear respuesta
		$respuesta = array(
			'codResponse' => '00',
			'msjResponse' => 'TRANSACCIÓN OK',
			'data' => $fila
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
} catch (Exception $e) {
	// Capturar la excepción y devolver un mensaje de error
	header('Content-Type: application/json');
	echo json_encode(array(
		'codResponse' => '99',
		'msjResponse' => $e->getMessage()
	), JSON_UNESCAPED_UNICODE);
}
