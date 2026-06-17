<?php require_once("vista/layouts/header.php"); ?>
<style>
    body.app-shell {
        background: linear-gradient(180deg, #b3e6ff 60%, #fff 100%) !important;
        color: #222;
    }
    .celeste-card {
        background: #e6f7ff;
        border: 1px solid #b3e6ff;
        border-radius: 1rem;
        box-shadow: 0 2px 12px 0 rgba(0, 180, 255, 0.08);
    }
    .celeste-header {
        background: #b3e6ff !important;
        color: #0077b6 !important;
        border-bottom: 2px solid #90caf9;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .celeste-table th {
        background: #e6f7ff !important;
        color: #0077b6 !important;
        border-bottom: 2px solid #b3e6ff !important;
    }
    .celeste-table td {
        background: #fff !important;
        color: #222;
    }
    .btn-celeste {
        background: #00b4d8;
        color: #fff;
        border: none;
        font-weight: 500;
        transition: background 0.2s;
    }
    .btn-celeste:hover, .btn-celeste:focus {
        background: #0096c7;
        color: #fff;
    }
    .btn-outline-celeste {
        border: 1.5px solid #00b4d8;
        color: #00b4d8;
        background: #fff;
        font-weight: 500;
        transition: background 0.2s, color 0.2s;
    }
    .btn-outline-celeste:hover, .btn-outline-celeste:focus {
        background: #00b4d8;
        color: #fff;
    }
    .modal-content {
        border-radius: 1.2rem;
        border: 1.5px solid #b3e6ff;
        box-shadow: 0 4px 24px 0 rgba(0, 180, 255, 0.10);
    }
    .modal-header.text-bg-info {
        background: #00b4d8 !important;
        color: #fff !important;
        border-top-left-radius: 1.2rem;
        border-top-right-radius: 1.2rem;
    }
    .form-label {
        color: #0077b6;
        font-weight: 500;
    }
    .form-control:focus {
        border-color: #00b4d8;
        box-shadow: 0 0 0 0.15rem #b3e6ff;
    }
    .modal-footer.bg-light {
        background: #e6f7ff !important;
        border-bottom-left-radius: 1.2rem;
        border-bottom-right-radius: 1.2rem;
    }
</style>

<div class="container py-2">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-celeste shadow-sm" id="incluir">
            <i class="fas fa-user-plus me-1"></i>INCLUIR
        </button>
        <a href="index.php" class="btn btn-outline-celeste">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <h1 class="h3 mb-3" style="color:#00b4d8;font-weight:700;letter-spacing:0.01em;">Productos</h1>

    <div class="celeste-card card shadow-sm border-0">
        <div class="celeste-header card-header border-bottom py-3">
            <span class="fw-semibold">Listado de productos</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table celeste-table table-striped table-hover table-bordered align-middle mb-0"
                    id="tablausuarios"
                    aria-describedby="tabla-productos-desc"
                >
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
                            <th scope="col">Nombre del producto</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Precio del producto</th>
                            <th scope="col">Proveedor</th>
                            <th scope="col">Cantidad actual</th>
                            <th scope="col">Tipo de producto</th>
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
                <h2 class="modal-title fs-5" id="modal1Label">Añadir producto</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="formulario_producto" autocomplete="off">
                    <input type="hidden" name="accion" id="accion" value="">
                    <input type="hidden" name="idProducto" id="idProducto" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="nombreProducto">Nombre del producto</label>
                            <input class="form-control" type="text" id="nombreProducto" name="nombreProducto" autocomplete="off">
                            <span class="form-text text-danger" id="snombreProducto"></span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="marca">Marca</label>
                            <input class="form-control" type="text" id="marca" name="marca" autocomplete="off">
                            <span class="form-text text-danger" id="smarca"></span>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label" for="idProveedor">Proveedor</label>
                            <select class="form-select" id="idProveedor" name="idProveedor">
                                <option value="">Cargando proveedores...</option>
                            </select>
                            <span class="form-text text-danger" id="sidProveedor"></span>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-0">
                        <div class="col-md-4">
                            <label class="form-label" for="precioProducto">Precio del producto</label>
                            <input class="form-control" type="number" step="0.01" id="precioProducto" name="precioProducto">
                            <span class="form-text text-danger" id="sprecioProducto"></span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="cantidadActual">Cantidad actual</label>
                            <input class="form-control" type="number" id="cantidadActual" name="cantidadActual">
                            <span class="form-text text-danger" id="scantidadActual"></span>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label" for="tipoProducto">Tipo de producto</label>
                            <input class="form-control" type="text" id="tipoProducto" name="tipoProducto" autocomplete="off">
                            <span class="form-text text-danger" id="stipoProducto"></span>
                        </div>
                    </div>

                    
                
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-celeste" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-celeste" type="submit" id="proceso">INCLUIR</button>
            </div>
                </form>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>