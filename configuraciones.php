<?php
	include 'conexion.php';
	require_once 'util/funciones.php';

	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$usu_usuario=$_GET['usuario'];
		$usu_password=$_GET['password'];
		$nueva_password=$_GET['nueva'];
	}


	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$usu_usuario=$_POST['usuario'];
		$usu_password=$_POST['password'];
		$nueva_password=$_POST['nueva'];
	}


	 
	$SQL = " SELECT R.ROL, U.USUARIO, U.CAMBIAR_CONTRASENIA
			FROM DB_APP_DISLEXIA.APP_USUARIOS_SISTEMA U
			JOIN DB_APP_DISLEXIA.APP_DATOS_PERSONALES D ON D.ID_USUARIO = U.ID
			JOIN DB_APP_DISLEXIA.APP_ROL R ON R.ID = D.ID_ROL
			WHERE U.USUARIO = ?
			##AND U.CONTRASENIA = SHA2(?, 256)
			AND U.CONTRASENIA = ?
			AND U.BLOQUEADO = 'N'
			AND U.ESTADO = 'S' ";


	try {

		$texto_encriptado = encriptar($usu_password, $reemplazos);
		$nueva_pass_en = encriptar($nueva_password, $reemplazos);
			// Ejecutar la sentencia
			$sentencia=$conexion->prepare($SQL);

			$sentencia->bind_param('ss',$usu_usuario,$texto_encriptado);
			##$sentencia->bind_param('ss',$usu_usuario,$usu_password);
			$sentencia->execute();

			$resultado = $sentencia->get_result();
			if ($fila = $resultado->fetch_assoc()){
				
				try{
						/*echo $nueva_password;
						echo "pass... ";*/
						
						$SQL = "UPDATE DB_APP_DISLEXIA.APP_USUARIOS_SISTEMA U
						SET #CONTRASENIA = SHA2(?, 256),
						    CONTRASENIA = ?,
						    CAMBIAR_CONTRASENIA = 'N'
						WHERE U.USUARIO = ?
						##AND U.CONTRASENIA = SHA2(?, 256)
						AND U.CONTRASENIA = ?
						AND U.BLOQUEADO = 'N'
						AND U.ESTADO = 'S' ";
						
						$sentencia=$conexion->prepare($SQL);

						$sentencia->bind_param('sss',$nueva_pass_en,$usu_usuario,$texto_encriptado);
						$sentencia->execute();
			
						// Crear respuesta
						$respuesta = array(
							'codResponse' => '00',
							'msjResponse' => 'TRANSACCIÓN OK',
							'data' => $fila
						);
				} catch (Exception $e) {
					  // Capturar la excepción y devolver un mensaje de error
					  $respuesta = array(
						'codResponse' => '02',
						'msjResponse' => 'NO SE PUDO ACTUALIZAR LA CONTRASEÑA'
					  );
				}
				
				// Devolver respuesta en formato JSON
				header('Content-Type: application/json');
				echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
				
			}else {
				
				// Crear respuesta
				$respuesta = array(
					'codResponse' => '02',
					'msjResponse' => 'NO HAY DATOS',
				);
				
				// Devolver respuesta en formato JSON
				header('Content-Type: application/json');
				echo json_encode($respuesta,JSON_UNESCAPED_UNICODE);
				
			}
			$sentencia->close();
			$conexion->close();
			
	} catch (Exception $e) {
		  // Capturar la excepción y devolver un mensaje de error
		  header('Content-Type: application/json');
		  echo json_encode(array(
			'codResponse' => '99',
			'msjResponse' => $e->getMessage()
		  ),JSON_UNESCAPED_UNICODE);
	}

?>
