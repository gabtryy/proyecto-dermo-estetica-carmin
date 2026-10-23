<?php require_once 'vista/layouts/header.php'; ?>

<div class="container py-2">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <button type="button" class="btn btn-purple shadow-sm" id="nuevaVenta">
            <i class="fas fa-cart-plus me-1"></i>REGISTRAR
        </button>
        <a href="index.php?pagina=home" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>REGRESAR
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <span class="fw-semibold">Historial de ventas</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="tablaVentas">
                    <thead>
                        <tr>
                            <th>Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Monto pagado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaVentas">
                        <tr><td colspan="8" class="text-center text-muted">Cargando ventas...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVenta" tabindex="-1" aria-labelledby="modalVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-purple">
                <h2 class="modal-title fs-5" id="modalVentaLabel">Registrar venta</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formVenta" autocomplete="off">
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="cedulaCliente">Cliente</label>
                            <select class="form-select" id="cedulaCliente" name="cedulaCliente" required>
                                <option value="">Seleccione cliente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="fechaCompra">Fecha y hora de venta</label>
                            <input class="form-control" type="datetime-local" id="fechaCompra" name="fechaCompra" required>
                        </div>
                    </div>

                    <h3 class="h6">Productos</h3>
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-7">
                            <label class="form-label" for="productoVenta">Producto disponible</label>
                            <select class="form-select" id="productoVenta">
                                <option value="">Seleccione producto</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="cantidadVenta">Cantidad</label>
                            <input class="form-control" type="number" id="cantidadVenta" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100" id="agregarProducto">
                                <i class="fas fa-plus me-1"></i>Agregar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio unitario</th>
                                    <th style="width: 130px">Cantidad</th>
                                    <th>Subtotal</th>
                                    <th style="width: 70px">Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="productosVenta">
                                <tr><td colspan="5" class="text-center text-muted">Agregue productos para comenzar.</td></tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total de la venta</th>
                                    <th id="totalVenta">$0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <h3 class="h6 mt-4">Pago único</h3>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="idMetodoPago">Método de pago</label>
                            <select class="form-select" id="idMetodoPago" name="idMetodoPago" required>
                                <option value="">Seleccione método</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="id_moneda">Moneda</label>
                            <select class="form-select" id="id_moneda" name="id_moneda" required>
                                <option value="">Seleccione moneda</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tasa_cobrada">Tasa (unidades de moneda por cada $1)</label>
                            <input class="form-control" type="number" id="tasa_cobrada" min="0.01" max="999999.9999" step="0.01" required>
                            <input type="hidden" id="tasa_cobrada_exacta" name="tasa_cobrada">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="referencia">Referencia de pago (opcional)</label>
                            <input class="form-control" type="text" id="referencia" name="referencia" maxlength="50">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <p class="mb-2 text-muted">Monto a cobrar: <strong id="montoPago">0,00</strong></p>
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Los precios de los productos se toman como base en dólares. El inventario se descuenta al confirmar la venta.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple" id="guardarVenta">Confirmar venta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'vista/layouts/footer.php'; ?>
