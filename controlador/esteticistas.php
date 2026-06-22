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
            $res = $modelo->listar();
            if (!$res['ok']) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al obtener datos: ' . ($res['error'] ?? '')]);
            } else {
                echo json_encode(['ok' => true, 'data' => $res['data']]);
            }
            exit;

        case 'especialidades':
            $res = $modelo->listarEspecialidades();
            if (!$res['ok']) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al obtener especialidades: ' . ($res['error'] ?? '')]);
            } else {
                echo json_encode(['ok' => true, 'data' => $res['data']]);
            }
            exit;

        case 'incluir':
            $datos = [
                'cedula' => trim($_POST['cedula'] ?? ''),
                'nombres' => trim($_POST['nombres'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
                'especialidad' => trim($_POST['especialidad'] ?? ''),
                'fechaNacimiento' => trim($_POST['fechaNacimiento'] ?? ''),
            ];

            // Validaciones básicas para cédula
            if (empty($datos['cedula'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula es obligatoria.']);
                exit;
            }
            if (!preg_match('/^\d+$/', $datos['cedula'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula inválida. Solo se permiten dígitos.']);
                exit;
            }
            if (strlen($datos['cedula']) < 6 || strlen($datos['cedula']) > 12) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula debe tener entre 6 y 12 dígitos.']);
                exit;
            }

            // verificar existencia
            $ex = $modelo->existeCedula($datos['cedula']);
            if (!$ex['ok']) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al verificar cédula: ' . ($ex['error'] ?? '')]);
                exit;
            }
            if ($ex['exists']) {
                http_response_code(409);
                echo json_encode(['ok' => false, 'mensaje' => 'La cédula ya está registrada.']);
                exit;
            }

            try {
                $modelo->set_cedula($datos['cedula']);
                $modelo->set_nombres($datos['nombres']);
                $modelo->set_telefono($datos['telefono']);
                $modelo->set_correo($datos['correo']);
                $modelo->set_especialidad($datos['especialidad']);
                $modelo->set_fechaNacimiento($datos['fechaNacimiento']);

                $res = $modelo->insertar();
                if ($res['ok']) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Esteticista guardado correctamente.']);
                    exit;
                } else {
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . ($res['error'] ?? '')]);
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

            $ex = $modelo->existeCedula($cedula);
            if (!$ex['ok']) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al verificar cédula: ' . ($ex['error'] ?? '')]);
                exit;
            }
            if (!$ex['exists']) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula no encontrada.']);
                exit;
            }

            try {
                $res = $modelo->eliminar($cedula);
                if ($res['ok']) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Esteticista eliminado correctamente.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo eliminar: ' . ($res['error'] ?? '')]);
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
                'fechaNacimiento' => trim($_POST['fechaNacimiento'] ?? ''),
            ];

            if (empty($datos['cedula'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula es obligatoria.']);
                exit;
            }
            if (!preg_match('/^\d+$/', $datos['cedula'])) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula inválida.']);
                exit;
            }

            $ex = $modelo->existeCedula($datos['cedula']);
            if (!$ex['ok']) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al verificar cédula: ' . ($ex['error'] ?? '')]);
                exit;
            }
            if (!$ex['exists']) {
                http_response_code(404);
                echo json_encode(['ok' => false, 'mensaje' => 'Cédula no encontrada.']);
                exit;
            }

            try {
                $modelo->set_cedula($datos['cedula']);
                $modelo->set_nombres($datos['nombres']);
                $modelo->set_telefono($datos['telefono']);
                $modelo->set_correo($datos['correo']);
                $modelo->set_especialidad($datos['especialidad']);
                $modelo->set_fechaNacimiento($datos['fechaNacimiento']);

                $res = $modelo->modificar();
                if ($res['ok']) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Esteticista modificado correctamente.']);
                    exit;
                } else {
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . ($res['error'] ?? '')]);
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