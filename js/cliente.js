// Función centralizada para enviar AJAX
function enviaAjax({datos, done, fail, always, url = 'index.php?c=clientes', type = 'POST', dataType = 'json'}) {
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
        $tbody.append('<tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var fila = ''
            + '<tr>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-outline-celeste me-1 btn-editar" title="Modificar" data-cedula="' + (item.cedula_cliente || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-cedula="' + (item.cedula_cliente || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '<td>' + (item.cedula_cliente || '') + '</td>'
            + '<td>' + (item.nombre_cliente || '') + '</td>'
            + '<td>' + (item.telefono_cliente || '') + '</td>'
            + '<td>' + (item.direccion_cliente || '') + '</td>'
            + '<td>' + (item.fecha_nacimiento || '') + '</td>'
            + '<td>' + (item.genero || '') + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

$(document).ready(function () {
    
    // Delegación para botón eliminar
    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var cedula = $(this).data('cedula');
        if (!cedula) return;

        Swal.fire({
            title: '¿Eliminar cliente?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('cedula', cedula);

                enviaAjax({
                    datos: datos,
                    done: function (resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Cliente eliminado.', 'success');
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
    var cedula = $(this).data('cedula');
    if (!cedula) return;

    // Buscar la fila y extraer los datos
    var $fila = $(this).closest('tr');
    var nombre = $fila.find('td').eq(2).text();
    var telefono = $fila.find('td').eq(3).text();
    var direccion = $fila.find('td').eq(4).text();
    var fecha = $fila.find('td').eq(5).text();
    var genero = $fila.find('td').eq(6).text();

    // Llenar el formulario
    $('#cedula').val(cedula).prop('readonly', true);
    $('#nombres').val(nombre);
    $('#telefono').val(telefono);
    $('#direccion').val(direccion);
    $('#fechadenacimiento').val(fecha);
    if (genero === 'M') {
        $('#masculino').prop('checked', true);
    } else if (genero === 'F') {
        $('#femenino').prop('checked', true);
    } else {
        $('input[name=\"sexo\"]').prop('checked', false);
    }

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
        $('#formulario_cliente').on('submit', function (e) {
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
                        text: resp.mensaje || 'Cliente guardado correctamente.',
                        confirmButtonText: 'Aceptar'
                    });
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
                    form.reset();
                    consultar();
                },
                fail: function (xhr) {
                    console.log('Respuesta AJAX fallida:', xhr.responseText);
                    var msg = 'Error al guardar cliente.';
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