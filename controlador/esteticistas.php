<?php
  
require_once("modelo/".$pagina.".php");  

$modelo = new Esteticistas();

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
                'telefono' => trim($_POST['telefono'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'especialidad' => trim($_POST['especialidad'] ?? ''),
            ];
            try {
                $modelo->set_cedula($datos['cedula']);
                $modelo->set_nombres($datos['nombres']);
                $modelo->set_telefono($datos['telefono']);
                $modelo->set_correo($datos['correo']);
                $modelo->set_especialidad($datos['especialidad']);

                if ($modelo->insertar()) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Esteticista guardado correctamente.']);
                    exit;
                } else {
                    $error = $modelo->getUltimoError();
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
                    exit;
                }
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el esteticista.']);
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
                    echo json_encode(['ok' => true, 'mensaje' => 'Esteticista eliminado correctamente.']);
                } else {
                    $error = $modelo->getUltimoError();
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo eliminar: ' . $error]);
                }
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar el esteticista.']);
            }
            exit;

        case 'modificar':
            $datos = [
                'cedula' => trim($_POST['cedula'] ?? ''),
                'nombres' => trim($_POST['nombres'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'especialidad' => trim($_POST['especialidad'] ?? ''),
            ];
            try {
                $modelo->set_cedula($datos['cedula']);
                $modelo->set_nombres($datos['nombres']);
                $modelo->set_telefono($datos['telefono']);
                $modelo->set_correo($datos['correo']);
                $modelo->set_especialidad($datos['especialidad']);

                if ($modelo->modificar()) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Esteticista modificado correctamente.']);
                    exit;
                } else {
                    $error = $modelo->getUltimoError();
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
                    exit;
                }
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudo modificar el esteticista.']);
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
