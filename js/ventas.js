$(document).ready(function () {
    var productosDisponibles = {};
    var carrito = {};
    var preciosCarrito = {};
    var cantidadesOriginales = {};
    var idVentaEnEdicion = null;
    var catalogosListos = $.Deferred();
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalVenta'));

    function enviar(datos, done, fail) {
        return $.ajax({
            url: 'index.php?pagina=ventas',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(done).fail(fail || function () {});
    }

    function dinero(valor) {
        return Number(valor || 0).toFixed(2);
    }

    function fechaHoraLocal() {
        var ahora = new Date();
        var ajustar = function (valor) {
            return String(valor).padStart(2, '0');
        };
        return ahora.getFullYear() + '-' + ajustar(ahora.getMonth() + 1) + '-' +
            ajustar(ahora.getDate()) + 'T' + ajustar(ahora.getHours()) + ':' +
            ajustar(ahora.getMinutes());
    }

    function cargarCatalogos() {
        var datos = new FormData();
        datos.append('accion', 'catalogos');
        enviar(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('Error', respuesta.mensaje || 'No se pudieron cargar los datos.', 'error');
                return;
            }

            var data = respuesta.data;
            productosDisponibles = {};
            $('#cedulaCliente').empty().append($('<option>').val('').text('Seleccione cliente'));
            $('#idMetodoPago').empty().append($('<option>').val('').text('Seleccione método'));
            $('#id_moneda').empty().append($('<option>').val('').text('Seleccione moneda'));
            $('#productoVenta').empty().append($('<option>').val('').text('Seleccione producto'));
            data.clientes.forEach(function (cliente) {
                $('#cedulaCliente').append($('<option>').val(cliente.cedulaCliente).text(cliente.nombreCliente));
            });
            data.metodosPago.forEach(function (metodo) {
                $('#idMetodoPago').append($('<option>').val(metodo.idMetodoPago).text(metodo.nom_MetodoPago));
            });
            data.monedas.forEach(function (moneda) {
                $('#id_moneda').append($('<option>').val(moneda.id_moneda).text(moneda.nombre_moneda + ' (' + moneda.simbolo + ')'));
            });
            data.productos.forEach(function (producto) {
                producto.idProducto = Number(producto.idProducto);
                producto.precioProducto = Number(producto.precioProducto);
                producto.cantidadActual = Number(producto.cantidadActual);
                productosDisponibles[producto.idProducto] = producto;
                $('#productoVenta').append(
                    $('<option>').val(producto.idProducto).text(
                        producto.nombreProducto + ' — $' + dinero(producto.precioProducto) +
                        ' (disponibles: ' + producto.cantidadActual + ')'
                    )
                );
            });
            catalogosListos.resolve();
        }, function () {
            catalogosListos.reject();
            Swal.fire('Error', 'No fue posible cargar clientes, productos y métodos de pago.', 'error');
        });
    }

    function stockDisponible(id) {
        var producto = productosDisponibles[id];
        return producto ? producto.cantidadActual + (cantidadesOriginales[id] || 0) : 0;
    }

    function actualizarCarrito() {
        var $tbody = $('#productosVenta').empty();
        var total = 0;
        var cantidadItems = Object.keys(carrito);

        if (!cantidadItems.length) {
            $tbody.append($('<tr>').append(
                $('<td>').attr('colspan', 5).addClass('text-center text-muted').text('Agregue productos para comenzar.')
            ));
        } else {
            cantidadItems.forEach(function (id) {
                var producto = productosDisponibles[id];
                var cantidad = carrito[id];
                var precio = preciosCarrito[id] === undefined ? producto.precioProducto : preciosCarrito[id];
                var subtotal = precio * cantidad;
                total += subtotal;

                var $fila = $('<tr>');
                $fila.append($('<td>').text(producto.nombreProducto));
                $fila.append($('<td>').text('$' + dinero(precio)));
                $fila.append($('<td>').append(
                    $('<input>').attr({type: 'number', min: 1, max: stockDisponible(id)})
                        .addClass('form-control form-control-sm cantidad-carrito')
                        .attr('data-id', id)
                        .val(cantidad)
                ));
                $fila.append($('<td>').text('$' + dinero(subtotal)));
                $fila.append($('<td>').append(
                    $('<button>').attr({type: 'button', 'data-id': id, 'aria-label': 'Quitar producto'})
                        .addClass('btn btn-sm btn-outline-danger quitar-producto')
                        .append($('<i>').addClass('fas fa-times'))
                ));
                $tbody.append($fila);
            });
        }

        $('#totalVenta').text('$' + dinero(total));
        actualizarMonto(total);
    }

    function totalCarrito() {
        return Object.keys(carrito).reduce(function (suma, id) {
            var precio = preciosCarrito[id] === undefined
                ? productosDisponibles[id].precioProducto
                : preciosCarrito[id];
            return suma + precio * carrito[id];
        }, 0);
    }

    function actualizarMonto(total) {
        var tasa = Number($('#tasa_cobrada').val()) || 0;
        var moneda = $('#id_moneda option:selected').text();
        $('#montoPago').text((moneda && !$('#id_moneda').val() ? '' : moneda + ' ') + dinero(total * tasa));
    }

    function consultarVentas() {
        var datos = new FormData();
        datos.append('accion', 'consultar');
        enviar(datos, function (respuesta) {
            var $tbody = $('#listaVentas').empty();
            if (!respuesta.ok || !respuesta.data.length) {
                $tbody.append($('<tr>').append(
                    $('<td>').attr('colspan', 8).addClass('text-center text-muted')
                        .text(respuesta.ok ? 'Todavía no hay ventas registradas.' : 'No se pudieron cargar las ventas.')
                ));
                return;
            }

            respuesta.data.forEach(function (venta) {
                var $fila = $('<tr>');
                var $acciones = $('<td>').addClass('text-nowrap');
                $acciones.append(
                    $('<button>').attr({
                        type: 'button',
                        title: 'Editar venta',
                        'aria-label': 'Editar venta ' + venta.idVenta,
                        'data-id-venta': venta.idVenta
                    }).addClass('btn btn-sm btn-purple me-1 editar-venta')
                        .append($('<i>').addClass('fas fa-edit'))
                );
                $acciones.append(
                    $('<button>').attr({
                        type: 'button',
                        title: 'Eliminar venta',
                        'aria-label': 'Eliminar venta ' + venta.idVenta,
                        'data-id-venta': venta.idVenta
                    }).addClass('btn btn-sm btn-danger eliminar-venta')
                        .append($('<i>').addClass('fas fa-trash-alt'))
                );
                $fila.append($acciones);
                $fila.append($('<td>').text('#' + venta.idVenta));
                $fila.append($('<td>').text(venta.fechaCompra));
                $fila.append($('<td>').text(venta.nombreCliente));
                $fila.append($('<td>').text(venta.productos));
                $fila.append($('<td>').text('$' + dinero(venta.totalVenta)));
                $fila.append($('<td>').text(venta.nom_MetodoPago || '—'));
                $fila.append($('<td>').text(
                    venta.monto_pagado === null ? '—' : (venta.simbolo || '') + ' ' + dinero(venta.monto_pagado)
                ));
                $tbody.append($fila);
            });
        }, function () {
            $('#listaVentas').empty().append($('<tr>').append(
                $('<td>').attr('colspan', 8).addClass('text-center text-danger').text('Error al consultar las ventas.')
            ));
        });
    }

    function prepararVentaNueva() {
        idVentaEnEdicion = null;
        carrito = {};
        preciosCarrito = {};
        cantidadesOriginales = {};
        $('#formVenta')[0].reset();
        $('#tasa_cobrada').val('1');
        $('#fechaCompra').val(fechaHoraLocal());
        $('#modalVentaLabel').text('Registrar venta');
        $('#guardarVenta').text('Confirmar venta');
        actualizarCarrito();
    }

    $('#nuevaVenta').on('click', function () {
        prepararVentaNueva();
        modal.show();
    });

    $('#id_moneda, #tasa_cobrada').on('change input', function () {
        actualizarMonto(totalCarrito());
    });

    $('#agregarProducto').on('click', function () {
        var id = $('#productoVenta').val();
        var producto = productosDisponibles[id];
        var cantidad = Number($('#cantidadVenta').val());
        if (!producto || !Number.isInteger(cantidad) || cantidad < 1) {
            Swal.fire('Atención', 'Seleccione un producto e indique una cantidad válida.', 'warning');
            return;
        }
        var nuevaCantidad = (carrito[id] || 0) + cantidad;
        var disponible = stockDisponible(id);
        if (nuevaCantidad > disponible) {
            Swal.fire('Inventario insuficiente', 'Solo hay ' + disponible + ' unidades disponibles.', 'warning');
            return;
        }
        carrito[id] = nuevaCantidad;
        if (preciosCarrito[id] === undefined) {
            preciosCarrito[id] = producto.precioProducto;
        }
        $('#productoVenta').val('');
        $('#cantidadVenta').val(1);
        actualizarCarrito();
    });

    $('#productosVenta').on('change', '.cantidad-carrito', function () {
        var id = String($(this).attr('data-id'));
        var cantidad = Number($(this).val());
        var producto = productosDisponibles[id];
        var disponible = stockDisponible(id);
        if (!Number.isInteger(cantidad) || cantidad < 1 || cantidad > disponible) {
            $(this).val(carrito[id]);
            Swal.fire('Cantidad inválida', 'Ingrese una cantidad entre 1 y ' + disponible + '.', 'warning');
            return;
        }
        carrito[id] = cantidad;
        actualizarCarrito();
    });

    $('#productosVenta').on('click', '.quitar-producto', function () {
        var id = String($(this).attr('data-id'));
        delete carrito[id];
        delete preciosCarrito[id];
        actualizarCarrito();
    });

    $('#listaVentas').on('click', '.editar-venta', function () {
        var idVenta = $(this).attr('data-id-venta');
        var datos = new FormData();
        datos.append('accion', 'detalle');
        datos.append('idVenta', idVenta);

        enviar(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('Error', respuesta.mensaje || 'No se pudo cargar la venta.', 'error');
                return;
            }
            $.when(catalogosListos).done(function () {
                var venta = respuesta.data;
                idVentaEnEdicion = String(venta.idVenta);
                carrito = {};
                preciosCarrito = {};
                cantidadesOriginales = {};
                (venta.productos || []).forEach(function (item) {
                    var id = String(item.idProducto);
                    carrito[id] = Number(item.cantidad);
                    preciosCarrito[id] = Number(item.precioUnitario);
                    cantidadesOriginales[id] = Number(item.cantidad);
                });
                $('#cedulaCliente').val(venta.cedulaCliente);
                $('#fechaCompra').val(String(venta.fechaCompra).replace(' ', 'T').slice(0, 16));
                $('#idMetodoPago').val(venta.idMetodoPago || '');
                $('#id_moneda').val(venta.id_moneda || '');
                $('#tasa_cobrada').val(venta.tasa_cobrada || '1');
                $('#referencia').val(venta.referencia || '');
                $('#modalVentaLabel').text('Editar venta #' + venta.idVenta);
                $('#guardarVenta').text('Guardar cambios');
                actualizarCarrito();
                modal.show();
            });
        }, function (xhr) {
            var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
                ? xhr.responseJSON.mensaje
                : 'No se pudo cargar la venta para editar.';
            Swal.fire('Error', mensaje, 'error');
        });
    });

    $('#listaVentas').on('click', '.eliminar-venta', function () {
        var idVenta = $(this).attr('data-id-venta');
        Swal.fire({
            title: '¿Eliminar la venta #' + idVenta + '?',
            text: 'La operación no se puede deshacer y las unidades vendidas se devolverán al inventario.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) {
                return;
            }
            var datos = new FormData();
            datos.append('accion', 'eliminar');
            datos.append('idVenta', idVenta);
            enviar(datos, function (respuesta) {
                if (!respuesta.ok) {
                    Swal.fire('Error', respuesta.mensaje || 'No se pudo eliminar la venta.', 'error');
                    return;
                }
                Swal.fire('Venta eliminada', respuesta.mensaje, 'success');
                consultarVentas();
                cargarCatalogos();
            }, function (xhr) {
                var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
                    ? xhr.responseJSON.mensaje
                    : 'No se pudo eliminar la venta.';
                Swal.fire('Error', mensaje, 'error');
            });
        });
    });

    $('#formVenta').on('submit', function (event) {
        event.preventDefault();
        if (!Object.keys(carrito).length) {
            Swal.fire('Atención', 'Agregue al menos un producto a la venta.', 'warning');
            return;
        }

        var datos = new FormData(this);
        datos.append('accion', idVentaEnEdicion ? 'modificar' : 'registrar');
        if (idVentaEnEdicion) {
            datos.append('idVenta', idVentaEnEdicion);
        }
        datos.append('productos', JSON.stringify(Object.keys(carrito).map(function (id) {
            return {idProducto: id, cantidad: carrito[id]};
        })));
        $('#guardarVenta').prop('disabled', true);

        enviar(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('No se registró la venta', respuesta.mensaje || 'Verifique los datos e intente nuevamente.', 'error');
                return;
            }
            modal.hide();
            Swal.fire(
                idVentaEnEdicion ? 'Venta modificada' : 'Venta registrada',
                respuesta.mensaje + (respuesta.idVenta ? ' N.º ' + respuesta.idVenta + '.' : ''),
                'success'
            );
            prepararVentaNueva();
            consultarVentas();
            cargarCatalogos();
        }, function (xhr) {
            var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
                ? xhr.responseJSON.mensaje
                : 'No se pudo guardar la venta. Intente nuevamente.';
            Swal.fire('Error', mensaje, 'error');
        }).always(function () {
            $('#guardarVenta').prop('disabled', false);
        });
    });

    cargarCatalogos();
    consultarVentas();
});
