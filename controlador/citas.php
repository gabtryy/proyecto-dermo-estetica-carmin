<?php
if (!is_file("modelo/".$pagina.".php")) {
    echo "Falta definir la clase ".$pagina;
    exit;
}

require_once("modelo/".$pagina.".php");

$modelo = new Citas();

if (!empty($_POST)) {
    $accion = $_POST['accion'] ?? '';

    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }

    if ($accion === 'consultar') {
        echo json_encode(['ok' => true, 'data' => $modelo->consultar()]);
        exit;
    }

    if ($accion === 'clientes') {
        echo json_encode(['ok' => true, 'data' => $modelo->listarClientes()]);
        exit;
    }

    if ($accion === 'esteticistas') {
        echo json_encode(['ok' => true, 'data' => $modelo->listarEsteticistas()]);
        exit;
    }

    if ($accion === 'servicios') {
        echo json_encode(['ok' => true, 'data' => $modelo->listarServicios()]);
        exit;
    }

    if ($accion === 'incluir') {
        $servicios = $_POST['servicios'] ?? ($_POST['servicios[]'] ?? []);
        $respuesta = $modelo->insertar([
            'cedulaCliente' => trim($_POST['cedulaCliente'] ?? ''),
            'cedulaEsteticista' => trim($_POST['cedulaEsteticista'] ?? ''),
            'fecha_cita' => trim($_POST['fecha_cita'] ?? ''),
            'hora' => trim($_POST['hora'] ?? ''),
            'servicios' => is_array($servicios) ? $servicios : [$servicios],
        ]);

        if ($respuesta['ok']) {
            echo json_encode(['ok' => true, 'mensaje' => $respuesta['mensaje']]);
        } else {
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => $respuesta['mensaje']]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
    exit;
}

if (is_file("vista/modulos/".$pagina.".php")) {
    require_once("vista/modulos/".$pagina.".php");
} else {
    echo "pagina en construccion";
}
?>
