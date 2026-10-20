<?php
$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
    header('Content-Type: application/json; charset=utf-8');
}

require_once 'modelo/ventas.php';
$modelo = new Venta();

if ($accion !== '') {
    switch ($accion) {
        case 'consultar':
            try {
                echo json_encode(['ok' => true, 'data' => $modelo->listar()]);
            } catch (Throwable $e) {
                error_log('No se pudieron consultar las ventas: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron consultar las ventas.']);
            }
            exit;

        case 'catalogos':
            try {
                echo json_encode(['ok' => true, 'data' => $modelo->catalogos()]);
            } catch (Throwable $e) {
                error_log('No se pudieron consultar los datos para ventas: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron cargar los datos para registrar ventas.']);
            }
            exit;

        case 'detalle':
            $idVenta = filter_var($_POST['idVenta'] ?? null, FILTER_VALIDATE_INT);
            if (!$idVenta || $idVenta < 1) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'La venta indicada no es válida.']);
                exit;
            }
            try {
                $venta = $modelo->obtener($idVenta);
                if (!$venta) {
                    http_response_code(404);
                    echo json_encode(['ok' => false, 'mensaje' => 'La venta ya no existe.']);
                    exit;
                }
                echo json_encode(['ok' => true, 'data' => $venta]);
            } catch (Throwable $e) {
                error_log('No se pudo consultar el detalle de la venta: ' . $e->getMessage());
                http_response_code(500);
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudo consultar el detalle de la venta.']);
            }
            exit;

        case 'registrar':
            $datos = [
                'cedulaCliente' => $_POST['cedulaCliente'] ?? '',
                'fechaCompra' => $_POST['fechaCompra'] ?? '',
                'idMetodoPago' => $_POST['idMetodoPago'] ?? '',
                'id_moneda' => $_POST['id_moneda'] ?? '',
                'tasa_cobrada' => $_POST['tasa_cobrada'] ?? '',
                'referencia' => $_POST['referencia'] ?? '',
                'productos' => json_decode($_POST['productos'] ?? '', true),
            ];
            $resultado = $modelo->registrar($datos);
            if (!$resultado['ok']) {
                http_response_code(400);
            }
            echo json_encode($resultado);
            exit;

        case 'modificar':
            $idVenta = filter_var($_POST['idVenta'] ?? null, FILTER_VALIDATE_INT);
            if (!$idVenta || $idVenta < 1) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'La venta indicada no es válida.']);
                exit;
            }
            $datos = [
                'cedulaCliente' => $_POST['cedulaCliente'] ?? '',
                'fechaCompra' => $_POST['fechaCompra'] ?? '',
                'idMetodoPago' => $_POST['idMetodoPago'] ?? '',
                'id_moneda' => $_POST['id_moneda'] ?? '',
                'tasa_cobrada' => $_POST['tasa_cobrada'] ?? '',
                'referencia' => $_POST['referencia'] ?? '',
                'productos' => json_decode($_POST['productos'] ?? '', true),
            ];
            $resultado = $modelo->modificar($idVenta, $datos);
            if (!$resultado['ok']) {
                http_response_code(400);
            }
            echo json_encode($resultado);
            exit;

        case 'eliminar':
            $idVenta = filter_var($_POST['idVenta'] ?? null, FILTER_VALIDATE_INT);
            $resultado = $modelo->eliminar($idVenta ?: 0);
            if (!$resultado['ok']) {
                http_response_code(400);
            }
            echo json_encode($resultado);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
            exit;
    }
}

require_once 'vista/modulos/ventas.php';
