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

function consultar() {
    var datos = new FormData();
    datos.append('accion', 'consultar');
    enviaAjax({
        datos: datos,
        done: function (resp) {
            if (!resp?.ok) return;
            renderTabla(resp.data || []);
        }
    });
}

function renderTabla(lista) {
    var $tbody = $('#resultadoconsulta');
    $tbody.empty();

    if (!lista.length) {
        $tbody.append('<tr><td colspan="4" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var $fila = $('<tr>');
        $fila.append($('<td>').text(item.nombreServicio || ''));
        $fila.append($('<td>').text(item.precio || ''));
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
        $tbody.append($fila);
    });
}

$(document).ready(function () {
    var currentEditId = null;

    function validarCampoServicio(campo) {
        var $campo = $('#' + campo);
        var valor = String($campo.val() || '').trim();
        var error = '';

        if (campo === 'nombreServicio' && (Array.from(valor).length < 3 || Array.from(valor).length > 100)) {
            error = 'El nombre del servicio debe tener entre 3 y 100 caracteres.';
        } else if (campo === 'precio') {
            var precio = Number(valor);
            if (!/^\d{1,8}(?:\.\d{1,2})?$/.test(valor) || !Number.isFinite(precio) || precio <= 0) {
                error = 'El precio debe ser mayor que cero y tener como máximo dos decimales.';
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

    $('#formulario_servicio').on('input change', 'input:not([type="hidden"]), textarea', function () {
        if (this.id === 'nombreServicio' || this.id === 'precio') {
            validarCampoServicio(this.id);
        }
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
        $('#nombreServicio').val($fila.find('td:eq(0)').text().trim());
        $('#precio').val($fila.find('td:eq(1)').text().trim());
        $('#descripcion').val($fila.find('td:eq(2)').text().trim());
        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).show();
    });

    $('#incluir').on('click', function () {
        currentEditId = null;
        $('#formulario_servicio')[0].reset();
        $('#proceso').text('REGISTRAR');
        $('#accion').val('incluir');
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

    consultar();
});