<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	// Modo de prueba: omitir login si se solicita con ?skip_login=1
	// Uso: accede a cualquier URL con &skip_login=1 para crear sesión temporal.
	if (isset($_GET['skip_login']) && $_GET['skip_login'] === '1') {
		$_SESSION['cedula'] = 'TEST-0000';
		$_SESSION['username'] = 'tester';
		$_SESSION['id_rol'] = 2; // rol con permisos (ajustar si necesario)
	}

	$es_admin = false;
	$es_analista = false;
	$es_usuario = false;
	$puede_ver_reportes = false;
	$puede_gestionar_clientes = false;
	$puede_gestionar_esteticista = false;
	$puede_gestionar_categorias = false;
	$puede_ver_analisis = false;
	
	if (isset($_SESSION['id_rol'])) {
		$es_admin = $_SESSION['id_rol'] == 1;
		$es_analista = $_SESSION['id_rol'] == 2;
		$es_usuario = $_SESSION['id_rol'] == 3;
		$puede_gestionar_esteticista = $es_admin;
		$puede_ver_reportes = $es_admin || $es_analista;
		$puede_gestionar_clientes = $es_admin;
		$puede_gestionar_categorias = $es_admin;
		$puede_ver_analisis = $es_analista || $es_admin;
	
	}

	$controlador = $_GET['c'] ?? 'login';
	$metodo = $_GET['m'] ?? 'login';

	// Si se solicita omitir login y no se indicó controlador, abrir directamente módulo servicios
	if (isset($_GET['skip_login']) && $_GET['skip_login'] === '1' && (!isset($_GET['c']) || empty($_GET['c']))) {
		$controlador = 'servicios';
		$metodo = 'consultar';
	}

	if (!isset($_SESSION['cedula']) && $controlador !== 'login') {
		header("Location: index.php?c=login&m=login");
		exit;
	}

	if (isset($_SESSION['cedula']) && $controlador === 'login' && $metodo === 'login') {
		header("Location: index.php?c=login&m=home");
		exit;
	}

	$archivo = __DIR__ . "/Controlador/{$controlador}.php";
	$pagina = $controlador;

	if (file_exists($archivo)){

		require_once $archivo;
		
	}else {
		
		echo "Controlador no encontrado";
		//sapo
	}

?>
