<?php
  
// 1. VALIDACIÓN EXIGIDA POR EL PROFESOR: Verificar si el archivo existe antes de requerirlo
$ruta_modelo = "modelo/".$pagina.".php";

if (is_file($ruta_modelo)) {
    require_once($ruta_modelo);
} else {
    // Si es una petición AJAX (detectada por POST), devolvemos un JSON. Si no, un mensaje en pantalla.
    if (!empty($_POST['accion'])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['ok' => false, 'mensaje' => 'Error Crítico: El modelo no existe.']);
        exit;
    } else {
        die("Error Crítico: El archivo del modelo ($ruta_modelo) no se encuentra.");
    }
}

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
                'fechadenacimiento' => trim($_POST['fechadenacimiento'] ?? ''),
                'estado' => trim($_POST['estado'] ?? ''),
                'municipio' => trim($_POST['municipio'] ?? ''),
                'parroquia' => trim($_POST['parroquia'] ?? ''),
            ];
            
            try {
                $modelo->set_cedula($datos['cedula']);
                $modelo->set_nombres($datos['nombres']);
                $modelo->set_fechadenacimiento($datos['fechadenacimiento']);
                $modelo->set_estado($datos['estado']);
                $modelo->set_municipio($datos['municipio']);
                $modelo->set_parroquia($datos['parroquia']);

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
                'fechadenacimiento' => trim($_POST['fechadenacimiento'] ?? ''),
                'estado' => trim($_POST['estado'] ?? ''),
                'municipio' => trim($_POST['municipio'] ?? ''),
                'parroquia' => trim($_POST['parroquia'] ?? ''),
            ];
            try {
                $modelo->set_cedula($datos['cedula']);
                $modelo->set_nombres($datos['nombres']);
                $modelo->set_fechadenacimiento($datos['fechadenacimiento']);
                $modelo->set_estado($datos['estado']);
                $modelo->set_municipio($datos['municipio']);
                $modelo->set_parroquia($datos['parroquia']);

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

// 2. Validación de la vista
if (is_file("vista/modulos/".$pagina.".php")) {
    require_once("vista/modulos/".$pagina.".php");
}