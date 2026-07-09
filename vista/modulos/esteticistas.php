<?php require_once("vista/layouts/header.php"); ?>

<div class="container py-4">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-primary shadow-sm" id="incluir">
            <i class="fas fa-user-plus me-1"></i>INCLUIR
        </button>
        <a href="index.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3">Esteticistas</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
            <span class="fw-semibold">Listado de esteticistas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table table-striped table-hover table-bordered align-middle mb-0"
                    id="tablausuarios"
                    aria-describedby="tabla-esteticistas-desc"
                >
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
                            <th scope="col">Cédula</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Especialidad</th>
                        </tr>
                    </thead>
                    <tbody id="resultadoconsulta">
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal formulario esteticista (Bootstrap 5) -->
<div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-bg-info">
                <h2 class="modal-title fs-5" id="modal1Label">Añadir esteticista</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="formulario_esteticista" autocomplete="off">
                    <input type="hidden" name="accion" id="accion" value="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="cedula">Cédula</label>
                            <input class="form-control" type="text" id="cedula" name="cedula" autocomplete="off">
                            <span class="form-text text-danger" id="scedula"></span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="nombres">Nombre completo</label>
                            <input class="form-control" type="text" id="nombres" name="nombres" autocomplete="off">
                            <span class="form-text text-danger" id="snombres"></span>
                        </div>
                    </div>
                    <div class="row g-3 mt-0">
                        <div class="col-md-4">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input class="form-control" type="text" id="telefono" name="telefono" autocomplete="off">
                            <span class="form-text text-danger" id="stelefono"></span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="correo">Correo</label>
                            <input class="form-control" type="email" id="correo" name="correo" autocomplete="off">
                            <span class="form-text text-danger" id="scorreo"></span>
                        </div>
                    </div>
                    <div class="row g-3 mt-0">
                        <div class="col-md-12">
                            <label class="form-label" for="especialidad">Especialidad</label>
                            <input class="form-control" type="text" id="especialidad" name="especialidad" autocomplete="off">
                            <span class="form-text text-danger" id="sespecialidad"></span>
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
