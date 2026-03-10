<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Gastos</title>

</head>
<body>
    <div class="header">
        <h1>Reporte de Gastos</h1>
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
        <?php if (isset($_SESSION['username'])): ?>
        <p>Usuario: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <?php endif; ?>
    </div>

    <!-- Presupuestos Disponibles -->
    <div class="section">
        <h2>Presupuestos Disponibles</h2>
        <?php if (!empty($presupuestos)): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Monto</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($presupuestos as $pres): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pres['nombre']); ?></td>
                    <td class="amount">$<?php echo number_format($pres['monto_presupuesto'], 2); ?></td>
                    <td><?php echo $pres['fecha_inicio']; ?></td>
                    <td><?php echo $pres['fecha_final']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">
            No hay presupuestos disponibles.
        </div>
        <?php endif; ?>
    </div>

    <!-- Gastos Recientes -->
    <div class="section">
        <h2>Gastos Recientes</h2>
        <?php if (!empty($gastos)): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Presupuesto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $gasto): ?>
                <tr>
                    <td><?php echo $gasto['fecha_gastos']; ?></td>
                    <td><?php echo htmlspecialchars($gasto['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($gasto['nombre_categoria']); ?></td>
                    <td class="amount">$<?php echo number_format($gasto['monto_gastos'], 2); ?></td>
                    <td><?php echo htmlspecialchars($gasto['nombre_presupuesto']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">
            No hay gastos registrados.
        </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión de Gastos - <?php echo date('Y'); ?></p>
    </div>
</body>
</html>
