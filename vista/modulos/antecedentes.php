<?php require_once("vista/layouts/header.php"); ?>

<div class="container py-2">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-primary shadow-sm" id="incluir">
            <i class="fas fa-notes-medical me-1"></i>INCLUIR
        </button>
        <a href="index.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3">Antecedentes</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
            <span class="fw-semibold">Listado de antecedentes por cliente</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table table-striped table-hover table-bordered align-middle mb-0"
                    id="tablausuarios"
                    aria-describedby="tabla-antecedentes-desc"
                >
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Tipo de antecedente</th>
                            <th scope="col">Descripción</th>
                        </tr>
                    </thead>
                    <tbody id="resultadoconsulta">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-bg-info">
                <h2 class="modal-title fs-5" id="modal1Label">Añadir antecedentes</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="formulario_antecedente" autocomplete="off">
                    <input type="hidden" name="accion" id="accion" value="">
                    <input type="hidden" name="id_antecedente" id="id_antecedente" value="">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="cedulaCliente">Cliente</label>
                            <select class="form-select" id="cedulaCliente" name="cedulaCliente">
                                <option value="">Seleccione un cliente</option>
                            </select>
                            <span class="form-text text-danger" id="scedulaCliente"></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Tipos de antecedentes</label>
                            <div id="listaTiposAntecedentes" class="border rounded p-3 bg-light"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary" type="submit" form="formulario_antecedente" id="proceso">INCLUIR</button>
            </div>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>
