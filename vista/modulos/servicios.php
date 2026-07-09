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

    <h1 class="h3 mb-3">Servicios</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
            <span class="fw-semibold">Listado de servicios</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table table-striped table-hover table-bordered align-middle mb-0"
                    id="tablausuarios"
                    aria-describedby="tabla-clientes-desc"
                >
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
                            <th scope="col">Nombre Servicio</th>
                            <th scope="col">Precio</th>
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-bg-info">
                <h2 class="modal-title fs-5" id="modal1Label">Añadir servicio</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="formulario_servicio" autocomplete="off">
                    <input type="hidden" name="accion" id="accion" value="">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label" for="nombreServicio">Nombre Servicio</label>
                            <input class="form-control" type="text" id="nombreServicio" name="nombreServicio" autocomplete="off">
                            <span class="form-text text-danger" id="snombreServicio"></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="precio">Precio</label>
                            <input class="form-control" type="text" id="precio" name="precio" autocomplete="off">
                            <span class="form-text text-danger" id="sprecio"></span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" autocomplete="off"></textarea>
                            <span class="form-text text-danger" id="sdescripcion"></span>
                        </div>
                    </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary" type="submit" id="proceso">INCLUIR</button>
            </div>
                </form>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>