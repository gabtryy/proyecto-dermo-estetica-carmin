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
            $modelo->set_rif(trim($_POST['rif'] ?? ''));
            $modelo->set_nombreProveedor(trim($_POST['nombreProveedor'] ?? ''));
            $modelo->set_estadoDirProveedor(trim($_POST['estadoDirProveedor'] ?? ''));
            $modelo->set_municipioDirProveedor(trim($_POST['municipioDirProveedor'] ?? ''));
            $modelo->set_parroquiaDirProveedor(trim($_POST['parroquiaDirProveedor'] ?? ''));
            $modelo->set_telefono(trim($_POST['telefono'] ?? ''));
            $modelo->set_correo(trim($_POST['correo'] ?? ''));

            $res = $modelo->insertar();

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Proveedor registrado con éxito.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al registrar: ' . $res['mensaje']]);
            }
            break;

        case 'modificar':
            $modelo->set_idProveedor(trim($_POST['idProveedor'] ?? ''));
            $modelo->set_rif(trim($_POST['rif'] ?? ''));
            $modelo->set_nombreProveedor(trim($_POST['nombreProveedor'] ?? ''));
            $modelo->set_estadoDirProveedor(trim($_POST['estadoDirProveedor'] ?? ''));
            $modelo->set_municipioDirProveedor(trim($_POST['municipioDirProveedor'] ?? ''));
            $modelo->set_parroquiaDirProveedor(trim($_POST['parroquiaDirProveedor'] ?? ''));
            $modelo->set_telefono(trim($_POST['telefono'] ?? ''));
            $modelo->set_correo(trim($_POST['correo'] ?? ''));

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
