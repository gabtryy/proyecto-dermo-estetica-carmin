<?php require_once("vista/layouts/header.php"); ?>

<div class="container py-2">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-primary shadow-sm" id="incluir">
            <i class="fas fa-user-plus me-1"></i>INCLUIR
        </button>
        <a href="index.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3">Proveedores</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
            <span class="fw-semibold">Listado de proveedores</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table table-striped table-hover table-bordered align-middle mb-0"
                    id="tablausuarios"
                    aria-describedby="tabla-proveedores-desc"
                >
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
                            <th scope="col">RIF</th>
                            <th scope="col">Nombre del proveedor</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Municipio</th>
                            <th scope="col">Parroquia</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Correo</th>
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-bg-info">
                <h2 class="modal-title fs-5" id="modal1Label">Añadir proveedor</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="formulario_proveedor" autocomplete="off">
                    <input type="hidden" name="accion" id="accion" value="">
                    <input type="hidden" name="idProveedor" id="idProveedor" value="">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="rif">RIF</label>
                            <input class="form-control" type="text" id="rif" name="rif" autocomplete="off">
                            <span class="form-text text-danger" id="srif"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nombreProveedor">Nombre del proveedor</label>
                            <input class="form-control" type="text" id="nombreProveedor" name="nombreProveedor" autocomplete="off">
                            <span class="form-text text-danger" id="snombreProveedor"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label" for="estadoDirProveedor">Estado</label>
                            <input class="form-control" type="text" id="estadoDirProveedor" name="estadoDirProveedor" autocomplete="off">
                            <span class="form-text text-danger" id="sestadoDirProveedor"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="municipioDirProveedor">Municipio</label>
                            <input class="form-control" type="text" id="municipioDirProveedor" name="municipioDirProveedor" autocomplete="off">
                            <span class="form-text text-danger" id="smunicipioDirProveedor"></span>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="parroquiaDirProveedor">Parroquia</label>
                            <input class="form-control" type="text" id="parroquiaDirProveedor" name="parroquiaDirProveedor" autocomplete="off">
                            <span class="form-text text-danger" id="sparroquiaDirProveedor"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input class="form-control" type="text" id="telefono" name="telefono" autocomplete="off">
                            <span class="form-text text-danger" id="stelefono"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="correo">Correo</label>
                            <input class="form-control" type="email" id="correo" name="correo" autocomplete="off">
                            <span class="form-text text-danger" id="scorreo"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary" type="submit" form="formulario_proveedor" id="proceso">INCLUIR</button>
            </div>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>
