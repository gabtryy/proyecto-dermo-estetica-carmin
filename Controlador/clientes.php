<?php
  
require_once("modelo/".$pagina.".php");  

$modelo = new Clientes();

$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
	if (!headers_sent()) {
		header('Content-Type: application/json; charset=utf-8');
	}

	switch ($accion) {
		case 'consultar':
			echo json_encode([
				'ok' => true,
				'data' => $modelo->listar(),
			]);
			exit;
		case 'incluir':
		
			$datos = [
				'cedula' => trim($_POST['cedula'] ?? ''),
				'nombres' => trim($_POST['nombres'] ?? ''),
				'telefono' => trim($_POST['telefono'] ?? ''),
				'direccion' => trim($_POST['direccion'] ?? ''),
				'fechadenacimiento' => trim($_POST['fechadenacimiento'] ?? ''),
				'sexo' => trim($_POST['sexo'] ?? ''),
			];
			try {
				$modelo->set_cedula($datos['cedula']);
				$modelo->set_nombres($datos['nombres']);
				$modelo->set_fechadenacimiento($datos['fechadenacimiento']);
				$modelo->set_sexo($datos['sexo']);
				$modelo->set_telefono($datos['telefono']);
				$modelo->set_direccion($datos['direccion']);

				if ($modelo->insertar()) {
					http_response_code(200);
					echo json_encode(['ok' => true, 'mensaje' => 'Cliente guardado correctamente.']);
					exit;
				} else {
					$error = $modelo->getUltimoError();
					http_response_code(500);
					echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
					exit;
				}
			} catch (Throwable $e) {
				http_response_code(500);
				echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el cliente.']);
				exit;
			}
		case 'eliminar':
			$cedula = trim($_POST['cedula'] ?? '');
			if (!$cedula) {
				http_response_code(400);
				echo json_encode(['ok' => false, 'mensaje' => 'Cédula no proporcionada.']);
				exit;
			}
			try {
				if ($modelo->eliminar($cedula)) {
					http_response_code(200);
					echo json_encode(['ok' => true, 'mensaje' => 'Cliente eliminado correctamente.']);
				} else {
					$error = $modelo->getUltimoError();
					http_response_code(500);
					echo json_encode(['ok' => false, 'mensaje' => 'No se pudo eliminar: ' . $error]);
				}
			} catch (Throwable $e) {
				http_response_code(500);
				echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar el cliente.']);
			}
			exit;
		case 'modificar':
			$datos = [
				'cedula' => trim($_POST['cedula'] ?? ''),
				'nombres' => trim($_POST['nombres'] ?? ''),
				'telefono' => trim($_POST['telefono'] ?? ''),
				'direccion' => trim($_POST['direccion'] ?? ''),
				'fechadenacimiento' => trim($_POST['fechadenacimiento'] ?? ''),
				'sexo' => trim($_POST['sexo'] ?? ''),
			];
			try {
				$modelo->set_cedula($datos['cedula']);
				$modelo->set_nombres($datos['nombres']);
				$modelo->set_fechadenacimiento($datos['fechadenacimiento']);
				$modelo->set_sexo($datos['sexo']);
				$modelo->set_telefono($datos['telefono']);
				$modelo->set_direccion($datos['direccion']);

				if ($modelo->modificar()) {
					http_response_code(200);
					echo json_encode(['ok' => true, 'mensaje' => 'Cliente modificado correctamente.']);
					exit;
				} else {
					$error = $modelo->getUltimoError();
					http_response_code(500);
					echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
					exit;
				}
			} catch (Throwable $e) {
				http_response_code(500);
				echo json_encode(['ok' => false, 'mensaje' => 'No se pudo modificar el cliente.']);
				exit;
			}
		default:
			http_response_code(400);
			echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
			exit;
	}
}

if (is_file("vista/modulos/".$pagina.".php")) {
	require_once("vista/modulos/".$pagina.".php");
}