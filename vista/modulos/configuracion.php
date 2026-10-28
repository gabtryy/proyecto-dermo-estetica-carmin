<?php require_once 'vista/layouts/header.php'; ?>

<div class="container py-2">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a href="index.php?pagina=home" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <div class="mb-4">
        <h1 class="h3 page-title mb-1">Configuración</h1>
        <p class="page-subtitle mb-0">Administra monedas, métodos de pago y tasas de cambio.</p>
    </div>

    <style>
        .config-toggle {
            min-height: 82px;
            padding: 1rem 1.25rem;
            color: #38174d;
            background: linear-gradient(115deg, #ffffff 0%, #f8f1ff 58%, #efe2fa 100%);
            border: 1px solid rgba(107, 45, 134, 0.2);
            border-radius: 1rem;
            box-shadow: 0 8px 22px rgba(56, 23, 77, 0.1);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .config-toggle:hover,
        .config-toggle:focus-visible {
            color: #38174d;
            background: linear-gradient(115deg, #ffffff 0%, #f5eaff 58%, #ead9f8 100%);
            border-color: rgba(107, 45, 134, 0.42);
            box-shadow: 0 12px 28px rgba(56, 23, 77, 0.16);
            transform: translateY(-2px);
        }

        .config-toggle:focus-visible {
            outline: 3px solid rgba(107, 45, 134, 0.3);
            outline-offset: 3px;
        }

        .config-toggle-label {
            color: #38174d;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .config-toggle-hint {
            color: #75568a;
            font-size: 0.85rem;
        }

        .config-toggle-chevron-wrap {
            display: grid;
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            place-items: center;
            color: #ffffff;
            background: linear-gradient(145deg, #8445a3, #54226f);
            border: 1px solid rgba(56, 23, 77, 0.12);
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(56, 23, 77, 0.18);
        }

        .config-toggle-action {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-left: 1rem;
            color: #54226f;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .config-toggle-close {
            display: none;
        }

        .config-toggle[aria-expanded="true"] .config-toggle-open {
            display: none;
        }

        .config-toggle[aria-expanded="true"] .config-toggle-close {
            display: inline;
        }

        .config-toggle-chevron {
            transition: transform 0.2s ease;
        }

        .config-toggle[aria-expanded="true"] .config-toggle-chevron {
            transform: rotate(180deg);
        }

        @media (prefers-reduced-motion: reduce) {
            .config-toggle,
            .config-toggle-chevron {
                transition: none;
            }
        }

        .config-list-card {
            overflow: hidden;
            border: 1px solid rgba(107, 45, 134, 0.12) !important;
            border-radius: 1rem;
            box-shadow: 0 10px 28px rgba(56, 23, 77, 0.08);
        }

        .config-form-card {
            overflow: hidden;
            border: 1px solid rgba(107, 45, 134, 0.14) !important;
            border-radius: 1rem;
            box-shadow: 0 10px 26px rgba(56, 23, 77, 0.09);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .config-form-card:hover {
            box-shadow: 0 14px 32px rgba(56, 23, 77, 0.14);
            transform: translateY(-2px);
        }

        .config-form-card .card-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(115deg, #ffffff, #f4eafb) !important;
            border-bottom: 1px solid rgba(107, 45, 134, 0.1);
        }

        .config-form-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
            color: #38174d;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 700;
        }

        .config-form-icon {
            display: grid;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            place-items: center;
            color: #ffffff;
            background: linear-gradient(145deg, #8445a3, #54226f);
            border-radius: 0.8rem;
            box-shadow: 0 4px 10px rgba(56, 23, 77, 0.16);
        }

        .config-form-card .card-body {
            padding: 1.35rem 1.25rem;
            background: #ffffff;
        }

        .config-form-card .form-label {
            color: #493653;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .config-form-card .form-control,
        .config-form-card .form-select {
            min-height: 44px;
            border-color: #e3d8eb;
            border-radius: 0.7rem;
        }

        .config-form-card .form-control:focus,
        .config-form-card .form-select:focus {
            border-color: #9b69b5;
            box-shadow: 0 0 0 0.22rem rgba(107, 45, 134, 0.13);
        }

        .config-form-card .btn-purple {
            padding: 0.6rem 1rem;
            border-radius: 0.65rem;
            box-shadow: 0 4px 10px rgba(56, 23, 77, 0.14);
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .config-form-card .btn-purple:hover {
            box-shadow: 0 7px 14px rgba(56, 23, 77, 0.2);
            transform: translateY(-1px);
        }

        .config-form-card .form-text {
            color: #755f80;
            line-height: 1.5;
        }

        .config-list-card .card-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(115deg, #ffffff, #f8f2fc) !important;
            border-bottom: 1px solid rgba(107, 45, 134, 0.1);
        }

        .config-list-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #38174d;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 700;
        }

        .config-list-icon {
            display: grid;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            place-items: center;
            color: #642c80;
            background: #efe3f7;
            border-radius: 0.75rem;
        }

        .config-table {
            --bs-table-bg: #ffffff;
            --bs-table-striped-bg: #fbf8fd;
            --bs-table-hover-bg: #f5eef9;
            margin-bottom: 0;
        }

        .config-table thead th {
            padding: 0.85rem 1.25rem;
            color: #68477a;
            background: #f8f4fb;
            border-bottom: 1px solid #e9dff0;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .config-table tbody td {
            padding: 0.9rem 1.25rem;
            color: #493653;
            border-color: #f0eaf3;
            vertical-align: middle;
        }

        .config-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .config-table tbody tr:hover {
            --bs-table-accent-bg: #f5eef9;
        }

        .config-table tbody td.text-center {
            padding: 1.5rem 1rem;
        }

        @media (max-width: 575.98px) {
            .config-list-card .card-header {
                padding: 0.85rem 1rem;
            }

            .config-form-card .card-header,
            .config-form-card .card-body {
                padding-right: 1rem;
                padding-left: 1rem;
            }

            .config-table thead th,
            .config-table tbody td {
                padding-right: 0.85rem;
                padding-left: 0.85rem;
            }
        }
    </style>
    <div class="mb-4">
        <button
            class="btn config-toggle w-100 d-flex align-items-center justify-content-between text-start"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#formulariosConfiguracion"
            aria-expanded="false"
            aria-controls="formulariosConfiguracion"
            id="toggleFormulariosConfiguracion"
        >
            <span class="d-flex align-items-center gap-3">
                <span class="config-toggle-chevron-wrap" aria-hidden="true">
                    <i class="fas fa-sliders-h"></i>
                </span>
                <span class="d-flex flex-column">
                    <span class="config-toggle-label">Configuración de monedas y pagos</span>
                    <span class="config-toggle-hint">Administra monedas, métodos de pago y tasas de cambio</span>
                </span>
            </span>
            <span class="config-toggle-action">
                <span>
                    <span class="config-toggle-open">Mostrar opciones</span>
                    <span class="config-toggle-close">Ocultar opciones</span>
                </span>
                <span class="config-toggle-chevron-wrap" aria-hidden="true">
                    <i class="fas fa-chevron-down config-toggle-chevron"></i>
                </span>
            </span>
        </button>
        <div class="collapse" id="formulariosConfiguracion">
            <div class="row g-4 pt-3">
        <div class="col-xl-4">
            <section class="card config-form-card h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="config-form-title">
                        <span class="config-form-icon"><i class="fas fa-coins" aria-hidden="true"></i></span>
                        Añadir moneda
                    </h2>
                </div>
                <div class="card-body">
                    <form id="formMoneda" autocomplete="off">
                        <input type="hidden" name="accion" value="agregarMoneda">
                        <div class="mb-3">
                            <label class="form-label" for="nombre_moneda">Nombre</label>
                            <input class="form-control" id="nombre_moneda" name="nombre_moneda" maxlength="50" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="simbolo">Símbolo</label>
                            <input class="form-control" id="simbolo" name="simbolo" maxlength="5" required>
                        </div>
                        <button type="submit" class="btn btn-purple">
                            <i class="fas fa-plus me-1"></i>Agregar moneda
                        </button>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="card config-form-card h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="config-form-title">
                        <span class="config-form-icon"><i class="fas fa-credit-card" aria-hidden="true"></i></span>
                        Añadir método de pago
                    </h2>
                </div>
                <div class="card-body">
                    <form id="formMetodo" autocomplete="off">
                        <input type="hidden" name="accion" value="agregarMetodo">
                        <div class="mb-3">
                            <label class="form-label" for="nom_MetodoPago">Nombre del método</label>
                            <input class="form-control" id="nom_MetodoPago" name="nom_MetodoPago" maxlength="50" required>
                        </div>
                        <button type="submit" class="btn btn-purple">
                            <i class="fas fa-plus me-1"></i>Agregar método
                        </button>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="card config-form-card h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="config-form-title">
                        <span class="config-form-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                        Registrar tasa
                    </h2>
                </div>
                <div class="card-body">
                    <form id="formTasa" autocomplete="off">
                        <input type="hidden" name="accion" value="guardarTasa">
                        <div class="mb-3">
                            <label class="form-label" for="monedaTasa">Moneda</label>
                            <select class="form-select" id="monedaTasa" name="id_moneda" required>
                                <option value="">Cargando monedas...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="fecha_tasa">Fecha de la tasa</label>
                            <input class="form-control" type="date" id="fecha_tasa" name="fecha_tasa" value="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>" max="<?= htmlspecialchars(date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="monto_tasa">Tasa por cada $1</label>
                            <input class="form-control" type="number" id="monto_tasa" name="monto_tasa" min="0.0001" max="999999.9999" step="0.0001" required>
                            <div class="form-text">Ejemplo: cuántos bolívares equivalen a un dólar. Cada registro conserva el historial y se usará la tasa más reciente de la fecha.</div>
                        </div>
                        <button type="submit" class="btn btn-purple">
                            <i class="fas fa-save me-1"></i>Guardar tasa
                        </button>
                    </form>
                </div>
            </section>
        </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <section class="card config-list-card h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="config-list-title mb-0">
                        <span class="config-list-icon"><i class="fas fa-coins" aria-hidden="true"></i></span>
                        Monedas disponibles
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle config-table">
                        <thead>
                            <tr><th>Moneda</th><th>Símbolo</th><th>Tasa de hoy por $1</th></tr>
                        </thead>
                        <tbody id="listaMonedas">
                            <tr><td colspan="3" class="text-center text-muted">Cargando monedas...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="card config-list-card h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="config-list-title mb-0">
                        <span class="config-list-icon"><i class="fas fa-credit-card" aria-hidden="true"></i></span>
                        Métodos de pago disponibles
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle config-table">
                        <thead><tr><th>Nombre</th></tr></thead>
                        <tbody id="listaMetodos">
                            <tr><td class="text-center text-muted">Cargando métodos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-12">
            <section class="card config-list-card">
                <div class="card-header bg-white py-3">
                    <h2 class="config-list-title mb-0">
                        <span class="config-list-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                        Historial de tasas
                    </h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle config-table">
                        <thead><tr><th>Fecha</th><th>Moneda</th><th>Tasa por $1</th></tr></thead>
                        <tbody id="listaTasas">
                            <tr><td colspan="3" class="text-center text-muted">Cargando tasas...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<?php require_once 'vista/layouts/footer.php'; ?>
