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

    <h1 class="h3 mb-3">Usuarios</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
            <span class="fw-semibold">Listado de usuarios</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0" id="tablausuarios">
                    <thead class="table-purple text-white">
                        <tr>
                            <th scope="col">Cédula</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Rol</th>
                        </tr>
                    </thead>
                    <tbody id="resultadoconsulta"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-purple">
                <h2 class="modal-title fs-5" id="modal1Label">Añadir usuario</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="post" id="formulario_usuario" autocomplete="off" novalidate
                  data-confirmacion-registro
                  data-mensaje-confirmacion="La cédula del usuario no se puede cambiar después de crearlo. Verifica que sea correcta.">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="cedula">Cédula</label>
                        <input class="form-control" type="text" id="cedula" name="cedula" inputmode="numeric" maxlength="10">
                        <span class="form-text text-danger" id="scedula"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="usuario">Nombre de usuario</label>
                        <input class="form-control" type="text" id="usuario" name="usuario" maxlength="32" autocomplete="username">
                        <span class="form-text text-danger" id="susuario"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="clave">Clave</label>
                        <input class="form-control" type="password" id="clave" name="clave" maxlength="255" autocomplete="new-password">
                        <span class="form-text text-danger" id="sclave"></span>
                    </div>
                    <div>
                        <label class="form-label" for="rol">Rol</label>
                        <select class="form-select" id="rol" name="rol">
                            <option value="">Seleccione un rol</option>
                        </select>
                        <span class="form-text text-danger" id="srol"></span>
                    </div>
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
