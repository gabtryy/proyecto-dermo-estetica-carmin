<?php
if ((int) ($_SESSION['nivel'] ?? 0) !== 1) {
    http_response_code(403);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'mensaje' => 'No tienes permiso para administrar usuarios.']);
    } else {
        echo 'No tienes permiso para administrar usuarios.';
    }
    exit;
}

require_once 'modelo/usuarios.php';
$modelo = new Usuarios();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'consultar') {
            echo json_encode(['ok' => true, 'data' => $modelo->listar()]);
            exit;
        }

        if ($accion === 'roles') {
            echo json_encode(['ok' => true, 'data' => $modelo->listarRoles()]);
            exit;
        }

        if ($accion === 'incluir') {
            $cedula = isset($_POST['cedula']) && is_string($_POST['cedula']) ? trim($_POST['cedula']) : '';
            $nombreUsuario = isset($_POST['usuario']) && is_string($_POST['usuario']) ? trim($_POST['usuario']) : '';
            $clave = isset($_POST['clave']) && is_string($_POST['clave']) ? $_POST['clave'] : '';
            $rol = isset($_POST['rol']) && is_string($_POST['rol']) ? trim($_POST['rol']) : '';

            if (!preg_match('/^[1-9]\d{6,9}$/', $cedula) || (int) $cedula > 2147483647) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'La cédula debe tener entre 7 y 10 dígitos válidos.']);
                exit;
            }
            if (!preg_match('/^[A-Za-z0-9._-]{3,32}$/', $nombreUsuario)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'El usuario debe tener entre 3 y 32 caracteres: letras, números, punto, guion o guion bajo.']);
                exit;
            }
            if ($clave === '' || strlen($clave) > 255) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'La clave es obligatoria y no debe superar 255 caracteres.']);
                exit;
            }
            if (!preg_match('/^[1-9]\d*$/', $rol) || !$modelo->existeRol($rol)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Seleccione un rol válido.']);
                exit;
            }

            $modelo->set_cedula($cedula);
            $modelo->set_rol($rol);
            $modelo->set_usuario($nombreUsuario);
            $modelo->set_clave($clave);
            $resultado = $modelo->insertar();
            if ($resultado === 'insertado') {
                echo json_encode(['ok' => true, 'mensaje' => 'Usuario creado correctamente.']);
            } elseif ($resultado === 'cedula_duplicada') {
                http_response_code(409);
                echo json_encode(['ok' => false, 'mensaje' => 'Ya existe un usuario registrado con esa cédula.']);
            } else {
                http_response_code(409);
                echo json_encode(['ok' => false, 'mensaje' => 'Ese nombre de usuario ya está registrado.']);
            }
            exit;
        }

        http_response_code(400);
        echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
    } catch (PDOException $e) {
        error_log('Error al administrar usuarios: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'No se pudo completar la operación. Inténtalo de nuevo.']);
    }
    exit;
}

require_once 'vista/modulos/usuarios.php';
