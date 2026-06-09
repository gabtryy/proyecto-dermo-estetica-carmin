<?php

<<<<<<< HEAD
require_once("modelo/".$pagina.".php"); 
$modelo = new Servicio();
=======
// 1. VALIDACIÓN PREVIA DEL MODELO (Verificar el archivo antes de llamarlo)
$archivoModelo = __DIR__ . '/../modelo/Servicio.php';
>>>>>>> rama-leomar

if (is_file($archivoModelo)) {
    require_once $archivoModelo;
    $modelo = new Servicio();
} else {
    // Si el archivo del modelo no existe, se frena el sistema
    die("Página en construcción (Error: Modelo no encontrado)");
}

// Captura de la acción para el AJAX unificado
$accion = $_POST['accion'] ?? '';

// Estructura de control para las peticiones AJAX (un solo punto de salida si hay acción)
if ($accion !== '') {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }

    // Variable unificada para la respuesta AJAX que va al servidor/cliente
    $respuesta = [
        'ok' => false,
        'mensaje' => ''
    ];

    switch ($accion) {
        case 'consultar':
            echo json_encode([
                'ok' => true,
                'data' => $modelo->listar(),
            ]);
            exit;

        case 'incluir':
            $modelo->set_nombreServicio(trim($_POST['nombreServicio'] ?? ''));
            $modelo->set_precio(trim($_POST['precio'] ?? ''));
            $modelo->set_descripcion(trim($_POST['descripcion'] ?? ''));
            
                if ($modelo->insertar()) {
                $respuesta['ok'] = true;
                $respuesta['mensaje'] = 'Servicio guardado correctamente.';
            } else {
                $respuesta['mensaje'] = 'Error en la base de datos: ' . $modelo->getUltimoError();
            }
            break;

        case 'modificar':
            $idServicio = trim($_POST['idServicio'] ?? '');
            if (!$idServicio) {
                $respuesta['mensaje'] = 'ID de servicio no proporcionado.';
            } else {
                // Ajustado el espacio en blanco que tenías en el array original 'nombreServicio '
                $modelo->set_nombreServicio(trim($_POST['nombreServicio'] ?? ''));
                $modelo->set_precio(trim($_POST['precio'] ?? ''));
                $modelo->set_descripcion(trim($_POST['descripcion'] ?? ''));

                if ($modelo->modificar($idServicio)) {
                    $respuesta['ok'] = true;
                    $respuesta['mensaje'] = 'Servicio modificado correctamente.';
                } else {
                    $respuesta['mensaje'] = 'Error en la base de datos: ' . $modelo->getUltimoError();
                }
            }
            break;

        case 'eliminar':
            $idServicio = trim($_POST['idServicio'] ?? '');
            if (!$idServicio) {
                $respuesta['mensaje'] = 'ID de servicio no proporcionado.';
            } else {
                if ($modelo->eliminar($idServicio)) {
                    $respuesta['ok'] = true;
                    $respuesta['mensaje'] = 'Servicio eliminado correctamente.';
                } else {
                    $respuesta['mensaje'] = 'No se pudo eliminar: ' . $modelo->getUltimoError();
                }
            }
            break;

        default:
                $respuesta['mensaje'] = 'Acción no válida.';
            break;
    }

    // UN SOLO AJAX DESDE EL SERVIDOR: Envía la respuesta unificada para modificar, eliminar e incluir
            echo json_encode($respuesta);
    exit;
}

// 2. ASPECTO DE ESTRUCTURA Y RUTAS DE LA VISTA
// Si no es una petición AJAX, se encarga de pintar la vista solicitada de forma segura
$rutaVista = "vista/modulos/" . $pagina . ".php";

if (is_file($rutaVista)) {
    require_once($rutaVista);
} else {
    // Si la variable $pagina apunta a algo que no existe -> Página en construcción
    echo "Página en construcción";
}