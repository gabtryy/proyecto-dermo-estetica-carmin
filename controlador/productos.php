<?php
$accion = $_POST['accion'] ?? '';
if ($accion !== '') {
    header('Content-Type: application/json; charset=utf-8');
}

require_once 'modelo/productos.php';
$modelo = new Productos();

if ($accion === '') {
    require_once 'vista/modulos/productos.php';
    exit;
}

switch ($accion) {
    case 'consultar':
        try {
            echo json_encode(['ok' => true, 'data' => $modelo->listar()]);
        } catch (Throwable $e) {
            error_log('No se pudieron consultar los productos: ' . $e->getMessage());
            http_response_code($e instanceof DomainException ? 400 : 500);
            echo json_encode([
                'ok' => false,
                'mensaje' => $e instanceof DomainException
                    ? $e->getMessage()
                    : 'No se pudieron consultar los productos.',
            ]);
        }
        exit;

    case 'catalogos':
        try {
            echo json_encode(['ok' => true, 'data' => $modelo->catalogos()]);
        } catch (Throwable $e) {
            error_log('No se pudieron cargar los catálogos de productos: ' . $e->getMessage());
            http_response_code($e instanceof DomainException ? 400 : 500);
            echo json_encode([
                'ok' => false,
                'mensaje' => $e instanceof DomainException
                    ? $e->getMessage()
                    : 'No se pudieron cargar proveedores y usos de producto.',
            ]);
        }
        exit;

    case 'incluir':
    case 'modificar':
        $idProducto = filter_var($_POST['idProducto'] ?? null, FILTER_VALIDATE_INT);
        $datos = [
            'nombreProducto' => trim((string) ($_POST['nombreProducto'] ?? '')),
            'marca' => trim((string) ($_POST['marca'] ?? '')),
            'idProveedor' => filter_var($_POST['idProveedor'] ?? null, FILTER_VALIDATE_INT),
            'precioProducto' => trim((string) ($_POST['precioProducto'] ?? '')),
            'cantidadActual' => trim((string) ($_POST['cantidadActual'] ?? '')),
            'idTipo' => filter_var($_POST['tipoProducto'] ?? null, FILTER_VALIDATE_INT),
        ];
        $patronCantidad = $accion === 'incluir'
            ? '/^\d+$/'
            : '/^\d+(?:\.\d{1,6})?$/';
        $maximoCantidad = $accion === 'incluir' ? 2147483647 : 2147483647.999999;

        if (
            $datos['nombreProducto'] === '' ||
            strlen($datos['nombreProducto']) > 100 ||
            strlen($datos['marca']) > 50 ||
            !$datos['idProveedor'] ||
            !preg_match('/^\d+(?:\.\d{1,2})?$/', $datos['precioProducto']) ||
            (float) $datos['precioProducto'] <= 0 ||
            (float) $datos['precioProducto'] > 99999999.99 ||
            !preg_match($patronCantidad, $datos['cantidadActual']) ||
            (float) $datos['cantidadActual'] < 0 ||
            (float) $datos['cantidadActual'] > $maximoCantidad ||
            $datos['idTipo'] === false ||
            $datos['idTipo'] < 1 ||
            ($accion === 'modificar' && (!$idProducto || $idProducto < 1))
        ) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => 'Revise nombre, marca, proveedor, precio, cantidad y uso del producto.']);
            exit;
        }

        $modelo->set_nombreProducto($datos['nombreProducto']);
        $modelo->set_marca($datos['marca']);
        $modelo->set_idProveedor((int) $datos['idProveedor']);
        $modelo->set_precioProducto($datos['precioProducto']);
        $modelo->set_cantidadActual($datos['cantidadActual']);
        $modelo->set_idTipo((int) $datos['idTipo']);

        if ($accion === 'modificar') {
            $modelo->set_idProducto((int) $idProducto);
        }

        $resultado = $accion === 'incluir'
            ? $modelo->insertar()
            : $modelo->modificar();
        if (!$resultado['ok']) {
            http_response_code(400);
        }
        echo json_encode([
            'ok' => $resultado['ok'],
            'mensaje' => $resultado['mensaje'] ?? (
                $accion === 'incluir' ? 'Producto guardado correctamente.' : 'Producto modificado correctamente.'
            ),
            'idProducto' => $resultado['insertId'] ?? null,
        ]);
        exit;

    case 'eliminar':
        $idProducto = filter_var($_POST['idProducto'] ?? $_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$idProducto || $idProducto < 1) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => 'Seleccione un producto válido.']);
            exit;
        }
        $resultado = $modelo->eliminar($idProducto);
        if (!$resultado['ok']) {
            http_response_code(400);
        }
        echo json_encode([
            'ok' => $resultado['ok'],
            'mensaje' => $resultado['ok']
                ? 'Producto eliminado correctamente.'
                : $resultado['mensaje'],
        ]);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
        exit;
}
