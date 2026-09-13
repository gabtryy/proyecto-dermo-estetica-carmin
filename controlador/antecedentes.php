<?php

if (!is_file("modelo/conexion.php")) {
    echo "Falta definir la clase Antecedente";
    exit;
} else {
    require_once("modelo/conexion.php");
}

$ruta_modelo = "modelo/antecedentes.php";
if (is_file($ruta_modelo)) {
    require_once($ruta_modelo);
} else {
    if (!empty($_POST['accion'])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'Error Crítico: El modelo de antecedentes no existe.']);
        exit;
    } else {
        die("Error Crítico: El archivo del modelo ($ruta_modelo) no se encuentra.");
    }
}

$modelo = new Antecedente();

$accion = $_POST['accion'] ?? '';

if ($accion !== '') {
    header('Content-Type: application/json');

    switch ($accion) {
        case 'consultar':
            $datos = $modelo->listar();
            if (is_array($datos)) {
                echo json_encode(['ok' => true, 'data' => $datos]);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron extraer los antecedentes registrados.', 'data' => []]);
            }
            break;

        case 'clientes':
            $datos = $modelo->listarClientes();
            if (is_array($datos)) {
                echo json_encode(['ok' => true, 'data' => $datos]);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron extraer los clientes.', 'data' => []]);
            }
            break;

        case 'tipos_antecedentes':
            $datos = $modelo->listarTiposAntecedentes();
            if (is_array($datos)) {
                echo json_encode(['ok' => true, 'data' => $datos]);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'No se pudieron extraer los tipos de antecedentes.', 'data' => []]);
            }
            break;

        case 'antecedentesCliente':
            $cedulaCliente = trim($_POST['cedulaCliente'] ?? '');
            if ($cedulaCliente === '') {
                echo json_encode(['ok' => false, 'mensaje' => 'Debe indicar un cliente.']);
                break;
            }
            $datos = $modelo->listarPorCliente($cedulaCliente);
            echo json_encode(['ok' => true, 'data' => $datos]);
            break;

        case 'incluir':
        case 'modificar':
            $cedulaCliente = trim($_POST['cedulaCliente'] ?? '');
            $tipos = $_POST['id_tipo_antecedente'] ?? [];
            $descripciones = $_POST['descripcion_antecedente'] ?? [];

            if ($cedulaCliente === '') {
                echo json_encode(['ok' => false, 'mensaje' => 'Debe seleccionar un cliente.']);
                break;
            }

            if (!is_array($tipos) || count($tipos) === 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'Debe seleccionar al menos un tipo de antecedente.']);
                break;
            }

            $res = $modelo->guardarCliente($cedulaCliente, $tipos, $descripciones);

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => $res['mensaje']]);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar: ' . $res['mensaje']]);
            }
            break;

        case 'eliminar':
            $modelo->set_id_antecedente(trim($_POST['id_antecedente'] ?? ''));
            $res = $modelo->eliminar();

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Antecedente eliminado correctamente.']);
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

if (is_file("vista/modulos/antecedentes.php")) {
    require_once("vista/modulos/antecedentes.php");
} else {
    echo "Página en construcción";
}
?>
