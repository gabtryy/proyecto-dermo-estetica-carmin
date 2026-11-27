<?php require_once 'vista/layouts/header.php'; ?>

<div class="container py-2">
    <div class="mb-3">
        <a href="index.php?pagina=home" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <section class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h1 class="h5 mb-0"><i class="fas fa-file-pdf text-purple-strong me-2"></i>Reportes</h1>
        </div>
        <div class="card-body">
            <?php if ($errorReporte !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorReporte, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <form method="post" action="index.php?pagina=reportes" class="row g-3">
                <div class="col-12">
                    <label class="form-label" for="tipoReporte">Reporte</label>
                    <select class="form-select" id="tipoReporte" name="tipo">
                        <option value="ventas" <?= $tipoReporte === 'ventas' ? 'selected' : '' ?>>Ventas</option>
                        <option value="citas" <?= $tipoReporte === 'citas' ? 'selected' : '' ?>>Citas</option>
                        <option value="usuarios" <?= $tipoReporte === 'usuarios' ? 'selected' : '' ?>>Cliente: compras y citas</option>
                        <option value="esteticistas" <?= $tipoReporte === 'esteticistas' ? 'selected' : '' ?>>Esteticista: citas atendidas</option>
                    </select>
                </div>
                <div class="col-12" id="seccionCliente" <?= $tipoReporte === 'usuarios' ? '' : 'hidden' ?>>
                    <div class="border rounded p-3">
                        <h2 class="h6 mb-2">Reporte por cliente</h2>
                        <label class="form-label" for="cedulaClienteReporte">Selecciona un cliente</label>
                        <select class="form-select" id="cedulaClienteReporte" name="cedulaCliente">
                            <option value="">Selecciona un cliente</option>
                            <?php foreach ($clientesReporte as $cliente): ?>
                                <option value="<?= htmlspecialchars((string) $cliente['cedulaCliente'], ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $cedulaClienteReporte === (string) $cliente['cedulaCliente'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cliente['nombreCliente'] . ' (' . $cliente['cedulaCliente'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12" id="seccionEsteticista" <?= $tipoReporte === 'esteticistas' ? '' : 'hidden' ?>>
                    <div class="border rounded p-3">
                        <h2 class="h6 mb-2">Reporte por esteticista</h2>
                        <label class="form-label" for="cedulaEsteticistaReporte">Selecciona un esteticista</label>
                        <select class="form-select" id="cedulaEsteticistaReporte" name="cedulaEsteticista">
                            <option value="">Selecciona un esteticista</option>
                            <?php foreach ($esteticistasReporte as $esteticista): ?>
                                <option value="<?= htmlspecialchars((string) $esteticista['cedulaEsteticista'], ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $cedulaEsteticistaReporte === (string) $esteticista['cedulaEsteticista'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($esteticista['nombreEsteticista'] . ' (' . $esteticista['cedulaEsteticista'] . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="fechaInicio">Desde</label>
                    <input class="form-control" type="date" id="fechaInicio" name="fechaInicio"
                           value="<?= htmlspecialchars($fechaInicio, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="fechaFin">Hasta</label>
                    <input class="form-control" type="date" id="fechaFin" name="fechaFin"
                           value="<?= htmlspecialchars($fechaFin, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-purple" type="submit" name="generar" value="1">
                        <i class="fas fa-file-pdf me-1"></i>Generar reporte
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tipoReporte = document.getElementById('tipoReporte');
        var seccionCliente = document.getElementById('seccionCliente');
        var seccionEsteticista = document.getElementById('seccionEsteticista');

        function actualizarSeccionesReporte() {
            seccionCliente.hidden = tipoReporte.value !== 'usuarios';
            seccionEsteticista.hidden = tipoReporte.value !== 'esteticistas';
        }

        tipoReporte.addEventListener('change', actualizarSeccionesReporte);
        actualizarSeccionesReporte();
    });
</script>

<?php require_once 'vista/layouts/footer.php'; ?>
