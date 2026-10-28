<?php
$accion = $_POST['accion'] ?? '';
if ($accion !== '') {
    header('Content-Type: application/json; charset=utf-8');
}

require_once 'modelo/configuracion.php';
$modelo = new Configuracion();

if ($accion === '') {
    require_once 'vista/modulos/configuracion.php';
    exit;
}

try {
    switch ($accion) {
        case 'consultar':
            echo json_encode(['ok' => true, 'data' => $modelo->listar()]);
            exit;

        case 'agregarMoneda':
            $nombre = trim((string) ($_POST['nombre_moneda'] ?? ''));
            $simbolo = trim((string) ($_POST['simbolo'] ?? ''));
            if (
                !preg_match('/^.{1,50}$/us', $nombre) ||
                !preg_match('/^.{1,5}$/us', $simbolo)
            ) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Ingrese el nombre de la moneda y un símbolo de hasta 5 caracteres.']);
                exit;
            }
            $modelo->agregarMoneda($nombre, $simbolo);
            echo json_encode(['ok' => true, 'mensaje' => 'Moneda agregada correctamente.']);
            exit;

        case 'agregarMetodo':
            $nombre = trim((string) ($_POST['nom_MetodoPago'] ?? ''));
            if (!preg_match('/^.{1,50}$/us', $nombre)) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Ingrese el nombre del método de pago, con un máximo de 50 caracteres.']);
                exit;
            }
            $modelo->agregarMetodo($nombre);
            echo json_encode(['ok' => true, 'mensaje' => 'Método de pago agregado correctamente.']);
            exit;

        case 'guardarTasa':
            $idMoneda = filter_var($_POST['id_moneda'] ?? null, FILTER_VALIDATE_INT);
            $fecha = trim((string) ($_POST['fecha_tasa'] ?? ''));
            $tasa = trim((string) ($_POST['monto_tasa'] ?? ''));
            $fechaValida = DateTime::createFromFormat('!Y-m-d', $fecha);
            $erroresFecha = DateTime::getLastErrors();
            if (
                !$idMoneda || $idMoneda < 1 ||
                !$fechaValida ||
                ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0)) ||
                $fechaValida->format('Y-m-d') !== $fecha ||
                $fecha > date('Y-m-d') ||
                !preg_match('/^\d{1,6}(?:\.\d{1,4})?$/', $tasa) ||
                (float) $tasa <= 0
            ) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'mensaje' => 'Indique una moneda válida, una fecha no futura y una tasa mayor que cero con hasta 4 decimales.']);
                exit;
            }
            $modelo->guardarTasa($idMoneda, $fecha, $tasa);
            echo json_encode([
                'ok' => true,
                'mensaje' => 'La nueva tasa fue registrada y se conservaron las anteriores.',
            ]);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
            exit;
    }
} catch (DomainException $e) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'mensaje' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Error en configuración de monedas: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo guardar la configuración. Verifique los datos e intente nuevamente.']);
}
