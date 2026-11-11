<?php
$escapar = static function ($valor): string {
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        p { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    </style>
</head>
<body>
    <h2>Reporte de <?= $escapar($nombreReporte) ?></h2>
    <p>Desde <?= $escapar($fechaInicio) ?> hasta <?= $escapar($fechaFin) ?></p>
    <table>
        <?php if ($tipoReporte === 'ventas'): ?>
            <thead>
                <tr>
                    <th>Venta</th><th>Fecha</th><th>Cliente</th>
                    <th>Productos</th><th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosReporte as $venta): ?>
                    <tr>
                        <td><?= (int) $venta['idVenta'] ?></td>
                        <td><?= $escapar($venta['fechaCompra']) ?></td>
                        <td><?= $escapar($venta['nombreCliente']) ?></td>
                        <td><?= $escapar($venta['productos']) ?></td>
                        <td>$<?= number_format((float) $venta['totalVenta'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        <?php else: ?>
            <thead>
                <tr>
                    <th>Fecha</th><th>Hora</th><th>Cliente</th>
                    <th>Esteticista</th><th>Servicio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosReporte as $cita): ?>
                    <tr>
                        <td><?= $escapar($cita['fecha_cita']) ?></td>
                        <td><?= $escapar(substr($cita['hora_cita'], 0, 5)) ?></td>
                        <td><?= $escapar($cita['nombreCliente'] ?? '—') ?></td>
                        <td><?= $escapar($cita['nombreEsteticista'] ?? '—') ?></td>
                        <td><?= $escapar($cita['servicios'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        <?php endif; ?>
        <?php if (!$datosReporte): ?>
            <tbody>
                <tr>
                    <td colspan="5">No hay registros en el período seleccionado.</td>
                </tr>
            </tbody>
        <?php endif; ?>
    </table>
</body>
</html>
