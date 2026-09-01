<?php

// llamada al archivo que contiene la clase
// usuarios, en ella estará el código que me permitirá
// guardar, consultar y modificar dentro de mi base de datos.

if (!is_file("modelo/".$pagina.".php")) {
    echo "Falta definir la clase ".$pagina;
    exit;
}

require_once("modelo/".$pagina.".php");

if (is_file("vista/modulos/".$pagina.".php")) {
    $modelo = new Clientes();

    if (!empty($_POST)) {
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'consultar') {
            echo json_encode([
                'ok' => true,
                'data' => $modelo->listar(),
            ]);
            exit;
        }

        if ($accion === 'eliminar') {
            $modelo->set_cedula(trim($_POST['cedula'] ?? ''));
            $resp = $modelo->eliminar($modelo->get_cedula());

            if ($resp === 'eliminado') {
                echo json_encode(['ok' => true, 'mensaje' => 'Cliente eliminado correctamente.']);
            } elseif ($resp === 'no existe') {
                echo json_encode(['ok' => false, 'mensaje' => 'Cliente no encontrado.']);
            } else {
                $msg = (strpos($resp, 'error:') === 0) ? trim(substr($resp, 6)) : 'No se pudo eliminar el cliente.';
                echo json_encode(['ok' => false, 'mensaje' => $msg]);
            }
            exit;
        }

        $modelo->set_cedula(trim($_POST['cedula'] ?? ''));
        $modelo->set_nombres(trim($_POST['nombres'] ?? ''));
        $modelo->set_fechadenacimiento(trim($_POST['fechadenacimiento'] ?? ''));
        $modelo->set_estado(trim($_POST['estado'] ?? ''));
        $modelo->set_municipio(trim($_POST['municipio'] ?? ''));
        $modelo->set_parroquia(trim($_POST['parroquia'] ?? ''));
        $modelo->set_telefono(trim($_POST['telefono'] ?? ''));

        if ($accion === 'incluir') {
            $resp = $modelo->insertar();
            if ($resp === 'insertado') {
                echo json_encode(['ok' => true, 'mensaje' => 'Cliente guardado correctamente.']);
            } elseif ($resp === 'duplicado') {
                echo json_encode(['ok' => false, 'mensaje' => 'El cliente ya existe (cédula duplicada).']);
            } else {
                $msg = (strpos($resp, 'error:') === 0) ? trim(substr($resp, 6)) : 'No se pudo guardar el cliente.';
                echo json_encode(['ok' => false, 'mensaje' => $msg]);
            }
            exit;
        }

        if ($accion === 'modificar') {
            $resp = $modelo->modificar();
            if ($resp === 'modificado') {
                echo json_encode(['ok' => true, 'mensaje' => 'Cliente modificado correctamente.']);
            } elseif ($resp === 'no existe') {
                echo json_encode(['ok' => false, 'mensaje' => 'Cliente no encontrado.']);
            } else {
                $msg = (strpos($resp, 'error:') === 0) ? trim(substr($resp, 6)) : 'No se pudo modificar el cliente.';
                echo json_encode(['ok' => false, 'mensaje' => $msg]);
            }
            exit;
        }

        echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
        exit;
    }

    require_once("vista/modulos/".$pagina.".php");
} else {
    echo "pagina en construccion";
}
