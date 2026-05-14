// Función centralizada para enviar AJAX
function enviaAjax({datos, done, fail, always, url = 'index.php?c=servicios', type = 'POST', dataType = 'json'}) {
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
        $tbody.append('<tr><td colspan="5" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var fila = ''
            + '<tr>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-outline-celeste me-1 btn-editar" title="Modificar" data-id_servicio="' + (item.id_servicio || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-id_servicio="' + (item.id_servicio || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '<td>' + (item.id_servicio || '') + '</td>'
            + '<td>' + (item.nombre_servicio || '') + '</td>'
            + '<td>' + (item.precio || '') + '</td>'
            + '<td>' + (item.descripcion || '') + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

$(document).ready(function () {
    
    // Delegación para botón eliminar
    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var id_servicio = $(this).data('id_servicio');
        if (!id_servicio) return;

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
                datos.append('id_servicio', id_servicio);

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

    // Delegación para botón editar
$('#resultadoconsulta').on('click', '.btn-editar', function () {
    var id_servicio = $(this).data('id_servicio');
    if (!id_servicio) return;

    // Buscar la fila y extraer los datos
    var $fila = $(this).closest('tr');
    var nombre_servicio = $fila.find('td:eq(2)').text().trim();
    var precio = $fila.find('td:eq(3)').text().trim();
    var descripcion = $fila.find('td:eq(4)').text().trim();
    // Llenar el formulario
    $('#id_servicio').val(id_servicio).prop('readonly', true);
    $('#nombre_servicio').val(nombre_servicio);
    $('#precio').val(precio);
    $('#descripcion').val(descripcion);
    $('#proceso').text('MODIFICAR');
    $('#accion').val('modificar');
    var $modal = $('#modal1');
    if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance($modal[0]).show();
    }
});

    // Botón Incluir (Abrir modal)
    $(document).ready(function () {
        $('#incluir').on('click', function () {
            $('#proceso').text('INCLUIR');
            $('#accion').val('incluir');
            var $modal = $('#modal1');
            if (!$modal.length || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
                return;
            }
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        });

        // Cambiar a submit y validar campos vacíos
        $('#formulario_servicio').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            var datos = new FormData(form);

            
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
                        text: resp.mensaje || 'Servicio guardado correctamente.',
                        confirmButtonText: 'Aceptar'
                    });
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
                    form.reset();
                    consultar();
                },
                fail: function (xhr) {
                    console.log('Respuesta AJAX fallida:', xhr.responseText);
                    var msg = 'Error al guardar servicio.';
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

});