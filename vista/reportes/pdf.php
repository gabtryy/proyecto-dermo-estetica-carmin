<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Gastos y Presupuestos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        
        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item h3 {
            margin: 0;
            font-size: 18px;
            color: #2c3e50;
        }
        
        .summary-item p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            background-color: #3498db;
            color: white;
            padding: 10px;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .positive {
            color: #27ae60;
        }
        
        .negative {
            color: #e74c3c;
        }
        
        .category-badge {
            background-color: #3498db;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-data {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Gastos y Presupuestos</h1>
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
        <?php if (isset($_SESSION['username'])): ?>
        <p>Usuario: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        <?php endif; ?>
    </div>

    <!-- Resumen General -->
    <div class="summary">
        <div class="summary-item">
            <h3>$<?php echo number_format($total_gastos, 2); ?></h3>
            <p>Total Gastos</p>
        </div>
        <div class="summary-item">
            <h3>$<?php echo number_format($total_presupuestos, 2); ?></h3>
            <p>Total Presupuestos</p>
        </div>
        <div class="summary-item">
            <h3 class="<?php echo ($total_presupuestos - $total_gastos) >= 0 ? 'positive' : 'negative'; ?>">
                $<?php echo number_format($total_presupuestos - $total_gastos, 2); ?>
            </h3>
            <p>Diferencia</p>
        </div>
        <div class="summary-item">
            <h3><?php echo count($datos_reporte); ?></h3>
            <p>Total Registros</p>
        </div>
    </div>

    <!-- Gastos por Categoría -->
    <?php if (!empty($gastos_por_categoria)): ?>
    <div class="section">
        <h2>Gastos por Categoría</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Cantidad de Gastos</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos_por_categoria as $categoria): ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></td>
                    <td><?php echo $categoria['cantidad_gastos']; ?></td>
                    <td class="amount">$<?php echo number_format($categoria['total_gastos'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Gastos por Usuario (solo para administradores) -->
    <?php if (!empty($gastos_por_usuario) && ($_SESSION['id_rol'] == 2 || $_SESSION['id_rol'] == 3)): ?>
    <div class="section">
        <h2>Gastos por Usuario</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Cantidad de Gastos</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos_por_usuario as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo $usuario['cantidad_gastos']; ?></td>
                    <td class="amount">$<?php echo number_format($usuario['total_gastos'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Detalle de Gastos -->
    <div class="section">
        <h2>Detalle de Gastos</h2>
        <?php if (!empty($datos_reporte)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Presupuesto</th>
                    <?php if ($_SESSION['id_rol'] == 2 || $_SESSION['id_rol'] == 3): ?>
                    <th>Usuario</th>
                    <th>Email</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos_reporte as $gasto): ?>
                <tr>
                    <td><?php echo $gasto['id_gastos']; ?></td>
                    <td><?php echo htmlspecialchars($gasto['descripcion']); ?></td>
                    <td class="amount">$<?php echo number_format($gasto['monto_gastos'], 2); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($gasto['fecha_gastos'])); ?></td>
                    <td>
                        <span class="category-badge">
                            <?php echo htmlspecialchars($gasto['nombre_categoria']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($gasto['nombre_presupuesto']); ?></td>
                    <?php if ($_SESSION['id_rol'] == 2 || $_SESSION['id_rol'] == 3): ?>
                    <td><?php echo htmlspecialchars($gasto['username']); ?></td>
                    <td><?php echo htmlspecialchars($gasto['email']); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="no-data">
            No se encontraron gastos para mostrar.
        </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Reporte generado automáticamente por el Sistema de Gestión de Gastos - <?php echo date('Y'); ?></p>
    </div>
</body>
</html>
