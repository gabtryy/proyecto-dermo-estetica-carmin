<?php
if (isset($_SESSION['nivel'])) {
    header('Location: index.php?pagina=home');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = isset($_POST['usuario']) && is_string($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $clave = isset($_POST['clave']) && is_string($_POST['clave']) ? $_POST['clave'] : '';

    try {
        require_once 'modelo/Usuario.php';
        $modeloUsuario = new Usuario();
        $usuario = $modeloUsuario->autenticar($nombreUsuario, $clave);

        if ($usuario !== false) {
            session_destroy();
            session_start();
            $_SESSION['nivel'] = $usuario['rol'];
            $_SESSION['usuario'] = $usuario['usuario'];
            header('Location: index.php');
            exit;
        }

        $mensaje = 'Usuario o contraseña incorrectos.';
    } catch (PDOException $e) {
        error_log('Error al iniciar sesión: ' . $e->getMessage());
        $mensaje = 'No se pudo validar el acceso en este momento. Inténtalo más tarde.';
    }
}

require_once 'vista/login.php';
