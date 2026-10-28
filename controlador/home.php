<?php

require_once 'modelo/clientes.php';
require_once 'modelo/citas.php';

$modeloClientes = new Clientes();
$totalClientes = $modeloClientes->contar();

$zonaHoraria = new DateTimeZone('America/Caracas');
$hoy = new DateTimeImmutable('today', $zonaHoraria);
$inicioSemana = $hoy->modify('monday this week');
$finSemana = $inicioSemana->modify('+6 days');
$modeloCitas = new Citas();
$citasSemana = $modeloCitas->listarPeriodo(
    $inicioSemana->format('Y-m-d'),
    $finSemana->format('Y-m-d')
);

$diasSemana = [
    ['nombre' => 'Lun', 'fecha' => $inicioSemana->format('Y-m-d'), 'total' => 0],
    ['nombre' => 'Mar', 'fecha' => $inicioSemana->modify('+1 day')->format('Y-m-d'), 'total' => 0],
    ['nombre' => 'Mié', 'fecha' => $inicioSemana->modify('+2 days')->format('Y-m-d'), 'total' => 0],
    ['nombre' => 'Jue', 'fecha' => $inicioSemana->modify('+3 days')->format('Y-m-d'), 'total' => 0],
    ['nombre' => 'Vie', 'fecha' => $inicioSemana->modify('+4 days')->format('Y-m-d'), 'total' => 0],
    ['nombre' => 'Sáb', 'fecha' => $inicioSemana->modify('+5 days')->format('Y-m-d'), 'total' => 0],
    ['nombre' => 'Dom', 'fecha' => $inicioSemana->modify('+6 days')->format('Y-m-d'), 'total' => 0],
];
$citasHoy = [];

foreach ($citasSemana as $cita) {
    foreach ($diasSemana as &$dia) {
        if ($dia['fecha'] === $cita['fecha_cita']) {
            $dia['total']++;
            break;
        }
    }
    unset($dia);

    if ($cita['fecha_cita'] === $hoy->format('Y-m-d')) {
        $citasHoy[] = $cita;
    }
}

$totalCitasSemana = count($citasSemana);
$meses = [
    1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
];
$dias = [
    1 => 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo',
];
$fechaHoyTexto = $dias[(int) $hoy->format('N')] . ', ' . $hoy->format('j') .
    ' de ' . $meses[(int) $hoy->format('n')] . ' de ' . $hoy->format('Y');

require_once 'vista/home.php';

?>
