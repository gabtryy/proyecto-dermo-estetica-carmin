<?php

if (!is_file("modelo/".$pagina.".php")){
	//alli pregunte que si no es archivo se niega //con !
	//si no existe envio mensaje y me salgo
	echo "Falta definir la clase ".$pagina;
	exit;
}  
require_once("modelo/".$pagina.".php");  

$modelo = new Clientes();
$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
	header('Content-Type: application/json; charset=utf-8');

	if ($accion === 'consultar') {
		echo json_encode([
			'ok' => true,
			'data' => $modelo->listar(),
		]);
		exit;
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

		if ($datos['cedula'] === '' || $datos['nombres'] === '' || $datos['sexo'] === '') {
			http_response_code(422);
			echo json_encode(['ok' => false, 'mensaje' => 'Cédula, nombre y sexo son obligatorios.']);
			exit;
		}

		if ($modelo->existeCedula($datos['cedula'])) {
			http_response_code(409);
			echo json_encode(['ok' => false, 'mensaje' => 'La cédula ya está registrada.']);
			exit;
		}

		   try {
			   // Setear atributos usando setters
			   $modelo->set_cedula($datos['cedula']);
			   $modelo->set_nombres($datos['nombres']);
			   $modelo->set_fechadenacimiento($datos['fechadenacimiento']);
			   $modelo->set_sexo($datos['sexo']);
			   $modelo->set_telefono($datos['telefono']);
			   $modelo->set_direccion($datos['direccion']);

			   $modelo->insertar($datos);
			   echo json_encode(['ok' => true, 'mensaje' => 'Cliente guardado correctamente.']);
		   } catch (Throwable $e) {
			   http_response_code(500);
			   echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el cliente.']);
		   }
		   exit;
	}

	http_response_code(400);
	echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
	exit;
}

if (is_file("vista/modulos/".$pagina.".php")) {
	require_once("vista/modulos/".$pagina.".php");
}