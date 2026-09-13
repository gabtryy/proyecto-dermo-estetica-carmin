// Función centralizada para enviar AJAX
function enviaAjax({datos, done, fail, always, url = 'index.php?pagina=proveedores', type = 'POST', dataType = 'json'}) {
    $.ajax({
        url: url,
        type: type,
        data: datos,
        processData: false,
        contentType: false,
        dataType: dataType
    })
    .done(done || function() {})
    .fail(fail || function() {})
    .always(always || function() {});
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
        $tbody.append('<tr><td colspan="8" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var fila = ''
            + '<tr>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-outline-primary me-1 btn-editar" title="Modificar" data-id-proveedor="' + (item.idProveedor || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-id-proveedor="' + (item.idProveedor || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '<td>' + (item.rif || '') + '</td>'
            + '<td>' + (item.nombreProveedor || '') + '</td>'
            + '<td>' + (item.estadoDirProveedor || '') + '</td>'
            + '<td>' + (item.municipioDirProveedor || '') + '</td>'
            + '<td>' + (item.parroquiaDirProveedor || '') + '</td>'
            + '<td>' + (item.telefono || '') + '</td>'
            + '<td>' + (item.correo || '') + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

$(document).ready(function () {
    var currentEditId = null;

    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var idProveedor = $(this).data('idProveedor') || $(this).attr('data-id-proveedor');
        if (!idProveedor) return;

        Swal.fire({
            title: '¿Eliminar proveedor?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('idProveedor', idProveedor);

                enviaAjax({
                    datos: datos,
                    done: function (resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Proveedor eliminado.', 'success');
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
        var idProveedor = $(this).data('idProveedor') || $(this).attr('data-id-proveedor');
        if (!idProveedor) return;

        var $fila = $(this).closest('tr');
        var rif = $fila.find('td:eq(1)').text().trim();
        var nombreProveedor = $fila.find('td:eq(2)').text().trim();
        var estado = $fila.find('td:eq(3)').text().trim();
        var municipio = $fila.find('td:eq(4)').text().trim();
        var parroquia = $fila.find('td:eq(5)').text().trim();
        var telefono = $fila.find('td:eq(6)').text().trim();
        var correo = $fila.find('td:eq(7)').text().trim();

        currentEditId = idProveedor;
        $('#idProveedor').val(idProveedor);
        $('#rif').val(rif);
        $('#nombreProveedor').val(nombreProveedor);
        $('#estadoDirProveedor').val(estado);
        $('#municipioDirProveedor').val(municipio);
        $('#parroquiaDirProveedor').val(parroquia);
        $('#telefono').val(telefono);
        $('#correo').val(correo);

        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');

        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#incluir').on('click', function () {
        $('#proceso').text('INCLUIR');
        $('#accion').val('incluir');
        $('#formulario_proveedor')[0].reset();
        currentEditId = null;
        $('#idProveedor').val('');

        var $modal = $('#modal1');
        if (!$modal.length || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            return;
        }
        bootstrap.Modal.getOrCreateInstance($modal[0]).show();
    });

    $('#formulario_proveedor').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        var datos = new FormData(form);

        if ($('#accion').val() === 'modificar' && currentEditId) {
            datos.append('idProveedor', currentEditId);
        }

        var vacio = true;
        for (var pair of datos.entries()) {
            var key = pair[0];
            var value = pair[1];
            if (key !== 'accion' && key !== 'proceso' && value && value.toString().trim() !== '') {
                vacio = false;
                break;
            }
        }

        if (vacio) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El formulario está vacío. Por favor, complete al menos un campo.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

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
                    text: resp.mensaje || 'Proveedor guardado correctamente.',
                    confirmButtonText: 'Aceptar'
                });
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
                form.reset();
                currentEditId = null;
                consultar();
            },
            fail: function (xhr) {
                console.log('Respuesta AJAX fallida:', xhr.responseText);
                var msg = 'Error al guardar proveedor.';
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

    consultar();
});
