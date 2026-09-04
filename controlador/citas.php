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
        $modelo->set_cedulaCliente(trim($_POST['cedulaCliente'] ?? ''));
        $modelo->set_cedulaEsteticista(trim($_POST['cedulaEsteticista'] ?? ''));
        $modelo->set_fechaCita(trim($_POST['fecha_cita'] ?? ''));
        $modelo->set_hora(trim($_POST['hora'] ?? ''));
        $modelo->set_servicios(is_array($servicios) ? $servicios : [$servicios]);
        $respuesta = $modelo->insertar();

        if ($respuesta['ok']) {
            echo json_encode(['ok' => true, 'mensaje' => $respuesta['mensaje']]);
        } else {
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensaje' => $respuesta['mensaje']]);
        }
        exit;
    }

    if ($accion === 'modificar') {
        $servicios = $_POST['servicios'] ?? ($_POST['servicios[]'] ?? []);
        $modelo->set_idCita($_POST['idCita'] ?? 0);
        $modelo->set_cedulaCliente(trim($_POST['cedulaCliente'] ?? ''));
        $modelo->set_cedulaEsteticista(trim($_POST['cedulaEsteticista'] ?? ''));
        $modelo->set_fechaCita(trim($_POST['fecha_cita'] ?? ''));
        $modelo->set_hora(trim($_POST['hora'] ?? ''));
        $modelo->set_servicios(is_array($servicios) ? $servicios : [$servicios]);
        $respuesta = $modelo->modificar();
        if (!$respuesta['ok']) {
            http_response_code(400);
        }
        echo json_encode($respuesta);
        exit;
    }

    if ($accion === 'eliminar') {
        $modelo->set_idCita((int) ($_POST['idCita'] ?? 0));
        $respuesta = $modelo->eliminar();
        if (!$respuesta['ok']) {
            http_response_code(400);
        }
        echo json_encode($respuesta);
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
