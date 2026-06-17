<?php
  
// 1. VALIDACIÓN EXIGIDA POR EL PROFESOR: Verificar si el archivo existe antes de requerirlo
$ruta_modelo = "modelo/".$pagina.".php";

if (is_file($ruta_modelo)) {
    require_once($ruta_modelo);
} else {
    
    echo "Falta definir la clase ".$pagina;
	exit;
}

$modelo = new Productos();

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
                'nombreProducto' => trim($_POST['nombreProducto'] ?? ''),
                'marca' => trim($_POST['marca'] ?? ''),
                'idProveedor' => trim($_POST['idProveedor'] ?? ''),
                'precioProducto' => trim($_POST['precioProducto'] ?? ''),
                'cantidadActual' => trim($_POST['cantidadActual'] ?? ''),
                'tipoProducto' => trim($_POST['tipoProducto'] ?? ''),
            ];
            
            try {
                $modelo->set_nombre($datos['nombreProducto']);
                $modelo->set_marca($datos['marca']);
                $modelo->set_idProveedor($datos['idProveedor']);
                $modelo->set_precio($datos['precioProducto']);
                $modelo->set_cantidad($datos['cantidadActual']);
                $modelo->set_tipoProducto($datos['tipoProducto']);

                if ($modelo->insertar()) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Producto guardado correctamente.']);
                    exit;
                } else {
                    $error = $modelo->getUltimoError();
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
                    exit;
                }
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar el producto.']);
                exit;
            }
            
        case 'eliminar':
            // Aceptar tanto 'id' como 'idProducto' enviado desde el cliente
            $id = trim($_POST['id'] ?? $_POST['idProducto'] ?? '');
            if (!$id) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'ID no proporcionado.']);
                exit;
            }
            try {
                if ($modelo->eliminar($id)) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Producto eliminado correctamente.']);
                } else {
                    $error = $modelo->getUltimoError();
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo eliminar: ' . $error]);
                }
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar el producto.']);
            }
            exit;

        case 'proveedores':
            try {
                echo json_encode(['ok' => true, 'data' => $modelo->listarProveedores()]);
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron obtener proveedores.']);
            }
            exit;
            
        case 'modificar':
            $datos = [
                'idProducto' => trim($_POST['idProducto'] ?? ''),
                'nombreProducto' => trim($_POST['nombreProducto'] ?? ''),
                'marca' => trim($_POST['marca'] ?? ''),
                'idProveedor' => trim($_POST['idProveedor'] ?? ''),
                'precioProducto' => trim($_POST['precioProducto'] ?? ''),
                'cantidadActual' => trim($_POST['cantidadActual'] ?? ''),
                'tipoProducto' => trim($_POST['tipoProducto'] ?? ''),
                'parroquia' => trim($_POST['parroquia'] ?? ''),
            ];
            try {
                $modelo->set_id($datos['idProducto']);
                $modelo->set_nombre($datos['nombreProducto']);
                $modelo->set_marca($datos['marca']);
                $modelo->set_idProveedor($datos['idProveedor']);
                $modelo->set_precio($datos['precioProducto']);
                $modelo->set_cantidad($datos['cantidadActual']);
                $modelo->set_tipoProducto($datos['tipoProducto']);

                if ($modelo->modificar()) {
                    http_response_code(200);
                    echo json_encode(['ok' => true, 'mensaje' => 'Producto modificado correctamente.']);
                    exit;
                } else {
                    $error = $modelo->getUltimoError();
                    http_response_code(500);
                    echo json_encode(['ok' => false, 'mensaje' => 'Error en la base de datos: ' . $error]);
                    exit;
                }
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudo modificar el producto.']);
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