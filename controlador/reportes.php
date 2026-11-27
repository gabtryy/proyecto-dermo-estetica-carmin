<?php
require_once 'modelo/reportes.php';

if (!in_array((int) ($_SESSION['nivel'] ?? 0), [1, 2], true)) {
    http_response_code(403);
    exit('No tienes permiso para generar reportes.');
}

$tipoReporte = (string) ($_POST['tipo'] ?? 'ventas');
$fechaHoy = new DateTimeImmutable('today', new DateTimeZone('America/Caracas'));
$fechaInicioPredeterminada = $fechaHoy->modify('first day of this month')->format('Y-m-d');
$fechaFinPredeterminada = $fechaHoy->format('Y-m-d');
$fechaInicio = (string) ($_POST['fechaInicio'] ?? $fechaInicioPredeterminada);
$fechaFin = (string) ($_POST['fechaFin'] ?? $fechaFinPredeterminada);
$cedulaClienteReporte = (string) ($_POST['cedulaCliente'] ?? '');
$cedulaEsteticistaReporte = (string) ($_POST['cedulaEsteticista'] ?? '');
$errorReporte = '';
$datosReporte = [];
$clientesReporte = [];
$esteticistasReporte = [];

try {
    $modeloReportes = new Reportes();
    $clientesReporte = $modeloReportes->listarClientes();
    $esteticistasReporte = $modeloReportes->listarEsteticistas();
} catch (Throwable $e) {
    error_log('No se pudieron cargar las personas para los reportes: ' . $e->getMessage());
    $errorReporte = 'No se pudieron cargar los clientes y esteticistas.';
    http_response_code(500);
}

if (!in_array($tipoReporte, ['ventas', 'citas', 'usuarios', 'esteticistas'], true)) {
    $tipoReporte = 'ventas';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errorReporte = 'El tipo de reporte seleccionado no es válido.';
    }
}

$validarFecha = static function (string $fecha): bool {
    $valor = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $errores = DateTimeImmutable::getLastErrors();

    return $valor !== false
        && ($errores === false || ($errores['warning_count'] === 0 && $errores['error_count'] === 0))
        && $valor->format('Y-m-d') === $fecha;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar']) && $errorReporte === '') {
    if (!$validarFecha($fechaInicio) || !$validarFecha($fechaFin)) {
        $errorReporte = 'Indica fechas válidas para generar el reporte.';
    } elseif ($fechaInicio > $fechaFin) {
        $errorReporte = 'La fecha inicial no puede ser posterior a la fecha final.';
    } elseif ($tipoReporte === 'usuarios' && (
        !ctype_digit($cedulaClienteReporte)
        || !in_array($cedulaClienteReporte, array_map(
            static function (array $cliente): string {
                return (string) $cliente['cedulaCliente'];
            },
            $clientesReporte
        ), true)
    )) {
        $errorReporte = 'Selecciona un cliente válido.';
    } elseif ($tipoReporte === 'esteticistas' && (
        !ctype_digit($cedulaEsteticistaReporte)
        || !in_array($cedulaEsteticistaReporte, array_map(
            static function (array $esteticista): string {
                return (string) $esteticista['cedulaEsteticista'];
            },
            $esteticistasReporte
        ), true)
    )) {
        $errorReporte = 'Selecciona un esteticista válido.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar']) && $errorReporte === '') {
    try {
        if ($tipoReporte === 'ventas') {
            $datosReporte = $modeloReportes->ventas($fechaInicio, $fechaFin);
        } elseif ($tipoReporte === 'citas') {
            $datosReporte = $modeloReportes->citas($fechaInicio, $fechaFin);
        } elseif ($tipoReporte === 'usuarios') {
            $datosReporte = $modeloReportes->usuarios($fechaInicio, $fechaFin, $cedulaClienteReporte);
        } else {
            $datosReporte = $modeloReportes->esteticistas($fechaInicio, $fechaFin, $cedulaEsteticistaReporte);
        }
    } catch (Throwable $e) {
        error_log('No se pudo generar el reporte: ' . $e->getMessage());
        $errorReporte = 'No se pudo generar el reporte. Inténtalo nuevamente.';
        http_response_code(500);
    }

    if ($errorReporte === '') {
        $autoload = __DIR__ . '/../dompdf/vendor/autoload.php';
        if (!is_file($autoload)) {
            error_log('No se encontró Dompdf. Ejecuta Composer desde la carpeta dompdf.');
            http_response_code(500);
            exit('No está disponible el generador PDF. Verifica la instalación de Dompdf.');
        }

        require_once $autoload;
        $nombreReporte = [
            'ventas' => 'Ventas',
            'citas' => 'Citas',
            'usuarios' => 'Reporte de cliente',
            'esteticistas' => 'Esteticistas',
        ][$tipoReporte];
        $personaReporte = '';
        if ($tipoReporte === 'usuarios' && $datosReporte) {
            $personaReporte = $datosReporte[0]['nombreCliente'];
        } elseif ($tipoReporte === 'esteticistas' && $datosReporte) {
            $personaReporte = $datosReporte[0]['nombreEsteticista'];
        }
        ob_start();
        require 'vista/reportes_pdf.php';
        $htmlPdf = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($htmlPdf, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream(
            'reporte-' . $tipoReporte . '-' . ($cedulaClienteReporte ?: $cedulaEsteticistaReporte ?: 'general')
                . '-' . $fechaInicio . '-' . $fechaFin . '.pdf',
            ['Attachment' => false]
        );
        exit;
    }
}

require_once 'vista/modulos/reportes.php';
