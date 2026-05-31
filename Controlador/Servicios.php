<?php

require_once("modelo/".$pagina.".php"); 
$modelo = new Servicio();

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
				'id_servicio' => trim($_POST['id_servicio'] ?? ''),
				'nombre_servicio' => trim($_POST['nombre_servicio'] ?? ''),
				'precio' => trim($_POST['precio'] ?? ''),
				'descripcion' => trim($_POST['descripcion'] ?? ''),
			];
			
			try {
				$modelo->set_id_servicio($datos['id_servicio']);
				$modelo->set_nombre_servicio($datos['nombre_servicio']);
				$modelo->set_precio($datos['precio']);
				$modelo->set_descripcion($datos['descripcion']);

				if ($modelo->insertar()) {
					http_response_code(200);
					echo json_encode(['ok' => true, 'mensaje' => 'Servicio guardado correctamente.']);
					exit;
				} else {
					$error = $modelo->getUltimoError();
					http_response_code(500);
					echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
					exit;
				}
			} catch (Throwable $e) {
				http_response_code(500);
				echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el servicio.']);
				exit;
			}
		case 'eliminar':
			$id_servicio = trim($_POST['id_servicio'] ?? '');
			if (!$id_servicio) {
				http_response_code(400);
				echo json_encode(['ok' => false, 'mensaje' => 'ID de servicio no proporcionado.']);
				exit;
			}
			try {
				if ($modelo->eliminar($id_servicio)) {
					http_response_code(200);
					echo json_encode(['ok' => true, 'mensaje' => 'Servicio eliminado correctamente.']);
				} else {
					$error = $modelo->getUltimoError();
					http_response_code(500);
					echo json_encode(['ok' => false, 'mensaje' => 'No se pudo eliminar: ' . $error]);
				}
			} catch (Throwable $e) {
				http_response_code(500);
				echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar el servicio.']);
			}
			exit;
		case 'modificar':
			$datos = [
				'id_servicio' => trim($_POST['id_servicio'] ?? ''),
				'nombre_servicio' => trim($_POST['nombre_servicio'] ?? ''),
				'precio' => trim($_POST['precio'] ?? ''),
				'descripcion' => trim($_POST['descripcion'] ?? ''),
			];
			try {
				$modelo->set_id_servicio($datos['id_servicio']);
				$modelo->set_nombre_servicio($datos['nombre_servicio']);
				$modelo->set_precio($datos['precio']);
				$modelo->set_descripcion($datos['descripcion']);

				if ($modelo->modificar()) {
					http_response_code(200);
					echo json_encode(['ok' => true, 'mensaje' => 'Servicio modificado correctamente.']);
					exit;
				} else {
					$error = $modelo->getUltimoError();
					http_response_code(500);
					echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
					exit;
				}
			} catch (Throwable $e) {
				http_response_code(500);
				echo json_encode(['ok' => false, 'mensaje' => 'No se pudo modificar el servicio.']);
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