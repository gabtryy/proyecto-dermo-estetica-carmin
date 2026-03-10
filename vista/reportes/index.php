<?php
$titulo = "Reportes de Gastos";
include 'layouts/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Reportes de Gastos y Presupuestos
                    </h3>
                    <div class="btn-group">
                        <a href="?controlador=reportes&metodo=pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </a>
                        <a href="?controlador=reportes&metodo=excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Estadísticas Generales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Gastos</span>
                                    <span class="info-box-number">$<?php echo number_format($total_gastos, 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Presupuestos</span>
                                    <span class="info-box-number">$<?php echo number_format($total_presupuestos, 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-chart-pie"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Diferencia</span>
                                    <span class="info-box-number">$<?php echo number_format($total_presupuestos - $total_gastos, 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Registros</span>
                                    <span class="info-box-number"><?php echo count($datos_reporte); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos y Resúmenes -->
                    <div class="row mb-4">
                        <?php if (!empty($gastos_por_categoria)): ?>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Gastos por Categoría</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Categoría</th>
                                                    <th>Cantidad</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($gastos_por_categoria as $categoria): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></td>
                                                    <td><?php echo $categoria['cantidad_gastos']; ?></td>
                                                    <td>$<?php echo number_format($categoria['total_gastos'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($gastos_por_usuario) && ($_SESSION['id_rol'] == 2 || $_SESSION['id_rol'] == 3)): ?>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Gastos por Usuario</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Usuario</th>
                                                    <th>Cantidad</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($gastos_por_usuario as $usuario): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                                                    <td><?php echo $usuario['cantidad_gastos']; ?></td>
                                                    <td>$<?php echo number_format($usuario['total_gastos'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tabla Principal de Gastos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Detalle de Gastos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaGastos">
                                    <thead class="thead-dark">
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
                                        <?php if (!empty($datos_reporte)): ?>
                                            <?php foreach ($datos_reporte as $gasto): ?>
                                            <tr>
                                                <td><?php echo $gasto['id_gastos']; ?></td>
                                                <td><?php echo htmlspecialchars($gasto['descripcion']); ?></td>
                                                <td class="text-success font-weight-bold">$<?php echo number_format($gasto['monto_gastos'], 2); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($gasto['fecha_gastos'])); ?></td>
                                                <td>
                                                    <span class="badge badge-info">
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
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="<?php echo ($_SESSION['id_rol'] == 2 || $_SESSION['id_rol'] == 3) ? '8' : '6'; ?>" class="text-center">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle"></i> No se encontraron gastos para mostrar.
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Script -->
<script>
$(document).ready(function() {
    $('#tablaGastos').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[ 3, "desc" ]], // Ordenar por fecha descendente
        "pageLength": 25,
        "responsive": true
    });
});
</script>

<?php include 'layouts/footer.php'; ?>
