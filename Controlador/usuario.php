<?php
require_once 'modelo/Usuario.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

switch ($metodo) {
    case 'listar':
        if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
            header("Location: index.php?c=login&m=home");
            exit;
        }
        
        $u = new Usuario();
        $usuarios = $u->listar();
        require 'vista/Administrador/usuarios.php';
        break;
        
    case 'crear':
        verificarPermisosAdmin();
        require 'vista/Administrador/crear_usuario.php';
        break;
        
    case 'guardar':
        verificarPermisosAdmin();
        
        $cedula = $_POST['cedula'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $clave = $_POST['clave'] ?? '';
        
        if (empty($cedula) || empty($rol) || empty($nombre) || empty($correo) || empty($clave)) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: index.php?c=usuario&m=crear");
            exit;
        }
        
        $u = new Usuario();
        $exito = $u->registrar($cedula, $rol, $nombre, $telefono, $correo, $clave);
        
        if ($exito) {
            $_SESSION['mensaje'] = "Usuario creado correctamente.";
        } else {
            $_SESSION['error'] = "Error al crear el usuario.";
        }
        
        header("Location: index.php?c=usuario&m=listar");
        exit;
        break;
        
    case 'eliminar':
        verificarPermisosAdmin();
        
        $cedula = $_GET['cedula'] ?? '';
        
        if (empty($cedula)) {
            $_SESSION['error'] = "Cédula no válida.";
            header("Location: index.php?c=usuario&m=listar");
            exit;
        }
        
        $u = new Usuario();
        $exito = $u->eliminar($cedula);
        
        if ($exito) {
            $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
        } else {
            $_SESSION['error'] = "Error al eliminar el usuario.";
        }
        
        header("Location: index.php?c=usuario&m=listar");
        exit;
        break;
        
    default:
        echo "Acción no válida.";
        break;
}
?> 