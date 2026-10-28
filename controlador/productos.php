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
            http_response_code(500);
            echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron consultar los productos.']);
        }
        exit;

    case 'catalogos':
        try {
            echo json_encode(['ok' => true, 'data' => $modelo->catalogos()]);
        } catch (Throwable $e) {
            error_log('No se pudieron cargar los catálogos de productos: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron cargar proveedores y tipos de producto.']);
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
            'cantidadActual' => filter_var($_POST['cantidadActual'] ?? null, FILTER_VALIDATE_INT),
            'idTipo' => filter_var($_POST['tipoProducto'] ?? null, FILTER_VALIDATE_INT),
        ];

        if (
            $datos['nombreProducto'] === '' ||
            strlen($datos['nombreProducto']) > 100 ||
            strlen($datos['marca']) > 50 ||
            !$datos['idProveedor'] ||
            !preg_match('/^\d+(?:\.\d{1,2})?$/', $datos['precioProducto']) ||
            (float) $datos['precioProducto'] <= 0 ||
            (float) $datos['precioProducto'] > 99999999.99 ||
            $datos['cantidadActual'] === false ||
            $datos['cantidadActual'] < 0 ||
            $datos['idTipo'] === false ||
            $datos['idTipo'] < 1 ||
            ($accion === 'modificar' && (!$idProducto || $idProducto < 1))
        ) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => 'Revise nombre, marca, proveedor, precio, cantidad y tipo de producto.']);
            exit;
        }

        $resultado = $accion === 'incluir'
            ? $modelo->insertar($datos)
            : $modelo->modificar($idProducto, $datos);
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
