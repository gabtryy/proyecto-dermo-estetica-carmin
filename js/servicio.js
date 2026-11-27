// Función centralizada para enviar AJAX
function enviaAjax({datos, done, fail, always, url = 'index.php?pagina=servicios', type = 'POST', dataType = 'json'}) {
    $.ajax({
        url: url,
        type: type,
        data: datos,
        processData: false,
        contentType: false,
        dataType: dataType
    })
    .done(done || function(){})
    .fail(fail || function(){})
    .always(always || function(){});
}

var serviciosDisponibles = {};

function consultar() {
    var datos = new FormData();
    datos.append('accion', 'consultar');
    enviaAjax({
        datos: datos,
        done: function (resp) {
            if (!resp?.ok) return;
            renderTabla(resp.data || []);
        },
        fail: function (xhr) {
            var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
                ? xhr.responseJSON.mensaje
                : 'No se pudieron consultar los servicios.';
            Swal.fire('Error', mensaje, 'error');
        }
    });
}

function renderTabla(lista) {
    var $filas = $('<tbody>');
    serviciosDisponibles = {};

    if (!lista.length) {
        $filas.append('<tr><td colspan="5" class="text-center text-muted">Sin registros</td></tr>');
    } else {
        lista.forEach(function (item) {
            serviciosDisponibles[String(item.idServicio)] = item;
            var $fila = $('<tr>');
            $fila.append($('<td>').text(item.nombreServicio || ''));
            var $precio = $('<td>').append($('<div>').text(
                item.precioBs === null
                    ? 'Bs — tasa de hoy no registrada'
                    : 'Bs ' + Number(item.precioBs).toLocaleString('es-VE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
            ));
            $precio.append($('<small>').addClass('text-muted').text(
                '$ ' + Number(item.precioUSD).toLocaleString('es-VE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            ));
            $precio.attr('data-precio', item.precioUSD || '');
            $fila.append($precio);
            $fila.append($('<td>').text(item.insumos || 'Sin insumos asignados'));
            $fila.append($('<td>').text(item.descripcion || ''));
            $fila.append($('<td>').addClass('text-nowrap').append(
                $('<button>').attr({
                    type: 'button',
                    title: 'Modificar',
                    'aria-label': 'Modificar ' + (item.nombreServicio || 'servicio'),
                    'data-id-servicio': item.idServicio || ''
                }).addClass('btn btn-sm btn-purple me-1 btn-editar')
                    .append($('<i>').addClass('fas fa-edit'))
            ).append(
                $('<button>').attr({
                    type: 'button',
                    title: 'Eliminar',
                    'aria-label': 'Eliminar ' + (item.nombreServicio || 'servicio'),
                    'data-id-servicio': item.idServicio || ''
                }).addClass('btn btn-sm btn-danger btn-eliminar')
                    .append($('<i>').addClass('fas fa-trash-alt'))
            ));
            $filas.append($fila);
        });
    }

    actualizarTablaDataTable('#resultadoconsulta', function ($tbody) {
        $tbody.append($filas.children());
    });
}

$(document).ready(function () {
    var currentEditId = null;
    var tasaDolar = 0;
    var productosServicioCatalogo = [];

    function renderProductosServicio(seleccionados) {
        var $contenedor = $('#productosServicio').empty();
        if (!productosServicioCatalogo.length) {
            $contenedor.append(
                $('<div>').addClass('text-muted').text('No hay productos clasificados como Insumo o Ambos.')
            );
            return;
        }

        productosServicioCatalogo.forEach(function (producto) {
            var idProducto = String(producto.idProducto);
            var configuracion = seleccionados[idProducto];
            var seleccionado = configuracion !== undefined;
            var rendimiento = seleccionado && typeof configuracion === 'object'
                ? configuracion.rendimiento
                : 1;
            var $checkbox = $('<input>').attr({
                type: 'checkbox',
                value: idProducto,
                'aria-label': 'Usar ' + producto.nombreProducto
            }).addClass('form-check-input producto-servicio-check').prop('checked', seleccionado);
            var $rendimiento = $('<input>').attr({
                type: 'number',
                min: 1,
                max: 1000000,
                step: 1,
                'aria-label': 'Sesiones que rinde una unidad de ' + producto.nombreProducto
            }).addClass('form-control form-control-sm rendimiento-producto-servicio')
                .val(rendimiento)
                .prop('disabled', !seleccionado);
            var $fila = $('<div>').addClass('col-md-6');
            var $panel = $('<div>').addClass('border rounded p-3 h-100');
            var $nombre = $('<label>').addClass('form-check-label fw-semibold')
                .append($checkbox)
                .append(document.createTextNode(' ' + producto.nombreProducto));
            $panel.append($('<div>').addClass('form-check').append($nombre));
            $panel.append(
                $('<small>').addClass('d-block text-muted mt-1').text(
                    producto.usoProducto + ' · Existencia: ' +
                    Number(producto.cantidadActual).toLocaleString('es-VE', {maximumFractionDigits: 6})
                )
            );
            var $configuracion = $('<div>').addClass('row g-2 mt-1');
            $configuracion.append(
                $('<div>').addClass('col-12')
                    .append($('<label>').addClass('form-label small mb-1').text('Sesiones que rinde 1 unidad'))
                    .append($rendimiento)
            );
            $panel.append($configuracion);
            $fila.append($panel);
            $contenedor.append($fila);
        });
    }

    function obtenerProductosSeleccionados() {
        var productos = {};
        var valido = true;
        $('#productosServicio .producto-servicio-check:checked').each(function () {
            var $panel = $(this).closest('.border');
            var rendimiento = String($panel.find('.rendimiento-producto-servicio').val() || '');
            var rendimientoInvalido = !/^[1-9]\d*$/.test(rendimiento) || Number(rendimiento) > 1000000;
            if (rendimientoInvalido) {
                valido = false;
                $panel.find('.rendimiento-producto-servicio').toggleClass('is-invalid', rendimientoInvalido);
                return;
            }
            productos[String($(this).val())] = {
                rendimiento: Number(rendimiento)
            };
        });
        return valido ? productos : null;
    }

    function validarCampoServicio(campo) {
        var $campo = $('#' + campo);
        var valor = String($campo.val() || '').trim();
        var error = '';

        if (campo === 'nombreServicio' && (Array.from(valor).length < 3 || Array.from(valor).length > 100)) {
            error = 'El nombre del servicio debe tener entre 3 y 100 caracteres.';
        } else if (campo === 'precio') {
            var precio = Number(valor);
            if (!/^\d{1,8}(?:\.\d{1,2})?$/.test(valor) || !Number.isFinite(precio) || precio <= 0) {
                error = 'El precio en USD debe ser mayor que cero y tener como máximo dos decimales.';
            } else if (tasaDolar <= 0) {
                error = 'Registre la tasa del dólar de hoy para mostrar el precio en bolívares.';
            }
        }

        $('#s' + campo).text(error);
        $campo.toggleClass('is-invalid', Boolean(error));
        return !error;
    }

    function validarFormularioServicio() {
        var nombreValido = validarCampoServicio('nombreServicio');
        var precioValido = validarCampoServicio('precio');
        return nombreValido && precioValido;
    }

    function actualizarEquivalenciaUSD() {
        var precioUSD = Number($('#precio').val());
        var texto = tasaDolar > 0 && precioUSD > 0
            ? 'Precio actual: Bs ' + (precioUSD * tasaDolar).toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' (tasa: Bs ' + tasaDolar.toLocaleString('es-VE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 4
            }) + ' por USD).'
            : 'Registra la tasa del dólar de hoy para calcular el precio en bolívares.';
        $('#equivalenciaUSDServicio').text(texto);
    }

    $('#formulario_servicio').on('input change', 'input:not([type="hidden"]), textarea', function () {
        if (this.id === 'nombreServicio' || this.id === 'precio') {
            validarCampoServicio(this.id);
        }
        if ($(this).hasClass('producto-servicio-check')) {
            $(this).closest('.border').find('.rendimiento-producto-servicio')
                .prop('disabled', !this.checked)
                .toggleClass('is-invalid', false);
        }
        if ($(this).hasClass('rendimiento-producto-servicio')) {
            $(this).removeClass('is-invalid');
        }
        actualizarEquivalenciaUSD();
    });

    document.getElementById('modal1').addEventListener('hidden.bs.modal', function () {
        $('#formulario_servicio .is-invalid').removeClass('is-invalid');
        $('#snombreServicio, #sprecio, #sdescripcion').text('');
    });
    
    // Delegación para botón eliminar
    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var idServicio = $(this).data('idServicio') || $(this).attr('data-id-servicio');
        console.log('btn-eliminar clicked, idServicio=', idServicio);
        if (!idServicio) return;

        Swal.fire({
            title: '¿Eliminar servicio?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('idServicio', idServicio  );

                enviaAjax({
                    datos: datos,
                    done: function (resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Servicio eliminado.', 'success');
                            consultar();
                        } else {
                            Swal.fire('Error', resp?.mensaje || 'No se pudo eliminar.', 'error');
                        }
                    },
                    fail: function () {
                        Swal.fire('Error', 'No se pudo comunicarse con el servidor.', 'error');
                    }
                });
            }
        });
    });

    $('#resultadoconsulta').on('click', '.btn-editar', function () {
        var idServicio = $(this).attr('data-id-servicio');
        if (!idServicio) return;

        var $fila = $(this).closest('tr');
        currentEditId = idServicio;
        var servicio = serviciosDisponibles[String(idServicio)] || {};
        var seleccionados = {};
        String(servicio.productosServicio || '').split(',').forEach(function (par) {
            var datos = par.split(':');
            if (datos.length >= 2) {
                seleccionados[datos[0]] = {
                    rendimiento: datos.length >= 3 ? Number(datos[2]) : 1
                };
            }
        });
        renderProductosServicio(seleccionados);
        $('#nombreServicio').val($fila.find('td:eq(0)').text().trim());
        $('#precio').val(servicio.precioUSD);
        $('#descripcion').val($fila.find('td:eq(3)').text().trim());
        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).show();
    });

    $('#incluir').on('click', function () {
        currentEditId = null;
        $('#formulario_servicio')[0].reset();
        renderProductosServicio({});
        $('#proceso').text('REGISTRAR');
        $('#accion').val('incluir');
        actualizarEquivalenciaUSD();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).show();
    });

    $('#formulario_servicio').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        if (!validarFormularioServicio() || !form.reportValidity()) {
            $('#formulario_servicio .is-invalid').first().trigger('focus');
            return;
        }

        var datos = new FormData(form);
        var productosSeleccionados = obtenerProductosSeleccionados();
        if (productosSeleccionados === null) {
            Swal.fire('Rendimiento inválido', 'Indica cuántas sesiones rinde una unidad para cada insumo seleccionado.', 'warning');
            return;
        }
        datos.set('productosServicio', JSON.stringify(productosSeleccionados));
        if ($('#accion').val() === 'modificar' && currentEditId) {
            datos.set('idServicio', currentEditId);
        }

        enviaAjax({
            datos: datos,
            done: function (resp) {
                if (!resp || !resp.ok) {
                    Swal.fire('Error', (resp && resp.mensaje) || 'No se pudo guardar.', 'error');
                    return;
                }
                Swal.fire('¡Éxito!', resp.mensaje || 'Servicio guardado correctamente.', 'success');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
                form.reset();
                currentEditId = null;
                renderProductosServicio({});
                consultar();
            },
            fail: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.mensaje
                    ? xhr.responseJSON.mensaje
                    : 'Error al guardar servicio.';
                Swal.fire('Error', msg, 'error');
            }
        });

    });

    $.ajax({
        url: 'index.php?pagina=servicios',
        type: 'POST',
        data: {accion: 'catalogos'},
        dataType: 'json'
    }).done(function (respuesta) {
        if (!respuesta || !respuesta.ok) {
            Swal.fire('Error', (respuesta && respuesta.mensaje) || 'No se pudieron cargar los insumos.', 'error');
            return;
        }
        productosServicioCatalogo = respuesta.data || [];
        renderProductosServicio({});
    }).fail(function (xhr) {
        var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
            ? xhr.responseJSON.mensaje
            : 'No se pudieron cargar los productos para servicios.';
        $('#productosServicio').empty().append(
            $('<div>').addClass('text-danger').text(mensaje)
        );
        Swal.fire('Error', mensaje, 'error');
    });

    $.ajax({
        url: 'index.php?pagina=configuracion',
        type: 'POST',
        data: {accion: 'consultar'},
        dataType: 'json'
    }).done(function (respuesta) {
        if (respuesta.ok) {
            var dolar = (respuesta.data.monedas || []).find(function (moneda) {
                return /^(dólar|dolar)$/i.test(moneda.nombre_moneda);
            });
            tasaDolar = Number(dolar && dolar.tasa_actual) || 0;
        }
        actualizarEquivalenciaUSD();
    }).fail(function () {
        $('#equivalenciaUSDServicio').text('No se pudo consultar la tasa de hoy.');
    });
    consultar();
});