<?php
include 'conexion.php';



// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$opcion = $_GET['opcion'];
	$id_persona1 = $_GET['id_persona1'];
	$id_persona2 = $_GET['id_persona2'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$opcion = $_POST['opcion'];
	$id_persona1 = $_POST['id_persona1'];
	$id_persona2 = $_POST['id_persona2'];
}


try {

	if ($opcion == "IN1") {

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

	if ($opcion == "IN2") {

		$id_mensaje_relacion = $_POST['id_mensaje_relacion'];
		$id_duenio = $_POST['id_duenio'];
		$mensaje = $_POST['mensaje'];

		// Preparar las consultas para la inserción en ambas tablas
		$query1 = " INSERT INTO app_mensaje (ID_MENSAJE_RELACION, ID_DUENIO_SMS, MENSAJE) 
					VALUES(?,?,?) ";


		// Preparar las sentencias
		$stmt1 = $conexion->prepare($query1);

		// Vincular los parámetros
		$stmt1->bind_param('sss', $id_mensaje_relacion, $id_duenio, $mensaje);

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

	if ($opcion == "CN") { //CONSULTA DE MENSAJES

		$SQL = " SELECT ID, CAST(ID_MENSAJE_RELACION AS VARCHAR(10)) as ID_MENSAJE_RELACION, ID_DUENIO_SMS, mensaje,
				        CASE ID_DUENIO_SMS WHEN ? THEN 'S' ELSE 'N' END AS esEmisor
				 FROM db_app_dislexia.app_mensaje M
				 WHERE M.ID_MENSAJE_RELACION IN (SELECT ID
												FROM db_app_dislexia.app_mensaje_relacion R
												WHERE R.ID_PERSONA1 IN (?,?)
												  AND R.ID_PERSONA2 IN (?,?))
				 ORDER BY ID ";

		/*WHERE R.ID_PERSONA1 IN (3,4)
		 AND R.ID_PERSONA2 IN (3,4))*/

		// Ejecutar la sentencia
		$sentencia = $conexion->prepare($SQL);

		$sentencia->bind_param('sssss', $id_persona1, $id_persona1, $id_persona2, $id_persona1, $id_persona2);
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

	if ($opcion == "CE") { //consulta de estudiantes

		$SQL = " SELECT CAST(P.ID AS VARCHAR(10)) as ID, E.USUARIO, P.NOMBRES, P.APELLIDOS, P.SEXO
		 		 FROM APP_USUARIOS_SISTEMA E
				 JOIN APP_DATOS_PERSONALES P ON P.ID_USUARIO = E.ID
				 JOIN APP_ROL R 		     ON R.ID = P.ID_ROL AND R.ID = 3
				 
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

	if ($opcion == "CP") { //consulta de profesores

		$SQL = " SELECT CAST(P.ID AS VARCHAR(10)) as ID, E.USUARIO, P.NOMBRES, P.APELLIDOS, P.SEXO
		 		 FROM APP_USUARIOS_SISTEMA E
				 JOIN APP_DATOS_PERSONALES P ON P.ID_USUARIO = E.ID
				 JOIN APP_ROL R 		     ON R.ID = P.ID_ROL AND R.ID IN (1,2)
				 
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

	if ($opcion == "CT") { //consulta de todos

		$SQL = " SELECT CAST(P.ID AS VARCHAR(10)) as ID, E.USUARIO, P.NOMBRES, P.APELLIDOS, P.SEXO
		 		 FROM APP_USUARIOS_SISTEMA E
				 JOIN APP_DATOS_PERSONALES P ON P.ID_USUARIO = E.ID
				 JOIN APP_ROL R 		     ON R.ID = P.ID_ROL AND R.ID IN (2,3)
				 
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
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
