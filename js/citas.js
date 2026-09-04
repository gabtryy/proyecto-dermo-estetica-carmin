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
        Swal.fire('Error', 'No se pudieron cargar los datos de clientes, esteticistas o servicios.', 'error');
    });
}

function renderServicios(selector, servicios, seleccionados) {
    var $lista = $(selector).empty();
    if (!servicios.length) {
        $lista.append('<div class="col-12 text-muted">No hay servicios registrados.</div>');
        return;
    }

    servicios.forEach(function (servicio) {
        var precio = Number(servicio.precio || 0);
        var seleccionado = seleccionados.indexOf(String(servicio.idServicio)) !== -1 ? ' checked' : '';
        var card = $(
            '<div class="col-md-6">' +
            '  <label class="border rounded p-3 d-flex align-items-start gap-2 h-100 bg-light">' +
            '    <input type="checkbox" class="form-check-input mt-1 servicio-check" name="servicios[]" value="' + (servicio.idServicio || '') + '" data-precio="' + precio + '"' + seleccionado + '>' +
            '    <span><strong>' + (servicio.nombreServicio || '') + '</strong><br>' +
            '      <small class="text-muted">' + (servicio.descripcion || 'Sin descripción') + '</small><br>' +
            '      <span class="text-success fw-semibold">$' + precio.toFixed(2) + '</span></span>' +
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
    $('#total-cita').text('$' + total.toFixed(2));
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
            $tbody.append('<tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>');
            return;
        }

        lista.forEach(function (item) {
            var fila = ''
                + '<tr>'
                + '  <td>' + (item.nombreCliente || '') + '</td>'
                + '  <td>' + (item.nombreEsteticista || '') + '</td>'
                + '  <td>' + (item.fecha_cita || '') + '</td>'
                + '  <td>' + (item.hora || '') + '</td>'
                + '  <td>' + (item.servicios || 'Sin servicios') + '</td>'
                + '  <td class="text-nowrap">'
                + '    <button type="button" class="btn btn-sm btn-warning me-1 btn-editar-cita" title="Modificar" data-id-cita="' + item.idCita + '"><i class="fas fa-edit"></i></button>'
                + '    <button type="button" class="btn btn-sm btn-danger btn-eliminar-cita" title="Eliminar" data-id-cita="' + item.idCita + '"><i class="fas fa-trash-alt"></i></button>'
                + '  </td>'
                + '</tr>';
            $tbody.append(fila);
        });
    }, function () {
        $('#resultadoconsulta').html('<tr><td colspan="6" class="text-center text-danger">No se pudo consultar las citas.</td></tr>');
    });
}

$(document).ready(function () {
    cargarCatalogos();
    consultar();

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
