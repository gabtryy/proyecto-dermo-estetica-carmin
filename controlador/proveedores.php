<?php

if (!is_file("modelo/conexion.php")) {
    echo "Falta definir la clase Proveedor";
    exit;
} else {
    require_once("modelo/conexion.php");
}

$ruta_modelo = "modelo/proveedores.php";
if (is_file($ruta_modelo)) {
    require_once($ruta_modelo);
} else {
    if (!empty($_POST['accion'])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'Error Crítico: El modelo de proveedores no existe.']);
        exit;
    } else {
        die("Error Crítico: El archivo del modelo ($ruta_modelo) no se encuentra.");
    }
}

function validarDatosProveedor(array &$datos): ?string
{
    $rif = strtoupper(trim($datos['rif']));
    $rif = preg_replace('/^J-/i', '', $rif);
    $rif = str_replace('-', '', $rif);
    if (!preg_match('/^\d{9}$/', $rif)) {
        return 'El RIF debe tener 8 dígitos para el negocio y un dígito final.';
    }
    $datos['rif'] = 'J-' . substr($rif, 0, 8) . '-' . substr($rif, 8, 1);

    $nombre = trim($datos['nombreProveedor']);
    if (mb_strlen($nombre, 'UTF-8') < 2 || mb_strlen($nombre, 'UTF-8') > 100 || preg_match('/[\x00-\x1F]/', $nombre)) {
        return 'El nombre del proveedor debe tener entre 2 y 100 caracteres.';
    }

    if ($datos['correo'] === '' || strlen($datos['correo']) > 30 || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        return 'Ingrese un correo válido de máximo 30 caracteres.';
    }

    if (
        $datos['telefono'] !== '' &&
        !preg_match('/^04(?:12|13|14|16|24|26)\d{7}$/', $datos['telefono'])
    ) {
        return 'Ingrese un teléfono válido: seleccione un prefijo y escriba los 7 dígitos restantes.';
    }

    foreach (['estadoDirProveedor', 'municipioDirProveedor', 'parroquiaDirProveedor'] as $campo) {
        if (mb_strlen($datos[$campo], 'UTF-8') > 100 || preg_match('/[\x00-\x1F]/', $datos[$campo])) {
            return 'Revise los datos de la dirección del proveedor.';
        }
    }

    return null;
}

$modelo = new Proveedor();

$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
    header('Content-Type: application/json');

    switch ($accion) {
        case 'consultar':
            $datos = $modelo->listar();
            if (is_array($datos)) {
                echo json_encode([
                    'ok' => true,
                    'data' => $datos
                ]);
            } else {
                echo json_encode([
                    'ok' => false,
                    'mensaje' => 'No se pudieron extraer los proveedores registrados.',
                    'data' => []
                ]);
            }
            break;

        case 'incluir':
            $datos = [
                'rif' => trim($_POST['rif'] ?? ''),
                'nombreProveedor' => trim($_POST['nombreProveedor'] ?? ''),
                'estadoDirProveedor' => trim($_POST['estadoDirProveedor'] ?? ''),
                'municipioDirProveedor' => trim($_POST['municipioDirProveedor'] ?? ''),
                'parroquiaDirProveedor' => trim($_POST['parroquiaDirProveedor'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
            ];
            $error = validarDatosProveedor($datos);
            if ($error !== null) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => $error]);
                exit;
            }
            $modelo->set_rif($datos['rif']);
            $modelo->set_nombreProveedor($datos['nombreProveedor']);
            $modelo->set_estadoDirProveedor($datos['estadoDirProveedor']);
            $modelo->set_municipioDirProveedor($datos['municipioDirProveedor']);
            $modelo->set_parroquiaDirProveedor($datos['parroquiaDirProveedor']);
            $modelo->set_telefono($datos['telefono']);
            $modelo->set_correo($datos['correo']);

            $res = $modelo->insertar();

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Proveedor registrado con éxito.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al registrar: ' . $res['mensaje']]);
            }
            break;

        case 'modificar':
            $modelo->set_idProveedor(trim($_POST['idProveedor'] ?? ''));
            $datos = [
                'rif' => trim($_POST['rif'] ?? ''),
                'nombreProveedor' => trim($_POST['nombreProveedor'] ?? ''),
                'estadoDirProveedor' => trim($_POST['estadoDirProveedor'] ?? ''),
                'municipioDirProveedor' => trim($_POST['municipioDirProveedor'] ?? ''),
                'parroquiaDirProveedor' => trim($_POST['parroquiaDirProveedor'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'correo' => trim($_POST['correo'] ?? ''),
            ];
            $error = validarDatosProveedor($datos);
            if ($error !== null) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => $error]);
                exit;
            }
            $modelo->set_rif($datos['rif']);
            $modelo->set_nombreProveedor($datos['nombreProveedor']);
            $modelo->set_estadoDirProveedor($datos['estadoDirProveedor']);
            $modelo->set_municipioDirProveedor($datos['municipioDirProveedor']);
            $modelo->set_parroquiaDirProveedor($datos['parroquiaDirProveedor']);
            $modelo->set_telefono($datos['telefono']);
            $modelo->set_correo($datos['correo']);

            $res = $modelo->modificar();

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Proveedor modificado correctamente.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al modificar: ' . $res['mensaje']]);
            }
            break;

        case 'eliminar':
            $modelo->set_idProveedor(trim($_POST['idProveedor'] ?? ''));

            $res = $modelo->eliminar();

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Proveedor eliminado correctamente.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar: ' . $res['mensaje']]);
            }
            break;

        default:
            echo json_encode(['ok' => false, 'mensaje' => 'Acción no reconocida de manera interna.']);
            break;
    }

    exit;
}

if (is_file("vista/modulos/proveedores.php")) {
    require_once("vista/modulos/proveedores.php");
} else {
    echo "Página en construcción";
}
?>
