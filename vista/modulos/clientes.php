<?php require_once("vista/layouts/header.php"); ?>

<div class="container-fluid py-2 px-0 px-lg-3">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-purple shadow-sm" id="incluir">
            <i class="fas fa-user-plus me-1"></i>INCLUIR
        </button>
        <a href="index.php" class="btn btn-outline-purple">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3">Clientes</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-purple text-white border-bottom py-3">
            <span class="fw-semibold">Listado de clientes</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table table-striped table-hover table-bordered align-middle mb-0"
                    id="tablausuarios"
                    aria-describedby="tabla-clientes-desc"
                >
                    <thead class="table-purple text-white">
                        <tr>
                            <th scope="col">Cédula</th>
                            <th scope="col">Nombre</th>
                            <th scope="col" class="text-nowrap">Fecha nac.</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
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
            <div class="modal-header bg-purple">
                <h2 class="modal-title fs-5" id="modal1Label">Añadir cliente</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="formulario_cliente" autocomplete="off">
                    <input type="hidden" name="accion" id="accion" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="cedula">Cédula</label>
                            <input class="form-control" type="text" id="cedula" name="cedula" autocomplete="off">
                            <span class="form-text text-danger" id="scedula"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="nombres">Nombre completo</label>
                            <input class="form-control" type="text" id="nombres" name="nombres" autocomplete="off">
                            <span class="form-text text-danger" id="snombres"></span>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="fechadenacimiento">Fecha de nacimiento</label>
                            <input class="form-control" type="date" id="fechadenacimiento" name="fechadenacimiento">
                            <span class="form-text text-danger" id="sfechadenacimiento"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="estado">Estado</label>
                            <input class="form-control" type="text" id="estado" name="estado" autocomplete="off">
                            <span class="form-text text-danger" id="sestado"></span>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="municipio">Municipio</label>
                            <input class="form-control" type="text" id="municipio" name="municipio" autocomplete="off">
                            <span class="form-text text-danger" id="smunicipio"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="parroquia">Parroquia</label>
                            <input class="form-control" type="text" id="parroquia" name="parroquia" autocomplete="off">
                            <span class="form-text text-danger" id="sparroquia"></span>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input class="form-control" type="tel" id="telefono" name="telefono" autocomplete="off">
                            <span class="form-text text-danger" id="stelefono"></span>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-purple" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-purple" type="submit" id="proceso">INCLUIR</button>
            </div>
                </form>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>