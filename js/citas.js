function enviaAjax(datos, onDone, onFail) {
    $.ajax({
        url: 'index.php?pagina=citas',
        type: 'POST',
        data: datos,
        processData: false,
        contentType: false,
        cache: false,
        success: function (respuesta) {
            if (typeof respuesta === 'string') {
                try {
                    respuesta = JSON.parse(respuesta);
                } catch (e) {
                    Swal.fire('Error', 'Respuesta inválida del servidor.', 'error');
                    return;
                }
            }
            if (typeof onDone === 'function') {
                onDone(respuesta);
            }
        },
        error: function (xhr) {
            var mensaje = 'No se pudo completar la solicitud.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.mensaje) {
                mensaje = xhr.responseJSON.mensaje;
            }
            if (typeof onFail === 'function') {
                onFail(mensaje);
            } else {
                Swal.fire('Error', mensaje, 'error');
            }
        }
    });
}

function cargarCatalogos() {
    $.when(
        $.ajax({ url: 'index.php?pagina=citas', type: 'POST', data: { accion: 'clientes' }, dataType: 'json' }),
        $.ajax({ url: 'index.php?pagina=citas', type: 'POST', data: { accion: 'esteticistas' }, dataType: 'json' }),
        $.ajax({ url: 'index.php?pagina=citas', type: 'POST', data: { accion: 'servicios' }, dataType: 'json' })
    ).done(function (clientesResp, esteticistasResp, serviciosResp) {
        const clientes = clientesResp[0]?.data || [];
        const esteticistas = esteticistasResp[0]?.data || [];
        const servicios = serviciosResp[0]?.data || [];

        const $clientes = $('#cedulaCliente');
        const $esteticistas = $('#cedulaEsteticista');
        const $lista = $('#lista-servicios');

        $clientes.empty().append('<option value="">Seleccione un cliente</option>');
        clientes.forEach(function (item) {
            $clientes.append('<option value="' + (item.cedula || '') + '">' + (item.nombre || 'Sin nombre') + ' (' + (item.cedula || '') + ')</option>');
        });
        $('#editar-cliente').html($clientes.html());

        $esteticistas.empty().append('<option value="">Seleccione un esteticista</option>');
        esteticistas.forEach(function (item) {
            $esteticistas.append('<option value="' + (item.cedula || '') + '">' + (item.nombre || 'Sin nombre') + ' (' + (item.cedula || '') + ')</option>');
        });
        $('#editar-esteticista').html($esteticistas.html());

        renderServicios('#lista-servicios', servicios, []);
        renderServicios('#editar-lista-servicios', servicios, []);

        actualizarTotal();
    }).fail(function () {
        var xhr = arguments[0];
        var mensaje = xhr && xhr.responseJSON && xhr.responseJSON.mensaje
            ? xhr.responseJSON.mensaje
            : 'No se pudieron cargar los datos de clientes, esteticistas o servicios.';
        Swal.fire('Error', mensaje, 'error');
    });
}

var tasaDolarCita = 0;

function renderServicios(selector, servicios, seleccionados) {
    var $lista = $(selector).empty();
    tasaDolarCita = servicios.length ? Number(servicios[0].tasaDolar) || 0 : 0;
    if (!servicios.length) {
        $lista.append('<div class="col-12 text-muted">No hay servicios registrados.</div>');
        return;
    }

    servicios.forEach(function (servicio) {
        var precioBs = servicio.precioBs === null ? null : Number(servicio.precioBs);
        var seleccionado = seleccionados.indexOf(String(servicio.idServicio)) !== -1 ? ' checked' : '';
        var card = $(
            '<div class="col-md-6">' +
            '  <label class="border rounded p-3 d-flex align-items-start gap-2 h-100 bg-light">' +
            '    <input type="checkbox" class="form-check-input mt-1 servicio-check" name="servicios[]" value="' + (servicio.idServicio || '') + '" data-precio="' + (precioBs === null ? '' : precioBs) + '"' + seleccionado + '>' +
            '    <span><strong>' + (servicio.nombreServicio || '') + '</strong><br>' +
            '      <small class="text-muted">' + (servicio.descripcion || 'Sin descripción') + '</small><br>' +
            '      <span class="text-success fw-semibold">' +
            (precioBs === null
                ? 'Tasa USD de hoy no registrada'
                : 'Bs ' + precioBs.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2})) +
            '</span></span>' +
            '  </label>' +
            '</div>'
        );
        $lista.append(card);
    });
}

function actualizarTotal() {
    var total = 0;
    $('#lista-servicios .servicio-check:checked').each(function () {
        total += Number($(this).data('precio') || 0);
    });
    $('#total-cita').text(tasaDolarCita <= 0
        ? 'Tasa USD de hoy no registrada'
        : 'Bs ' + total.toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

function fechaLocalActual() {
    var hoy = new Date();
    var mes = String(hoy.getMonth() + 1).padStart(2, '0');
    var dia = String(hoy.getDate()).padStart(2, '0');
    return hoy.getFullYear() + '-' + mes + '-' + dia;
}

function validarFechaCita(selector) {
    var $fecha = $(selector);
    var invalida = Boolean($fecha.val()) && $fecha.val() < fechaLocalActual();
    $fecha.toggleClass('is-invalid', invalida);
    return !invalida;
}

function consultar() {
    var datos = new FormData();
    datos.append('accion', 'consultar');

    enviaAjax(datos, function (respuesta) {
        var lista = respuesta && respuesta.ok ? (respuesta.data || []) : [];
        window.citasListado = lista;
        var $tbody = $('#resultadoconsulta');
        $tbody.empty();

        if (!lista.length) {
            $tbody.append('<tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>');
            return;
        }

        lista.forEach(function (item) {
            var estado = String(item.estado_cita || 'pendiente').toLowerCase();
            var $fila = $('<tr>');
            $fila.append($('<td>').text(item.nombreCliente || ''));
            $fila.append($('<td>').text(item.nombreEsteticista || ''));
            $fila.append($('<td>').text(item.fecha_cita || ''));
            $fila.append($('<td>').text(item.hora || ''));
            $fila.append($('<td>').text(item.servicios || 'Sin servicios'));
            $fila.append($('<td>').append(
                $('<span>').addClass('badge ' + ({
                    finalizada: 'text-bg-success',
                    pagada: 'text-bg-primary',
                    cancelada: 'text-bg-danger'
                }[estado] || 'text-bg-secondary')).text(estado.charAt(0).toUpperCase() + estado.slice(1))
            ));

            var $acciones = $('<td>').addClass('text-nowrap');
            if (!['pagada', 'cancelada', 'finalizada'].includes(estado)) {
                $acciones.append($('<button>').attr({
                    type: 'button',
                    title: 'Modificar cita',
                    'data-id-cita': item.idCita
                }).addClass('btn btn-sm btn-purple me-1 btn-editar-cita')
                    .append($('<i>').addClass('fas fa-edit')));
                $acciones.append(
                    crearBotonEstado(item.idCita, 'finalizada', 'Finalizar', 'fa-check', 'btn-success'),
                    crearBotonEstado(item.idCita, 'cancelada', 'Cancelar cita', 'fa-ban', 'btn-warning')
                );
                $acciones.append($('<button>').attr({
                    type: 'button',
                    title: 'Eliminar cita',
                    'data-id-cita': item.idCita
                }).addClass('btn btn-sm btn-danger btn-eliminar-cita ms-1')
                    .append($('<i>').addClass('fas fa-trash-alt')));
            } else if (estado === 'finalizada') {
                $acciones.append(
                    crearBotonEstado(item.idCita, 'pagada', 'Marcar pagada', 'fa-coins', 'btn-primary')
                );
            }
            $fila.append($acciones);
            $tbody.append($fila);
        });
    }, function () {
        $('#resultadoconsulta').html('<tr><td colspan="7" class="text-center text-danger">No se pudo consultar las citas.</td></tr>');
    });
}

function crearBotonEstado(idCita, estado, etiqueta, icono, clase) {
    return $('<button>').attr({
        type: 'button',
        title: etiqueta,
        'aria-label': etiqueta + ' para la cita ' + idCita,
        'data-id-cita': idCita,
        'data-estado': estado
    }).addClass('btn btn-sm ' + clase + ' me-1 btn-estado-cita')
        .append($('<i>').addClass('fas ' + icono));
}

$(document).ready(function () {
    cargarCatalogos();
    consultar();

    $('#fecha_cita, #editar-fecha').on('input change', function () {
        validarFechaCita(this);
    });

    $('#btn-guardar-cita').on('click', function () {
        $('#formulario_cita').trigger('submit');
    });

    $('#lista-servicios').on('change', '.servicio-check', function () {
        actualizarTotal();
    });

    $('#resultadoconsulta').on('click', '.btn-eliminar-cita', function () {
        var idCita = $(this).data('id-cita');
        Swal.fire({
            title: '¿Eliminar cita?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;
            var datos = new FormData();
            datos.append('accion', 'eliminar');
            datos.append('idCita', idCita);
            enviaAjax(datos, function (respuesta) {
                Swal.fire('Éxito', respuesta.mensaje, 'success');
                consultar();
            }, function (mensaje) { Swal.fire('Error', mensaje, 'error'); });
        });
    });

    $('#resultadoconsulta').on('click', '.btn-estado-cita', function () {
        var idCita = $(this).attr('data-id-cita');
        var estado = $(this).attr('data-estado');
        var etiquetas = {
            finalizada: 'finalizada',
            cancelada: 'cancelada',
            pagada: 'pagada'
        };
        Swal.fire({
            title: '¿Marcar cita como ' + etiquetas[estado] + '?',
            text: estado === 'finalizada'
                ? 'Se descontarán del inventario los insumos usados por los servicios de esta cita.'
                : 'Confirma que deseas actualizar el estado de esta cita.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Volver'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;
            var datos = new FormData();
            datos.append('accion', 'actualizarEstado');
            datos.append('idCita', idCita);
            datos.append('estado', estado);
            enviaAjax(datos, function (respuesta) {
                Swal.fire('Estado actualizado', respuesta.mensaje, 'success');
                consultar();
            }, function (mensaje) {
                Swal.fire('Error', mensaje, 'error');
            });
        });
    });

    $('#resultadoconsulta').on('click', '.btn-editar-cita', function () {
        var idCita = $(this).data('id-cita');
        var item = $(this).data('cita');
        var lista = window.citasListado || [];
        item = lista.find(function (cita) { return String(cita.idCita) === String(idCita); });
        if (!item) return;

        $('#editar-id-cita').val(item.idCita);
        $('#editar-cliente').val(item.cedulaCliente);
        $('#editar-esteticista').val(item.cedulaEsteticista);
        $('#editar-fecha').val(item.fecha_cita);
        validarFechaCita('#editar-fecha');
        $('#editar-hora').val(item.hora);
        $('#editar-lista-servicios .servicio-check').prop('checked', false).each(function () {
            var idServicio = String($(this).val());
            var seleccionados = (item.idsServicios || '').split(',').map(function (id) { return id.trim(); });
            $(this).prop('checked', seleccionados.indexOf(idServicio) !== -1);
        });
        bootstrap.Modal.getOrCreateInstance($('#modal-editar-cita')[0]).show();
    });

    $('#formulario_editar_cita').on('submit', function (e) {
        e.preventDefault();
        if (!validarFechaCita('#editar-fecha')) {
            return;
        }
        if (!$('#formulario_editar_cita .servicio-check:checked').length) {
            Swal.fire('Faltan servicios', 'Selecciona al menos un servicio para la cita.', 'warning');
            return;
        }
        var datos = new FormData(this);
        datos.append('accion', 'modificar');
        enviaAjax(datos, function (respuesta) {
            bootstrap.Modal.getOrCreateInstance($('#modal-editar-cita')[0]).hide();
            Swal.fire('Éxito', respuesta.mensaje, 'success');
            consultar();
        }, function (mensaje) { Swal.fire('Error', mensaje, 'error'); });
    });

    $('#formulario_cita').on('submit', function (e) {
        e.preventDefault();

        if (!$('#cedulaCliente').val() || !$('#cedulaEsteticista').val() || !$('#fecha_cita').val() || !$('#hora').val()) {
            Swal.fire('Faltan datos', 'Debes completar cliente, esteticista, fecha y hora.', 'warning');
            return;
        }

        if (!validarFechaCita('#fecha_cita')) {
            return;
        }

        if ($('.servicio-check:checked').length === 0) {
            Swal.fire('Faltan servicios', 'Selecciona al menos un servicio para la cita.', 'warning');
            return;
        }

        var datos = new FormData(this);
        datos.append('accion', 'incluir');

        enviaAjax(datos, function (respuesta) {
            if (respuesta && respuesta.ok) {
                Swal.fire('Éxito', respuesta.mensaje || 'Cita registrada correctamente.', 'success');
                $('#formulario_cita')[0].reset();
                actualizarTotal();
                consultar();
                return;
            }
            Swal.fire('Error', (respuesta && respuesta.mensaje) || 'No se pudo guardar la cita.', 'error');
        }, function (mensaje) {
            Swal.fire('Error', mensaje, 'error');
        });
    });
});
