<?php


if (!is_file("modelo/conexion.php")){
    echo "Falta definir la clase Servicio";
    exit;
} else {
    require_once("modelo/conexion.php"); 
}

$ruta_modelo = "modelo/servicios.php";
if (is_file($ruta_modelo)) {
    require_once($ruta_modelo);
} else {
    if (!empty($_POST['accion'])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(['ok' => false, 'mensaje' => 'Error Crítico: El modelo de servicios no existe.']);
        exit;
    } else {
        die("Error Crítico: El archivo del modelo ($ruta_modelo) no se encuentra.");
    }
}

$modelo = new Servicio();


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
                    'mensaje' => 'No se pudieron extraer los servicios registrados.',
                    'data' => []
                ]);
            }
            break;

        case 'incluir':
            $modelo->set_nombreServicio(trim($_POST['nombreServicio'] ?? ''));
            $modelo->set_precio(trim($_POST['precio'] ?? ''));
            $modelo->set_descripcion(trim($_POST['descripcion'] ?? ''));

         
            $res = $modelo->insertar(); 
            
            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Servicio registrado con éxito.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al registrar: ' . $res['mensaje']]);
            }
            break;

        case 'modificar':
            $modelo->set_idServicio(trim($_POST['idServicio'] ?? ''));
            $modelo->set_nombreServicio(trim($_POST['nombreServicio'] ?? ''));
            $modelo->set_precio(trim($_POST['precio'] ?? ''));
            $modelo->set_descripcion(trim($_POST['descripcion'] ?? ''));

            $res = $modelo->modificar(); 

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Servicio modificado correctamente.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Error al modificar: ' . $res['mensaje']]);
            }
            break;

        case 'eliminar':
            $modelo->set_idServicio(trim($_POST['idServicio'] ?? ''));
            
            $res = $modelo->eliminar(); 

            if ($res['resultado'] === 'exito') {
                echo json_encode(['ok' => true, 'mensaje' => 'Servicio eliminado correctamente.']);
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

if (is_file("vista/modulos/servicios.php")){
    require_once("vista/modulos/servicios.php"); 
}
else{
    echo "Página en construcción";
}
?>