<?php require_once("vista/layouts/header.php"); ?>

<style>
    .diagnostico-card {
        max-width: 1050px;
        margin: 0 auto;
    }

    .rostro-panel {
        position: relative;
        width: min(100%, 560px);
        aspect-ratio: 1 / 1;
        margin: 0 auto;
        border-radius: 0.75rem;
        background-image: url("img/rostro-diagnostico.png");
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        border: none;
        box-shadow: none;
    }

    .rostro-label {
        position: absolute;
        z-index: 2;
        transform: translate(-50%, -50%);
        width: min(28%, 150px);
    }

    .rostro-label label {
        display: block;
        margin-bottom: 0.3rem;
        color: #38174d;
        font-size: 0.8rem;
        font-weight: 700;
        text-align: center;
    }

    .rostro-label input {
        width: 100%;
        font-size: 0.8rem;
    }

    .rostro-frente { top: 30%; left: 50%; }
    .rostro-mejilla-izquierda { top: 65%; left: 20%; }
    .rostro-mejilla-derecha { top: 65%; left: 80%; }
    .rostro-naris { top: 58%; left: 50%; }
    .rostro-menton { top: 90%; left: 50%; }

    @media (max-width: 575.98px) {
        .rostro-panel { width: 100%; }
        .rostro-label { width: 34%; }
        .rostro-label label { font-size: 0.7rem; }
        .rostro-label input { padding: 0.35rem 0.5rem; font-size: 0.7rem; }
    }
</style>

<div class="container-fluid py-2 px-0 px-lg-3">
    <div class="page-header p-4 mb-4">
        <h1 class="h3 page-title mb-1">Diagnóstico facial</h1>
        <p class="page-subtitle mb-0">Selecciona el cliente, el tipo de piel y la condición observada en cada zona.</p>
    </div>

    <div class="card diagnostico-card border-0 shadow-sm">
        <div class="card-header bg-purple text-white py-3">
            <span class="fw-semibold"><i class="fas fa-clipboard-check me-2"></i>Evaluación por zonas</span>
        </div>
        <div class="card-body p-3 p-md-5">
            <form id="formulario_diagnostico" method="post" autocomplete="off">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="cedulaCliente">Cliente</label>
                        <select class="form-select" id="cedulaCliente" name="cedulaCliente" required>
                            <option value="">Seleccione un cliente</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="idPiel">Tipo de piel</label>
                        <select class="form-select" id="idPiel" name="idPiel" required>
                            <option value="">Seleccione el tipo de piel</option>
                        </select>
                    </div>
                </div>

                <div class="rostro-panel" aria-label="Mapa del rostro para seleccionar el diagnóstico por zona">
                    <div class="rostro-label rostro-frente">
                        <label for="frente">Frente</label>
                        <input type="text" class="form-control" id="frente" name="frente">
                    </div>

                    <div class="rostro-label rostro-mejilla-izquierda">
                        <label for="mejilla_izquierda">Mejilla izquierda</label>
                        <input type="text" class="form-control" id="mejilla_izquierda" name="mejilla_izquierda">
                    </div>

                    <div class="rostro-label rostro-mejilla-derecha">
                        <label for="mejilla_derecha">Mejilla derecha</label>
                        <input type="text" class="form-control" id="mejilla_derecha" name="mejilla_derecha">
                    </div>

                    <div class="rostro-label rostro-naris">
                        <label for="naris">Naris</label>
                        <input type="text" class="form-control" id="naris" name="naris">
                    </div>

                    <div class="rostro-label rostro-menton">
                        <label for="menton">Mentón</label>
                        <input type="text" class="form-control" id="menton" name="menton">
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="reset" class="btn btn-outline-purple">Limpiar</button>
                    <button type="submit" class="btn btn-purple"><i class="fas fa-save me-1"></i>Guardar diagnóstico</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-purple text-white border-bottom py-3">
            <span class="fw-semibold">Listado de diagnósticos</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0" id="tabla-diagnosticos">
                    <thead class="table-purple text-white">
                        <tr>
                            <th scope="col">Cliente</th>
                            <th scope="col">Tipo de piel</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Frente</th>
                            <th scope="col">Nariz</th>
                            <th scope="col">Mejilla izq.</th>
                            <th scope="col">Mejilla der.</th>
                            <th scope="col">Mentón</th>
                        </tr>
                    </thead>
                    <tbody id="resultado-diagnosticos"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>