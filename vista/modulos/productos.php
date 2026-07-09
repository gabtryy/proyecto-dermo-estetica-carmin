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

    <h1 class="h3 mb-3">Productos</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3">
            <span class="fw-semibold">Listado de productos</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table
                    class="table table-striped table-hover table-bordered align-middle mb-0"
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
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary" type="submit" id="proceso">INCLUIR</button>
            </div>
                </form>
        </div>
    </div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>