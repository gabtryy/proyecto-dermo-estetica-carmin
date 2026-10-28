// ==========================================
// 1. FUNCIONES PRINCIPALES Y AJAX
// ==========================================

function enviaAjax(datos) {
    $.ajax({
        async: true,
        url: '',
        type: 'POST',
        data: datos,
        contentType: false,
        processData: false,
        cache: false,
        timeout: 10000,
        success: function (respuesta) {
            var resp = respuesta;
            if (typeof resp === 'string') {
                try {
                    resp = JSON.parse(resp);
                } catch (e) {
                    alert('Error en JSON ' + e.name);
                    return;
                }
            }

            var accion = datos.get('accion') || resp.resultado || '';
            if (accion === 'obtienefecha') {
                $('#fechadenacimiento').val(resp.data || resp.mensaje || '');
            } else if (accion === 'consultar') {
                if (resp?.ok) {
                    renderTabla(resp.data || []);
                } else {
                    muestraMensaje(resp?.mensaje || 'No se pudo consultar.', 'error');
                }
            } else if (accion === 'incluir') {
                if (resp?.ok) {
                    muestraMensaje(resp.mensaje || 'Cliente guardado correctamente.', 'success');
                    $('#modal1').modal('hide');
                    consultar();
                } else {
                    muestraMensaje(resp?.mensaje || 'No se pudo guardar el cliente.', 'error');
                }
            } else if (accion === 'modificar') {
                if (resp?.ok) {
                    muestraMensaje(resp.mensaje || 'Cliente modificado correctamente.', 'success');
                    $('#modal1').modal('hide');
                    consultar();
                } else {
                    muestraMensaje(resp?.mensaje || 'No se pudo modificar el cliente.', 'error');
                }
            } else if (accion === 'eliminar') {
                if (resp?.ok) {
                    muestraMensaje(resp.mensaje || 'Cliente eliminado correctamente.', 'success');
                    $('#modal1').modal('hide');
                    consultar();
                } else {
                    muestraMensaje(resp?.mensaje || 'No se pudo eliminar el cliente.', 'error');
                }
            } else if (resp?.resultado === 'error' || resp?.ok === false) {
                muestraMensaje(resp?.mensaje || 'Error en la operación.', 'error');
            }
        },
        error: function (request, status, err) {
            if (status === 'timeout') {
                muestraMensaje('Servidor ocupado, intente de nuevo', 'error');
            } else {
                muestraMensaje('ERROR: <br/>' + status + ' ' + err, 'error');
            }
        }
    });
}

function pone_fecha() {
    var datos = new FormData();
    datos.append('accion', 'obtienefecha');
    enviaAjax(datos);
}

function consultar() {
    var datos = new FormData();
    datos.append('accion', 'consultar');
    enviaAjax(datos);
}

function renderTabla(lista) {
    var $tbody = $('#resultadoconsulta');
    $tbody.empty();

    if (!lista.length) {
        $tbody.append('<tr><td colspan="6" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var direccion = [item.estadoDirCliente, item.municipioDirCliente, item.parroquiaDirCliente]
            .filter(function (valor) { return valor !== null && valor !== undefined && valor !== ''; })
            .join(' / ');
        var telefono = item.telefonoCliente || item.telefono || '';

        var fila = ''
            + '<tr>'
            + '<td>' + (item.cedulaCliente || '') + '</td>'
            + '<td>' + (item.nombreCliente || '') + '</td>'
            + '<td>' + (item.fechaNacimiento || '') + '</td>'
            + '<td>' + direccion + '</td>'
            + '<td>' + telefono + '</td>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-purple me-1 btn-editar" title="Modificar" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-trash-alt"></i></button>'
            +   '<button class="btn btn-sm btn-info ms-1 btn-detalle" title="Detalle" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-info-circle"></i></button>'
            + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

function validarCampo($campo, $mensaje, etiqueta, formato, expresion) {
    var valor = String($campo.val() || '').trim();
    var error = '';

    if (!valor) {
        error = etiqueta + ' es obligatorio.';
    } else if (expresion && !expresion.test(valor)) {
        error = formato;
    }

    $mensaje.text(error);
    $campo.toggleClass('is-invalid', Boolean(error));
    return !error;
}

function validarCedula() {
    return validarCampo(
        $('#cedula'),
        $('#scedula'),
        'La cédula',
        'La cédula debe contener solo números y tener 7 u 8 dígitos.',
        /^\d{7,8}$/
    );
}

function validarNombre() {
    return validarCampo(
        $('#nombres'),
        $('#snombres'),
        'El nombre completo',
        'Use entre 3 y 150 letras; puede incluir espacios, apóstrofes o guiones.',
        /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'-]{3,150}$/
    );
}

function validarFechaNacimiento() {
    var $fecha = $('#fechadenacimiento');
    var fecha = String($fecha.val() || '').trim();
    var $mensaje = $('#sfechadenacimiento');
    var error = '';

    if (!fecha) {
        error = 'La fecha de nacimiento es obligatoria.';
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
        error = 'Ingrese una fecha válida.';
    } else {
        var partes = fecha.split('-').map(Number);
        var fechaIngresada = new Date(partes[0], partes[1] - 1, partes[2]);
        var fechaValida = fechaIngresada.getFullYear() === partes[0]
            && fechaIngresada.getMonth() === partes[1] - 1
            && fechaIngresada.getDate() === partes[2];
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        if (!fechaValida) {
            error = 'Ingrese una fecha válida.';
        } else if (fechaIngresada < new Date(1950, 0, 1)) {
            error = 'La fecha debe ser igual o posterior al 01/01/1950.';
        } else if (fechaIngresada > hoy) {
            error = 'La fecha de nacimiento no puede ser futura.';
        }
    }

    $mensaje.text(error);
    $fecha.toggleClass('is-invalid', Boolean(error));
    return !error;
}

function validarEstado() {
    return validarCampo(
        $('#estado'),
        $('#sestado'),
        'El estado',
        'Use entre 3 y 50 letras.',
        /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'-]{3,50}$/
    );
}

function validarMunicipio() {
    return validarCampo(
        $('#municipio'),
        $('#smunicipio'),
        'El municipio',
        'Use entre 3 y 50 letras.',
        /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'-]{3,50}$/
    );
}

function validarParroquia() {
    return validarCampo(
        $('#parroquia'),
        $('#sparroquia'),
        'La parroquia',
        'Use entre 3 y 50 letras.',
        /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'-]{3,50}$/
    );
}

function validarTelefono() {
    return validarCampo(
        $('#telefono'),
        $('#stelefono'),
        'El teléfono',
        'El teléfono debe contener solo números.',
        /^\d+$/
    );
}

function limpiarErrores() {
    $('#formulario_cliente .is-invalid').removeClass('is-invalid');
    $('#scedula, #snombres, #sfechadenacimiento, #sestado, #smunicipio, #sparroquia, #stelefono').text('');
}

function validarFormularioCliente() {
    var cedulaValida = validarCedula();
    var nombreValido = validarNombre();
    var fechaValida = validarFechaNacimiento();
    var estadoValido = validarEstado();
    var municipioValido = validarMunicipio();
    var parroquiaValida = validarParroquia();
    var telefonoValido = validarTelefono();

    return cedulaValida
        && nombreValido
        && fechaValida
        && estadoValido
        && municipioValido
        && parroquiaValida
        && telefonoValido;
}

function limpia() {
    pone_fecha();
    $("#cedula").val("").prop("readonly", false);
    $("#nombres").val("");
    $("#estado").val("");
    $("#municipio").val("");
    $("#parroquia").val("");
    $("#telefono").val("");
    limpiarErrores();
}

function muestraMensaje(mensaje, icon = 'warning') {
    Swal.fire({
        icon: icon,
        html: mensaje,
        timer: 2000,
        timerProgressBar: false,
        showConfirmButton: true,
        position: 'center'
    });
}


// 2. EVENTOS Y DELEGACIONES (DOCUMENT READY)


$(document).ready(function () {
    pone_fecha();
    consultar();

    $('#cedula').on('input', validarCedula);
    $('#nombres').on('input', validarNombre);
    $('#fechadenacimiento').on('input change', validarFechaNacimiento);
    $('#estado').on('input', validarEstado);
    $('#municipio').on('input', validarMunicipio);
    $('#parroquia').on('input', validarParroquia);
    $('#telefono').on('input', validarTelefono);

    $('#incluir').on('click', function () {
        limpia();
        $('#proceso').text('REGISTRAR');
        $('#accion').val('incluir');
        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

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
        }).then(function (result) {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('cedula', cedula);
                enviaAjax(datos);
            }
        });
    });

    $('#resultadoconsulta').on('click', '.btn-editar', function () {
        var cedula = $(this).data('cedula');
        if (!cedula) return;

        var $fila = $(this).closest('tr');
        var nombre = $fila.find('td').eq(1).text();
        var fechaNacimiento = $fila.find('td').eq(2).text();
        var direccion = ($fila.find('td').eq(3).text() || '').split(' / ');
        var estado = direccion[0] || '';
        var municipio = direccion[1] || '';
        var parroquia = direccion[2] || '';
        var telefono = $fila.find('td').eq(4).text();

        $('#cedula').val(cedula).prop('readonly', true);
        $('#nombres').val(nombre);
        $('#fechadenacimiento').val(fechaNacimiento);
        $('#estado').val(estado);
        $('#municipio').val(municipio);
        $('#parroquia').val(parroquia);
        $('#telefono').val(telefono);
        limpiarErrores();

        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');

        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#formulario_cliente').on('submit', function (e) {
        e.preventDefault();
        var accion = $('#accion').val();
        if (accion === 'incluir' || accion === 'modificar') {
            if (!validarFormularioCliente()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incorrectos',
                    text: 'Corrige los campos marcados antes de continuar.'
                });
                return;
            }

            var datos = new FormData();
            datos.append('accion', accion);
            datos.append('accion', accion);
            datos.append('cedula', $('#cedula').val());
            datos.append('nombres', $('#nombres').val());
            datos.append('fechadenacimiento', $('#fechadenacimiento').val());
            datos.append('estado', $('#estado').val());
            datos.append('municipio', $('#municipio').val());
            datos.append('parroquia', $('#parroquia').val());
            datos.append('telefono', telefono);
            enviaAjax(datos);
        } else if (accion === 'eliminar') {
            var datos = new FormData();
            datos.append('accion', 'eliminar');
            datos.append('cedula', $('#cedula').val());
            enviaAjax(datos);
        }
    });
});
