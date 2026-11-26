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
        return Number(valor || 0).toLocaleString('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatearTasa(valor) {
        return valor === '' || valor === null || valor === undefined
            ? ''
            : Number(valor).toFixed(4).replace(/\.?0+$/, '');
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
                $('#id_moneda').append(
                    $('<option>')
                        .val(moneda.id_moneda)
                        .attr('data-tasa', moneda.tasa_actual === null ? '' : moneda.tasa_actual)
                        .text(moneda.nombre_moneda + ' (' + moneda.simbolo + ')')
                );
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
                ).append(
                    $('<span>').addClass('form-text text-danger error-cantidad-carrito')
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
        var tasa = Number($('#tasa_cobrada_exacta').val()) || 0;
        var moneda = $('#id_moneda option:selected').text();
        $('#montoPago').text((moneda && !$('#id_moneda').val() ? '' : moneda + ' ') + dinero(total * tasa));
    }

    function consultarVentas() {
        var datos = new FormData();
        datos.append('accion', 'consultar');
        enviar(datos, function (respuesta) {
            var $tbody = $('#listaVentas').empty();
            if (!respuesta.ok || !respuesta.data.length) {
                var $filaSinVentas = $('<tr>');
                $filaSinVentas.append($('<td>')
                    .addClass('text-center text-muted')
                    .text(respuesta.ok ? 'Todavía no hay ventas registradas.' : 'No se pudieron cargar las ventas.'));
                for (var i = 1; i < 8; i++) {
                    $filaSinVentas.append($('<td>').html('&nbsp;'));
                }
                $tbody.append($filaSinVentas);
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
                $fila.append($('<td>').text('#' + venta.idVenta));
                $fila.append($('<td>').text(venta.fechaCompra));
                $fila.append($('<td>').text(venta.nombreCliente));
                $fila.append($('<td>').text(venta.productos));
                $fila.append($('<td>').text('$' + dinero(venta.totalVenta)));
                $fila.append($('<td>').text(venta.nom_MetodoPago || '—'));
                $fila.append($('<td>').text(
                    venta.monto_pagado === null ? '—' : (venta.simbolo || '') + ' ' + dinero(venta.monto_pagado)
                ));
                $fila.append($acciones);
                $tbody.append($fila);
            });
        }, function () {
            var $filaError = $('<tr>').append(
                $('<td>').addClass('text-center text-danger').text('Error al consultar las ventas.')
            );
            for (var i = 1; i < 8; i++) {
                $filaError.append($('<td>').html('&nbsp;'));
            }
            $('#listaVentas').empty().append($filaError);
        });
    }

    function prepararVentaNueva() {
        idVentaEnEdicion = null;
        carrito = {};
        preciosCarrito = {};
        cantidadesOriginales = {};
        $('#formVenta')[0].reset();
        $('#tasa_cobrada').val('');
        $('#tasa_cobrada_exacta').val('');
        $('#fechaCompra').val(fechaHoraLocal());
        $('#formVenta .is-invalid').removeClass('is-invalid');
        $('#formVenta .form-text.text-danger').text('');
        $('#modalVentaLabel').text('Registrar venta');
        $('#guardarVenta').text('Confirmar venta');
        actualizarCarrito();
    }

    function seleccionarValido(selector) {
        var valor = $(selector).val();
        return /^[1-9]\d*$/.test(valor) &&
            Number.isSafeInteger(Number(valor)) &&
            Number(valor) <= 2147483647 &&
            $(selector + ' option').filter(function () {
                return this.value === valor;
            }).length > 0;
    }

    function mostrarErrorVenta(campo, error) {
        $('#' + campo).toggleClass('is-invalid', Boolean(error));
        $('#s' + campo).text(error || '');
        return !error;
    }

    function validarCampoVenta(campo) {
        var error = '';
        if (campo === 'cedulaCliente' && !seleccionarValido('#cedulaCliente')) {
            error = 'Seleccione un cliente válido.';
        } else if (campo === 'idMetodoPago' && !seleccionarValido('#idMetodoPago')) {
            error = 'Seleccione un método de pago válido.';
        } else if (campo === 'id_moneda' && !seleccionarValido('#id_moneda')) {
            error = 'Seleccione una moneda válida.';
        } else if (campo === 'fechaCompra') {
            var fechaEntrada = $('#fechaCompra').val();
            var partesFecha = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/.exec(fechaEntrada);
            if (partesFecha) {
                var fecha = new Date(
                    Number(partesFecha[1]),
                    Number(partesFecha[2]) - 1,
                    Number(partesFecha[3]),
                    Number(partesFecha[4]),
                    Number(partesFecha[5])
                );
                if (
                    fecha.getFullYear() !== Number(partesFecha[1]) ||
                    fecha.getMonth() !== Number(partesFecha[2]) - 1 ||
                    fecha.getDate() !== Number(partesFecha[3]) ||
                    fecha.getHours() !== Number(partesFecha[4]) ||
                    fecha.getMinutes() !== Number(partesFecha[5])
                ) {
                    partesFecha = null;
                }
            }
            if (!partesFecha) {
                error = 'Ingrese una fecha y hora de venta válida.';
            }
        } else if (campo === 'tasa_cobrada') {
            var tasa = $('#tasa_cobrada').val().trim();
            var tasaNumerica = Number(tasa);
            if (
                !/^\d+(?:\.\d{1,4})?$/.test(tasa) ||
                !Number.isFinite(tasaNumerica) ||
                tasaNumerica <= 0 ||
                tasaNumerica > 999999.9999
            ) {
                error = 'La tasa debe ser mayor que cero y tener hasta 4 decimales.';
            }
            $('#tasa_cobrada_exacta').val(tasa);
        } else if (campo === 'referencia') {
            if (new TextEncoder().encode($('#referencia').val()).length > 50) {
                error = 'La referencia no puede superar los 50 caracteres.';
            }
        } else if (campo === 'productoVenta') {
            var producto = $('#productoVenta').val();
            if (producto && !productosDisponibles[producto]) {
                error = 'Seleccione un producto válido.';
            }
        } else if (campo === 'cantidadVenta') {
            var productoSeleccionado = productosDisponibles[$('#productoVenta').val()];
            var cantidad = $('#cantidadVenta').val().trim();
            if (cantidad && (!/^\d+$/.test(cantidad) || Number(cantidad) < 1)) {
                error = 'Ingrese una cantidad entera mayor que cero.';
            } else if (productoSeleccionado && (!cantidad || Number(cantidad) > stockDisponible(productoSeleccionado.idProducto))) {
                error = 'La cantidad debe estar entre 1 y las unidades disponibles.';
            }
        }
        return mostrarErrorVenta(campo, error);
    }

    function validarCantidadCarrito(input) {
        var id = String($(input).attr('data-id'));
        var cantidad = $(input).val().trim();
        var disponible = stockDisponible(id);
        var error = '';
        if (!/^\d+$/.test(cantidad) || Number(cantidad) < 1 || Number(cantidad) > disponible) {
            error = 'Ingrese una cantidad entre 1 y ' + disponible + '.';
        }
        $(input).toggleClass('is-invalid', Boolean(error));
        $(input).siblings('.error-cantidad-carrito').text(error);
        return !error;
    }

    function validarFormularioVenta() {
        var campos = ['cedulaCliente', 'fechaCompra', 'idMetodoPago', 'id_moneda', 'tasa_cobrada', 'referencia'];
        var valido = true;
        campos.forEach(function (campo) {
            if (!validarCampoVenta(campo)) {
                valido = false;
            }
        });
        var carritoValido = Object.keys(carrito).length > 0;

        $('#sproductosVenta').text(carritoValido ? '' : 'Agregue al menos un producto a la venta.');
        Object.keys(carrito).forEach(function (id) {
            var cantidad = carrito[id];
            if (
                !productosDisponibles[id] ||
                !Number.isSafeInteger(cantidad) ||
                cantidad < 1 ||
                cantidad > 2147483647 ||
                cantidad > stockDisponible(id)
            ) {
                carritoValido = false;
                valido = false;
                $('#sproductosVenta').text('Revise los productos y sus cantidades.');
            }
        });
        $('.cantidad-carrito').each(function () {
            if (!validarCantidadCarrito(this)) {
                carritoValido = false;
                valido = false;
            }
        });

        if (!carritoValido) {
            valido = false;
            if (!Object.keys(carrito).length) {
                $('#agregarProducto').trigger('focus');
            } else {
                $('.cantidad-carrito.is-invalid').first().trigger('focus');
            }
        }
        var campoInvalido = campos.find(function (campo) {
            return $('#' + campo).hasClass('is-invalid');
        });
        if (campoInvalido) {
            $('#' + campoInvalido).trigger('focus');
        }

        if (idVentaEnEdicion && (!/^[1-9]\d*$/.test(idVentaEnEdicion) || Number(idVentaEnEdicion) > 2147483647)) {
            Swal.fire('Venta inválida', 'La venta seleccionada no es válida.', 'warning');
            return false;
        }
        return valido && carritoValido;
    }

    $('#nuevaVenta').on('click', function () {
        prepararVentaNueva();
        modal.show();
    });

    $('#id_moneda').on('change', function () {
        var tasa = $(this).find(':selected').attr('data-tasa') || '';
        $('#tasa_cobrada').val(formatearTasa(tasa));
        $('#tasa_cobrada_exacta').val(tasa);
        validarCampoVenta('id_moneda');
        validarCampoVenta('tasa_cobrada');
        actualizarMonto(totalCarrito());
    });

    $('#tasa_cobrada').on('input', function () {
        $('#tasa_cobrada_exacta').val($(this).val());
        validarCampoVenta('tasa_cobrada');
        actualizarMonto(totalCarrito());
    });

    $('#formVenta').on('input change', 'input, select', function () {
        if (['cedulaCliente', 'fechaCompra', 'idMetodoPago', 'id_moneda', 'tasa_cobrada', 'referencia'].indexOf(this.id) !== -1) {
            validarCampoVenta(this.id);
        }
    });

    $('#productoVenta').on('change', function () {
        validarCampoVenta('productoVenta');
        var producto = productosDisponibles[$(this).val()];
        $('#cantidadVenta').attr('max', producto ? stockDisponible(producto.idProducto) : '');
        validarCampoVenta('cantidadVenta');
    });

    $('#cantidadVenta').on('input change', function () {
        validarCampoVenta('cantidadVenta');
    });

    $('#agregarProducto').on('click', function () {
        var id = $('#productoVenta').val();
        var producto = productosDisponibles[id];
        var cantidad = Number($('#cantidadVenta').val());
        if (!producto) {
            mostrarErrorVenta('productoVenta', 'Seleccione un producto válido.');
            $('#productoVenta').trigger('focus');
            return;
        }
        if (!validarCampoVenta('cantidadVenta')) {
            $('#cantidadVenta').trigger('focus');
            return;
        }
        var nuevaCantidad = (carrito[id] || 0) + cantidad;
        var disponible = stockDisponible(id);
        if (nuevaCantidad > disponible) {
            mostrarErrorVenta('cantidadVenta', 'Solo hay ' + disponible + ' unidades disponibles.');
            $('#cantidadVenta').trigger('focus');
            return;
        }
        carrito[id] = nuevaCantidad;
        if (preciosCarrito[id] === undefined) {
            preciosCarrito[id] = producto.precioProducto;
        }
        $('#productoVenta').val('');
        $('#cantidadVenta').val(1);
        $('#cantidadVenta').removeAttr('max');
        mostrarErrorVenta('productoVenta', '');
        mostrarErrorVenta('cantidadVenta', '');
        $('#sproductosVenta').text('');
        actualizarCarrito();
    });

    $('#productosVenta').on('input', '.cantidad-carrito', function () {
        validarCantidadCarrito(this);
    });

    $('#productosVenta').on('change', '.cantidad-carrito', function () {
        var id = String($(this).attr('data-id'));
        var cantidad = Number($(this).val());
        if (!validarCantidadCarrito(this)) {
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
                $('#formVenta .is-invalid').removeClass('is-invalid');
                $('#formVenta .form-text.text-danger').text('');
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
                var tasa = venta.tasa_cobrada || '1';
                $('#tasa_cobrada').val(formatearTasa(tasa));
                $('#tasa_cobrada_exacta').val(tasa);
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
        if (!validarFormularioVenta() || !this.reportValidity()) {
            return;
        }

        $('.cantidad-carrito').each(function () {
            carrito[String($(this).attr('data-id'))] = Number($(this).val());
        });
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
