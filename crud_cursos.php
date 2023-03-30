<?php
include 'conexion.php';
require_once 'util/funciones.php';


// Configuración de las cabeceras HTTP para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$opcion = $_GET['opcion'];
	$id_profesor = $_GET['id_profesor'];
	$nombre = $_GET['nombre'];
	$descripcion = $_GET['descripcion'];
	$anio = $_GET['anio'];
	$estado = $_GET['estado'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$opcion = $_POST['opcion'];
	$id_profesor = $_POST['id_profesor'];
	$nombre = $_POST['nombre'];
	$descripcion = $_POST['descripcion'];
	$anio = $_POST['anio'];
	$estado = $_POST['estado'];
}





try {

	if ($opcion == "IN") {

		$SQL = " SELECT COUNT(1) EXISTE
			FROM db_app_dislexia.app_curso C
			WHERE C.ID_PROFESOR  = ?
			AND NOMBRE           = ? ";


		// Ejecutar la sentencia
		$sentencia   = $conexion->prepare($SQL);
		$sentencia->bind_param('ss', $id_profesor, $nombre);
		$sentencia->execute();
		$resultado   = $sentencia->get_result();

		$fila = $resultado->fetch_assoc();

		$existe = $fila["EXISTE"];

		if ($existe == "0") {


			// Preparar las consultas para la inserción en ambas tablas
			$query1 = " INSERT INTO app_curso 
										   (ID, ID_PROFESOR, NOMBRE, DESCRIPCION, ANIO, ESTADO) 
									VALUES ((SELECT NVL(max(id),0)+1
											 FROM db_app_dislexia.app_curso C),
											 ?, ?, ?, ?, ?) ";


			// Preparar las sentencias
			$stmt1 = $conexion->prepare($query1);

			// Vincular los parámetros
			$stmt1->bind_param('issss', $id_profesor, $nombre, $descripcion, $anio, $estado);

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
		} else {

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '02',
				'msjResponse' => 'LA INFORMACIÓN YA SE ENCUENTRA REGISTRADA.',
			);

			// Devolver respuesta en formato JSON
			header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		}
		$sentencia->close();
		$conexion->close();
	}


	if ($opcion == "CN") {

		$SQL = " SELECT CAST(ID AS VARCHAR(10)) as ID, ID_PROFESOR, NOMBRE, DESCRIPCION, ANIO, ESTADO
				 FROM db_app_dislexia.app_curso 
				 where ID_PROFESOR = ? ";

		// Ejecutar la sentencia
		$sentencia = $conexion->prepare($SQL);

		$sentencia->bind_param('i', $id_profesor);
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
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		} else {

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '02',
				'msjResponse' => 'NO HAY DATOS',
			);

			// Devolver respuesta en formato JSON
			header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		}
		$sentencia->close();
		$conexion->close();
	}

	if ($opcion == "CP") { //consulta de participantes pendientes para el curso
		$id_curso = $_POST['id_curso'];
		$SQL = " SELECT P.ID, E.USUARIO, P.NOMBRES, P.APELLIDOS, P.SEXO
				 FROM APP_USUARIOS_SISTEMA E
			     JOIN APP_DATOS_PERSONALES P ON P.ID_USUARIO = E.ID
				 JOIN APP_ROL R ON R.ID = P.ID_ROL AND R.ID = 3
				 WHERE E.ID NOT IN (SELECT ID_ESTUDIANTE FROM APP_ESTUD_X_CURSO Z WHERE Z.ID_CURSO = ?)
				 
				 ";

		// Ejecutar la sentencia
		$sentencia = $conexion->prepare($SQL);

		$sentencia->bind_param('s', $id_curso);
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
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		} else {

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '02',
				'msjResponse' => 'NO HAY DATOS',
			);

			// Devolver respuesta en formato JSON
			header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		}
		$sentencia->close();
		$conexion->close();
	}


	if ($opcion == "ICE") { //INSERTAR ESTUDIANTES POR CURSOS

		$id_curso = $_POST['id_curso'];
		$id_estudiante = $_POST['id_estudiante'];

		// Preparar las consultas para la inserción en ambas tablas
		$query1 = " INSERT INTO app_estud_x_curso(ID_CURSO, ID_ESTUDIANTE) VALUES(?,?) ";

		// Preparar las sentencias
		$stmt1 = $conexion->prepare($query1);

		// Vincular los parámetros
		$stmt1->bind_param('ss', $id_curso, $id_estudiante);

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

	if ($opcion == "CPC") { //consulta de participantes de el curso

		$id_curso = $_POST['id_curso'];

		$SQL = " SELECT P.ID, E.USUARIO, P.NOMBRES, P.APELLIDOS, P.SEXO
		 		 FROM APP_USUARIOS_SISTEMA E
				 JOIN APP_DATOS_PERSONALES P ON P.ID_USUARIO = E.ID
				 JOIN APP_ROL R ON R.ID = P.ID_ROL AND R.ID = 3
				 JOIN APP_ESTUD_X_CURSO X ON X.ID_ESTUDIANTE = E.ID
				 where X.ID_CURSO = ?
				 
				 ";

		// Ejecutar la sentencia
		$sentencia = $conexion->prepare($SQL);

		$sentencia->bind_param('s', $id_curso);
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
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		} else {

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '02',
				'msjResponse' => 'NO HAY DATOS',
			);

			// Devolver respuesta en formato JSON
			header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		}
		$sentencia->close();
		$conexion->close();
	}

	if ($opcion == "CX") { //CONSULTA DE LOS CURSOS DE LOS ESTUDIANTES

		$SQL = " SELECT CAST(C.ID AS VARCHAR(10)) as ID, NOMBRE, DESCRIPCION, ANIO, ESTADO
			 	 FROM app_estud_x_curso E
			 	 JOIN app_curso C ON E.ID_CURSO = C.ID
				 WHERE E.ID_ESTUDIANTE = ? ";

		// Ejecutar la sentencia
		$sentencia = $conexion->prepare($SQL);

		$sentencia->bind_param('i', $id_profesor); // id del estudiante 
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
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		} else {

			// Crear respuesta
			$respuesta = array(
				'codResponse' => '02',
				'msjResponse' => 'NO HAY DATOS',
			);

			// Devolver respuesta en formato JSON
			header('Content-Type: application/json');
			//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		}
		$sentencia->close();
		$conexion->close();
	}


	if ($opcion == "CT") { //CONSULTA TAREA POR CURSO
		$id_curso = $_POST['id_curso'];

		$SQL = " SELECT CAST(ID AS VARCHAR(25)) AS ID, 
                        CAST(ID_CURSO AS VARCHAR(25)) AS ID_CURSO, 
                        TITULO, INSTRUCCIONES, 
                        DATE_FORMAT(FECHA_VENCIMIENTO, '%d/%m/%Y') FECHA_VENCIMIENTO
                 FROM DB_APP_DISLEXIA.APP_ASIGNACIONES A
                 WHERE ID_CURSO = ?

                ";

		// Preparar la sentencia
		$sentencia = $conexion->prepare($SQL);
		$sentencia->bind_param('i', $id_curso);

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

		// Devolver respuesta en formato JSON
		header('Content-Type: application/json');
		//echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		$sentencia->close();
		$conexion->close();
	}

	
} catch (Exception $e) {
	// Capturar la excepción y devolver un mensaje de error
	/* header('Content-Type: application/json');
	echo json_encode(array(
		'codResponse' => '99',
		'msjResponse' => $e->getMessage()
	), JSON_UNESCAPED_UNICODE); */

	$respuesta = array(
		'codResponse' => '99',
		'msjResponse' =>  $e->getMessage()
	);
}



devolver_respuesta($respuesta);
