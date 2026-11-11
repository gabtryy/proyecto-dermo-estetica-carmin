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
$errorReporte = '';
$datosReporte = [];

if (!in_array($tipoReporte, ['ventas', 'citas'], true)) {
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
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar']) && $errorReporte === '') {
    try {
        $modeloReportes = new Reportes();
        $datosReporte = $tipoReporte === 'ventas'
            ? $modeloReportes->ventas($fechaInicio, $fechaFin)
            : $modeloReportes->citas($fechaInicio, $fechaFin);
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
        $nombreReporte = $tipoReporte === 'ventas' ? 'Ventas' : 'Citas';
        ob_start();
        require 'vista/reportes_pdf.php';
        $htmlPdf = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($htmlPdf, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream(
            'reporte-' . $tipoReporte . '-' . $fechaInicio . '-' . $fechaFin . '.pdf',
            ['Attachment' => false]
        );
        exit;
    }
}

require_once 'vista/modulos/reportes.php';
