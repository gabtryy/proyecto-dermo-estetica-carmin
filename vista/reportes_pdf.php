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
        <thead>
            <tr>
                <?php foreach ($columnasReporte as $etiqueta): ?>
                    <th><?= $escapar($etiqueta) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datosReporte as $registro): ?>
                <tr>
                    <?php foreach ($columnasReporte as $columna => $etiqueta): ?>
                        <?php
                        $valor = $registro[$columna] ?? '—';
                        if (in_array($columna, ['totalVenta', 'monto_pagado'], true)) {
                            $valor = 'Bs ' . number_format((float) $valor, 2, ',', '.');
                        } elseif ($columna === 'hora_cita') {
                            $valor = substr((string) $valor, 0, 5);
                        } elseif ($columna === 'estado_cita') {
                            $valor = ucfirst((string) $valor);
                        }
                        ?>
                        <td><?= $escapar($valor) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if (!$datosReporte): ?>
            <tbody>
                <tr>
                    <td colspan="<?= max(1, count($columnasReporte)) ?>">No hay registros en el período seleccionado.</td>
                </tr>
            </tbody>
        <?php endif; ?>
    </table>
</body>
</html>
