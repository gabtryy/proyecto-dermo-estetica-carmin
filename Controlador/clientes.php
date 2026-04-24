<?php

if (!is_file("modelo/".$pagina.".php")){
	
	echo "Falta definir la clase ".$pagina;
	exit;
}  
require_once("modelo/".$pagina.".php");  

$modelo = new Clientes();
$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
	   if (!headers_sent()) {
		   header('Content-Type: application/json; charset=utf-8');
	   }

	if ($accion === 'consultar') {
		   echo json_encode([
			   'ok' => true,
			   'data' => $modelo->listar(),
		   ]);
		   exit;
		   return;
	}

	if ($accion === 'incluir') {
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
				   return;
			   } else {
				   $error = $modelo->getUltimoError();
				   http_response_code(500);
				   echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
				   exit;
				   return;
			   }
		   } catch (Throwable $e) {
			   http_response_code(500);
			   echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el cliente.']);
			   exit;
			   return;
		   }
	}

	http_response_code(400);
	echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
	exit;
	return;
}

if (is_file("vista/modulos/".$pagina.".php")) {
	require_once("vista/modulos/".$pagina.".php");
}