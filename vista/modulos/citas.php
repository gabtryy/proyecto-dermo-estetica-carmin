<?php require_once("vista/layouts/header.php"); ?>

<div class="container-fluid py-2 px-0 px-lg-3">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-purple shadow-sm" id="btn-guardar-cita">
            <i class="fas fa-calendar-plus me-1"></i>GUARDAR CITA
        </button>
        <a href="index.php" class="btn btn-outline-purple">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3">Citas</h1>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-purple text-white border-bottom py-3">
            <span class="fw-semibold">Registrar nueva cita</span>
        </div>
        <div class="card-body">
            <form id="formulario_cita" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="cedulaCliente">Cliente</label>
                        <div class="input-group">
                            <select class="form-select" id="cedulaCliente" name="cedulaCliente" required>
                                <option value="">Seleccione un cliente</option>
                            </select>
                            <a class="btn btn-outline-purple" href="index.php?pagina=clientes" target="_blank" rel="noopener">
                                <i class="fas fa-user-plus"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="cedulaEsteticista">Esteticista</label>
                        <div class="input-group">
                            <select class="form-select" id="cedulaEsteticista" name="cedulaEsteticista" required>
                                <option value="">Seleccione un esteticista</option>
                            </select>
                            <a class="btn btn-outline-purple" href="index.php?pagina=esteticistas" target="_blank" rel="noopener">
                                <i class="fas fa-user-tie"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label" for="fecha_cita">Fecha</label>
                        <input class="form-control" type="date" id="fecha_cita" name="fecha_cita" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="hora">Hora</label>
                        <input class="form-control" type="time" id="hora" name="hora" required>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold">Servicios</label>
                    <div id="lista-servicios" class="row g-2"></div>
                    <div class="mt-3 small text-muted">Seleccione uno o varios servicios para la cita.</div>
                    <div class="mt-3 d-flex align-items-center gap-2">
                        <span class="fw-semibold">Total estimado:</span>
                        <span id="total-cita" class="badge bg-success-subtle text-success fs-6 px-3 py-2">$0.00</span>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-purple px-4">
                        <i class="fas fa-save me-1"></i>Guardar cita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-purple text-white border-bottom py-3">
            <span class="fw-semibold">Listado de citas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0" id="tabla-citas">
                    <thead class="table-purple text-white">
                        <tr>
                            <th scope="col">Cliente</th>
                            <th scope="col">Esteticista</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Hora</th>
                            <th scope="col">Servicios</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="resultadoconsulta">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-editar-cita" tabindex="-1" aria-labelledby="modal-editar-cita-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-purple text-white">
                <h2 class="modal-title fs-5" id="modal-editar-cita-label">Modificar cita</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formulario_editar_cita" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="idCita" id="editar-id-cita">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="editar-cliente">Cliente</label><select class="form-select" name="cedulaCliente" id="editar-cliente" required></select></div>
                        <div class="col-md-6"><label class="form-label" for="editar-esteticista">Esteticista</label><select class="form-select" name="cedulaEsteticista" id="editar-esteticista" required></select></div>
                        <div class="col-md-6"><label class="form-label" for="editar-fecha">Fecha</label><input class="form-control" type="date" name="fecha_cita" id="editar-fecha" required></div>
                        <div class="col-md-6"><label class="form-label" for="editar-hora">Hora</label><input class="form-control" type="time" name="hora" id="editar-hora" required></div>
                    </div>
                    <div class="mt-4"><label class="form-label fw-semibold">Servicios</label><div id="editar-lista-servicios" class="row g-2"></div></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-purple" data-bs-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-purple"><i class="fas fa-save me-1"></i>Guardar cambios</button></div>
            </form>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>
