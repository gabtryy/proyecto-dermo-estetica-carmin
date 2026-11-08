<?php require_once("vista/layouts/header.php"); ?>

<div class="container-fluid py-2 px-0 px-lg-3">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-purple shadow-sm" id="incluir">
            <i class="fas fa-user-plus me-1"></i>REGISTRAR
        </button>
        <a href="index.php?pagina=home" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3">Clientes</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
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
            <form method="post" id="formulario_cliente" autocomplete="off" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="accion" id="accion" value="">

                    <section class="mb-4" aria-labelledby="datos-personales-titulo">
                        <h3 class="fs-6 fw-semibold text-purple-strong border-bottom pb-2 mb-3" id="datos-personales-titulo">Datos personales</h3>
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
                            <div class="col-md-6">
                                <label class="form-label" for="fechadenacimiento">Fecha de nacimiento</label>
                                <input class="form-control" type="date" id="fechadenacimiento" name="fechadenacimiento">
                                <span class="form-text text-danger" id="sfechadenacimiento"></span>
                            </div>
                        </div>
                    </section>

                    <section class="mb-4" aria-labelledby="direccion-titulo">
                        <h3 class="fs-6 fw-semibold text-purple-strong border-bottom pb-2 mb-3" id="direccion-titulo">Dirección</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="estado-visible">Estado</label>
                                <input type="hidden" id="estado" name="estado" value="Lara">
                                <input class="form-control" type="text" id="estado-visible" value="Lara" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="capital">Capital</label>
                                <select class="form-select" id="capital">
                                    <option value="">Seleccione una capital</option>
                                </select>
                                <span class="form-text text-danger" id="smunicipio"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="municipio">Municipio</label>
                                <input class="form-control" type="text" id="municipio" name="municipio" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="parroquia">Parroquia</label>
                                <select class="form-select" id="parroquia" name="parroquia" disabled>
                                    <option value="">Seleccione una parroquia</option>
                                </select>
                                <span class="form-text text-danger" id="sparroquia"></span>
                            </div>
                        </div>
                    </section>

                    <section aria-labelledby="contacto-titulo">
                        <h3 class="fs-6 fw-semibold text-purple-strong border-bottom pb-2 mb-3" id="contacto-titulo">Contacto</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="telefono">Teléfono</label>
                                <input class="form-control" type="text" id="telefono" name="telefono" autocomplete="off">
                                <span class="form-text text-danger" id="stelefono"></span>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-purple" data-bs-dismiss="modal">Cerrar</button>
                    <button class="btn btn-purple" type="submit" id="proceso">REGISTRAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>