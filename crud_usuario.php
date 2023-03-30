<?php
include 'conexion.php';
require_once 'util/funciones.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$opcion = $_GET['opcion'];
	$id_usuario = $_GET['id_usuario'];
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
	$rol = $_POST['rol'];
	$cedula = $_POST['cedula'];
	$nombres = $_POST['nombres'];
	$apellidos = $_POST['apellidos'];
	$edad = $_POST['edad'];
	$usuario = $_POST['usuario'];
	$contrasenia = $_POST['contrasenia'];
	$sexo = $_POST['esMasculino'];
	$solicitarPass = $_POST['solicitarPass'];
}

try {

	if ($opcion == "IN") {

		$SQL = " SELECT COUNT(1) ES_PERSONAL
			FROM DB_APP_DISLEXIA.APP_DATOS_PERSONALES D
			WHERE D.CEDULA = ? ";

		$SQL_2 = " SELECT COUNT(1) ES_USUARIO
			FROM DB_APP_DISLEXIA.APP_USUARIOS_SISTEMA U
			WHERE U.USUARIO = ? ";



		// Ejecutar la sentencia
		$sentencia   = $conexion->prepare($SQL);
		$sentencia->bind_param('s', $cedula);
		$sentencia->execute();
		$resultado   = $sentencia->get_result();

		$sentencia_2 = $conexion->prepare($SQL_2);
		$sentencia_2->bind_param('s', $usuario);
		$sentencia_2->execute();
		$resultado_2 = $sentencia_2->get_result();

		$fila = $resultado->fetch_assoc();
		$fila_2 = $resultado_2->fetch_assoc();

		/*echo "\njson_encode: \n ";
			echo json_encode($fila);
			echo "\njson_encode esto: \n ";
			echo json_encode($fila_2);
					
			
			echo "\nOBTENER EL VALOR DE UNA COLUMNA: \n ";
			echo $fila["ES_PERSONAL"];
			echo "\nfila_2 esto: \n ";
			echo $fila_2["ES_USUARIO"];*/


		$es_persona = $fila["ES_PERSONAL"];
		$es_usuario = $fila_2["ES_USUARIO"];

		if ($es_persona == "0" && $es_usuario == "0") {

			// Preparar las consultas para la inserción en ambas tablas
			$query1 = "INSERT INTO app_usuarios_sistema 
							  (id, usuario, contrasenia, bloqueado, cambiar_contrasenia, estado, usuario_creacion, fecha_creacion) 
							   VALUES ((SELECT max(id)+1
										FROM db_app_dislexia.app_usuarios_sistema u), 
										?, SHA2(?, 256), 'N', ?,'S', 'ADMIN', now())";


			$query2 = "INSERT INTO app_datos_personales 
							   (ID, id_rol, id_usuario, nombres, apellidos, cedula, edad, sexo, estado, usuario_creacion, fecha_creacion) 
							   VALUES ((SELECT max(id)+1
										FROM db_app_dislexia.app_datos_personales d), 
										?, (SELECT max(id) FROM db_app_dislexia.app_usuarios_sistema u), 
										?, ?, ?, ?, ?, 'S', 'ADMIN', now())";

			// Preparar las sentencias
			$stmt1 = $conexion->prepare($query1);
			$stmt2 = $conexion->prepare($query2);

			// Vincular los parámetros
			$stmt1->bind_param("sss", $usuario, $contrasenia, $solicitarPass);
			$stmt2->bind_param("ssssss", $rol, $nombres, $apellidos, $cedula, $edad, $sexo);

			// Ejecutar las consultas
			$stmt1->execute();
			$stmt2->execute();

			// Cerrar las sentencias
			$stmt1->close();
			$stmt2->close();

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '00',
				'msjResponse' => 'TRANSACCIÓN OK',
				'data' => $fila
			);

			// Devolver respuesta en formato JSON
			//header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		} else {

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '02',
				'msjResponse' => 'EL USUARIO YA SE ENCUENTRA REGISTRADO',
			);

			// Devolver respuesta en formato JSON
			//header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		}
		$sentencia->close();
		$sentencia_2->close();
		$conexion->close();
	} else if ($opcion == "AC") {

		$SQL = "UPDATE app_usuarios_sistema
				SET USUARIO = ?,
					contrasenia = SHA2(?, 256)
				WHERE ID = ? ";

		$sentencia = $conexion->prepare($SQL);

		$sentencia->bind_param('ssi', $usuario, $contrasenia, $id_usuario);
		$sentencia->execute();

		$SQL_2 = "UPDATE app_datos_personales a
					SET A.NOMBRES = ?,
						A.APELLIDOS = ?,
						A.CEDULA = ?,
						A.EDAD = ?,
						A.SEXO = ?
					WHERE A.ID_USUARIO = ?";

		$sentencia_2 = $conexion->prepare($SQL_2);

		$sentencia_2->bind_param('sssssi', $nombres, $apellidos, $cedula, $edad, $sexo, $id_usuario);
		$sentencia_2->execute();

		// Crear respuesta
		$respuesta = array(
			'codResponse' => '00',
			'msjResponse' => 'TRANSACCIÓN OK'
		);

		// Devolver respuesta en formato JSON
		//header('Content-Type: application/json');
		//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

		$sentencia->close();
		$sentencia_2->close();
		$conexion->close();
	} else if ($opcion == "CN") {

		$SQL = " SELECT CAST(U.ID AS VARCHAR(10)) AS ID, R.ROL, U.USUARIO, U.CAMBIAR_CONTRASENIA,
						D.NOMBRES, D.APELLIDOS, D.CEDULA, D.SEXO, 
						CAST(D.EDAD  AS VARCHAR(10)) as EDAD,
						CAST(R.ID  AS VARCHAR(10)) as ID_ROL
				FROM DB_APP_DISLEXIA.APP_USUARIOS_SISTEMA U
				JOIN DB_APP_DISLEXIA.APP_DATOS_PERSONALES D ON D.ID_USUARIO = U.ID
				JOIN DB_APP_DISLEXIA.APP_ROL R ON R.ID = D.ID_ROL
				WHERE 1=1
				AND U.ID = ? ";

		// Ejecutar la sentencia
		$sentencia   = $conexion->prepare($SQL);
		$sentencia->bind_param('s', $id_usuario);
		$sentencia->execute();
		$resultado   = $sentencia->get_result();

		$fila = $resultado->fetch_assoc();
		//$es_persona = $fila["ES_PERSONAL"];

		// Crear respuesta
		$respuesta = array(
			'codResponse' => '00',
			'msjResponse' => 'TRANSACCIÓN OK',
			'data' => $fila
		);

		// Devolver respuesta en formato JSON
		//header('Content-Type: application/json');
		//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

		$sentencia->close();
		$conexion->close();
	} else if ($opcion == "CNC") { //CONSULTA POR CEDULA
		$SQL = " SELECT CAST(U.ID AS VARCHAR(10)) AS ID, R.ROL, U.USUARIO, U.CAMBIAR_CONTRASENIA,
						D.NOMBRES, D.APELLIDOS, D.CEDULA, D.SEXO, 
						CAST(D.EDAD  AS VARCHAR(10)) as EDAD,
						CAST(R.ID  AS VARCHAR(10)) as ID_ROL
				FROM DB_APP_DISLEXIA.APP_USUARIOS_SISTEMA U
				JOIN DB_APP_DISLEXIA.APP_DATOS_PERSONALES D ON D.ID_USUARIO = U.ID
				JOIN DB_APP_DISLEXIA.APP_ROL R ON R.ID = D.ID_ROL
				WHERE 1=1
				AND D.CEDULA = ? ";

		// Ejecutar la sentencia
		$sentencia   = $conexion->prepare($SQL);
		$sentencia->bind_param('s', $cedula);
		$sentencia->execute();
		$resultado   = $sentencia->get_result();

		$fila = $resultado->fetch_assoc();
		//$es_persona = $fila["ES_PERSONAL"];

		// Crear respuesta
		$respuesta = array(
			'codResponse' => '00',
			'msjResponse' => 'TRANSACCIÓN OK',
			'data' => $fila
		);

		// Devolver respuesta en formato JSON
		//header('Content-Type: application/json');
		//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

		$sentencia->close();
		$conexion->close();
	}
} catch (Exception $e) {
	// Capturar la excepción y devolver un mensaje de error
	/*header('Content-Type: application/json');
	echo json_encode(array(
		'codResponse' => '99',
		'msjResponse' => $e->getMessage()
	), JSON_UNESCAPED_UNICODE);*/

	$respuesta = array(
		'codResponse' => '99',
		'msjResponse' =>  $e->getMessage()
	);
}


devolver_respuesta($respuesta);
