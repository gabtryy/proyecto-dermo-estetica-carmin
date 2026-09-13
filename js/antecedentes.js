function enviaAjax({datos, done, fail, always, url = 'index.php?pagina=antecedentes', type = 'POST', dataType = 'json'}) {
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
        done: function(resp) {
            if (!resp?.ok) return;
            renderTabla(resp.data || []);
        }
    });
}

function cargarClientes() {
    var datos = new FormData();
    datos.append('accion', 'clientes');
    enviaAjax({
        datos: datos,
        done: function(resp) {
            if (!resp?.ok) return;
            var $select = $('#cedulaCliente');
            $select.empty();
            $select.append('<option value="">Seleccione un cliente</option>');
            (resp.data || []).forEach(function(item) {
                $select.append('<option value="' + (item.cedulaCliente || '') + '">' + (item.nombreCliente || item.cedulaCliente) + '</option>');
            });
        }
    });
}

function cargarTiposAntecedentes() {
    var datos = new FormData();
    datos.append('accion', 'tipos_antecedentes');
    enviaAjax({
        datos: datos,
        done: function(resp) {
            if (!resp?.ok) return;
            renderTiposAntecedentes(resp.data || []);
        }
    });
}

function renderTiposAntecedentes(lista) {
    var $contenedor = $('#listaTiposAntecedentes');
    $contenedor.empty();

    if (!lista.length) {
        $contenedor.append('<div class="text-muted">No hay tipos de antecedentes registrados.</div>');
        return;
    }

    lista.forEach(function(item) {
        var id = item.id_tipo_antecedente || '';
        var nombre = item.nom_tipo_antecedente || '';

        var html = `
            <div class="border rounded p-2 mb-2 bg-white">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <label class="d-flex align-items-center gap-2 mb-0">
                        <input type="checkbox" class="form-check-input tipo-check" value="${id}">
                        <span class="fw-semibold">${nombre}</span>
                    </label>
                </div>
                <textarea class="form-control descripcion-tipo" data-id="${id}" rows="2" placeholder="Descripción del antecedente para ${nombre}"></textarea>
            </div>
        `;

        $contenedor.append(html);
    });
}

function resetChecklist() {
    $('#formulario_antecedente')[0].reset();
    $('#listaTiposAntecedentes .tipo-check').prop('checked', false);
    $('#listaTiposAntecedentes .descripcion-tipo').val('');
    $('#cedulaCliente').val('');
    $('#accion').val('');
    $('#id_antecedente').val('');
}

function cargarAntecedentesCliente(cedulaCliente) {
    var datos = new FormData();
    datos.append('accion', 'antecedentesCliente');
    datos.append('cedulaCliente', cedulaCliente);
    enviaAjax({
        datos: datos,
        done: function(resp) {
            if (!resp?.ok) return;
            $('.tipo-check').prop('checked', false);
            $('.descripcion-tipo').val('');

            (resp.data || []).forEach(function(item) {
                var tipoId = item.id_tipo_antecedente;
                $('.tipo-check[value="' + tipoId + '"]').prop('checked', true);
                $('.descripcion-tipo[data-id="' + tipoId + '"]').val(item.descripcion_antecedente || '');
            });
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

    lista.forEach(function(item) {
        var fila = ''
            + '<tr>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-outline-primary me-1 btn-editar" title="Modificar" data-id-antecedente="' + (item.id_antecedente || '') + '" data-cedula-cliente="' + (item.cedulaCliente || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-id-antecedente="' + (item.id_antecedente || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '<td>' + (item.nombreCliente || '') + '</td>'
            + '<td>' + (item.nom_tipo_antecedente || '') + '</td>'
            + '<td>' + (item.descripcion_antecedente || '') + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

$(document).ready(function () {
    cargarClientes();
    cargarTiposAntecedentes();
    consultar();

    $('#incluir').on('click', function () {
        $('#proceso').text('INCLUIR');
        $('#accion').val('incluir');
        $('#id_antecedente').val('');
        resetChecklist();
        var $modal = $('#modal1');
        if (!$modal.length || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            return;
        }
        bootstrap.Modal.getOrCreateInstance($modal[0]).show();
    });

    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var idAntecedente = $(this).data('idAntecedente') || $(this).attr('data-id-antecedente');
        if (!idAntecedente) return;

        Swal.fire({
            title: '¿Eliminar antecedente?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('id_antecedente', idAntecedente);

                enviaAjax({
                    datos: datos,
                    done: function(resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Antecedente eliminado.', 'success');
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
        var idAntecedente = $(this).data('idAntecedente') || $(this).attr('data-id-antecedente');
        var cedulaCliente = $(this).data('cedulaCliente') || $(this).attr('data-cedula-cliente');
        if (!cedulaCliente) return;

        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');
        $('#id_antecedente').val(idAntecedente || '');
        $('#cedulaCliente').val(cedulaCliente);
        cargarAntecedentesCliente(cedulaCliente);

        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#formulario_antecedente').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        var cedulaCliente = $('#cedulaCliente').val();

        if (!cedulaCliente) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar un cliente.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        var tipos = [];
        var descripciones = [];
        $('.tipo-check:checked').each(function () {
            var tipo = $(this).val();
            var descripcion = $('.descripcion-tipo[data-id="' + tipo + '"]').val() || '';
            tipos.push(tipo);
            descripciones.push(descripcion);
        });

        if (!tipos.length) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar al menos un tipo de antecedente.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        var datos = new FormData(form);
        datos.delete('id_tipo_antecedente[]');
        datos.delete('descripcion_antecedente[]');
        datos.append('cedulaCliente', cedulaCliente);
        datos.append('accion', $('#accion').val() || 'incluir');

        tipos.forEach(function (tipo) {
            datos.append('id_tipo_antecedente[]', tipo);
        });

        descripciones.forEach(function (descripcion) {
            datos.append('descripcion_antecedente[]', descripcion);
        });

        enviaAjax({
            datos: datos,
            done: function (resp) {
                if (!resp || !resp.ok) {
                    alert((resp && resp.mensaje) || 'No se pudo guardar.');
                    return;
                }
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: resp.mensaje || 'Antecedentes guardados correctamente.',
                    confirmButtonText: 'Aceptar'
                });
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
                form.reset();
                consultar();
            },
            fail: function (xhr) {
                var msg = 'Error al guardar antecedentes.';
                if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                    msg = xhr.responseJSON.mensaje;
                } else if (xhr.responseText) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.mensaje) msg = json.mensaje;
                    } catch (e) {}
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonText: 'Aceptar'
                });
            }
        });
    });
});
