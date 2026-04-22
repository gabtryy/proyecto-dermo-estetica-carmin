<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	$es_admin = false;
	$es_analista = false;
	$es_usuario = false;
	$puede_ver_reportes = false;
	$puede_gestionar_usuarios = false;
	$puede_gestionar_categorias = false;
	$puede_ver_analisis = false;
	
	if (isset($_SESSION['id_rol'])) {
		$es_admin = $_SESSION['id_rol'] == 2;
		$es_analista = $_SESSION['id_rol'] == 3;
		$es_usuario = $_SESSION['id_rol'] == 1;
		
		
		
		$puede_ver_reportes = $es_admin || $es_analista;
		$puede_gestionar_usuarios = $es_admin;
		$puede_gestionar_categorias = $es_admin;
		$puede_ver_analisis = $es_analista || $es_admin;
	
	}

	$controlador = $_GET['c'] ?? 'login';
	$metodo = $_GET['m'] ?? 'login';

	if (!isset($_SESSION['cedula']) && $controlador !== 'login') {
		header("Location: index.php?c=login&m=login");
		exit;
	}

	if (isset($_SESSION['cedula']) && $controlador === 'login' && $metodo === 'login') {
		header("Location: index.php?c=login&m=home");
		exit;
	}

	$archivo = __DIR__ . "/Controlador/{$controlador}.php";

	if (file_exists($archivo)){

		require_once $archivo;
		
	}else {
		
		echo "Controlador no encontrado";

	}
// pene que ricoooo
?>