<?php
if (!is_file("modelo/conexion.php")) {
	echo "Falta definir la clase Diagnostico";
	exit;
}

require_once("modelo/diagnostico.php");

$modelo = new Diagnostico();
$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
	header('Content-Type: application/json; charset=utf-8');

	if ($accion === 'piel') {
		echo json_encode([
			'ok' => true,
			'data' => $modelo->listarPieles()
		]);
		exit;
	}

	if ($accion === 'consultar') {
		echo json_encode([
			'ok' => true,
			'data' => $modelo->listar()
		]);
		exit;
	}

	if ($accion === 'incluir') {
		$modelo->set_cedulaCliente(trim($_POST['cedulaCliente'] ?? ''));
		$modelo->set_idPiel(trim($_POST['idPiel'] ?? ''));
		$modelo->set_frente(trim($_POST['frente'] ?? ''));
		$modelo->set_nariz(trim($_POST['naris'] ?? $_POST['nariz'] ?? ''));
		$modelo->set_mejillaIzquierda(trim($_POST['mejilla_izquierda'] ?? $_POST['mejilla_izq'] ?? ''));
		$modelo->set_mejillaDerecha(trim($_POST['mejilla_derecha'] ?? $_POST['mejilla_der'] ?? ''));
		$modelo->set_menton(trim($_POST['menton'] ?? ''));

		$respuesta = $modelo->insertar();
		echo json_encode([
			'ok' => $respuesta['resultado'] === 'exito',
			'mensaje' => $respuesta['mensaje']
		]);
		exit;
	}

	echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
	exit;
}

require_once("vista/modulos/diagnostico.php");
?>