<?php

require_once 'modelo/clientes.php';

$modeloClientes = new Clientes();
$totalClientes = $modeloClientes->contar();

require_once 'vista/home.php';

?>
