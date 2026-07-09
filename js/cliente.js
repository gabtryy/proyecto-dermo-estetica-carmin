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
        $tbody.append('<tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var fila = ''
            + '<tr>'
            + '<td>' + (item.cedulaCliente || '') + '</td>'
            + '<td>' + (item.nombreCliente || '') + '</td>'
            + '<td>' + (item.fechaNacimiento || '') + '</td>'
            + '<td>' + (item.estadoDirCliente || '') + '</td>'
            + '<td>' + (item.municipioDirCliente || '') + '</td>'
            + '<td>' + (item.parroquiaDirCliente || '') + '</td>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-outline-primary me-1 btn-editar" title="Modificar" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

// ==========================================
// 2. FUNCIONES DE VALIDACIÓN
// ==========================================

function validarkeypress(er, e) {
    let key = e.keyCode || e.which;
    let tecla = String.fromCharCode(key);
    if (!er.test(tecla)) {
        e.preventDefault();
    }
}

function validarkeyup(er, $etiqueta, $etiquetamensaje, mensaje) {
    let a = er.test($etiqueta.val());
    if (a) {
        $etiquetamensaje.text("");
        return 1;
    } else {
        $etiquetamensaje.text(mensaje).css("color", "red");
        return 0;
    }
}

function validarenvio() {
    let regexTexto = /^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,50}$/;

    if (validarkeyup(/^[0-9]{7,8}$/, $("#cedula"), $("#scedula"), "El formato debe ser 9999999") == 0) {
        muestraMensaje('La cédula debe coincidir con el formato 9999999', 'warning');
        return false;
    } else if (validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,150}$/, $("#nombres"), $("#snombres"), "Solo letras entre 3 y 150 caracteres") == 0) {
        muestraMensaje('Nombres: Solo letras entre 3 y 150 caracteres', 'warning');
        return false;
    } else if (validarkeyup(/^(?:(?:1[6-9]|[2-9]\d)?\d{2})(?:(?:(\/|-|\.)(?:0?[13578]|1[02])\1(?:31))|(?:(\/|-|\.)(?:0?[13-9]|1[0-2])\2(?:29|30)))$|^(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\/|-|\.)0?2\3(?:29)$|^(?:(?:1[6-9]|[2-9]\d)?\d{2})(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:0?[1-9]|1\d|2[0-8])$/, $("#fechadenacimiento"), $("#sfechadenacimiento"), "Ingrese una fecha válida") == 0) {
        muestraMensaje('Ingrese una fecha de nacimiento válida', 'warning');
        return false;
    } else if (validarkeyup(regexTexto, $("#estado"), $("#sestado"), "Solo letras entre 3 y 50 caracteres") == 0) {
        muestraMensaje('Estado: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } else if (validarkeyup(regexTexto, $("#municipio"), $("#smunicipio"), "Solo letras entre 3 y 50 caracteres") == 0) {
        muestraMensaje('Municipio: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } else if (validarkeyup(regexTexto, $("#parroquia"), $("#sparroquia"), "Solo letras entre 3 y 50 caracteres") == 0) {
        muestraMensaje('Parroquia: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } else {
        var f1 = new Date(1950, 0, 1);
        var f2 = new Date($("#fechadenacimiento").val());

        if (f2 < f1) {
            muestraMensaje('La fecha de nacimiento debe ser mayor o igual a 01/01/1950', 'warning');
            return false;
        }
    }
    return true;
}

function limpia() {
    pone_fecha();
    $("#cedula").val("").prop("readonly", false);
    $("#nombres").val("");
    $("#estado").val("");
    $("#municipio").val("");
    $("#parroquia").val("");
    $("#scedula, #snombres, #sfechadenacimiento, #sestado, #smunicipio, #sparroquia").text("");
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


// 3. EVENTOS Y DELEGACIONES (DOCUMENT READY)


$(document).ready(function () {
    pone_fecha();
    consultar();

    $("#cedula").on("keypress", function (e) { validarkeypress(/^[0-9\b]*$/, e); });
    $("#cedula").on("keyup", function () { validarkeyup(/^[0-9]{7,8}$/, $(this), $("#scedula"), "El formato debe ser 9999999"); });

    $("#nombres, #estado, #municipio, #parroquia").on("keypress", function (e) { validarkeypress(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]*$/, e); });
    $("#nombres").on("keyup", function () { validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,150}$/, $(this), $("#snombres"), "Solo letras entre 3 y 150 caracteres"); });
    $("#estado").on("keyup", function () { validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,50}$/, $(this), $("#sestado"), "Solo letras entre 3 y 50 caracteres"); });
    $("#municipio").on("keyup", function () { validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,50}$/, $(this), $("#smunicipio"), "Solo letras entre 3 y 50 caracteres"); });
    $("#parroquia").on("keyup", function () { validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,50}$/, $(this), $("#sparroquia"), "Solo letras entre 3 y 50 caracteres"); });

    $('#incluir').on('click', function () {
        limpia();
        $('#proceso').text('INCLUIR');
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
        var nombre = $fila.find('td').eq(2).text();
        var fechaNacimiento = $fila.find('td').eq(3).text();
        var estado = $fila.find('td').eq(4).text();
        var municipio = $fila.find('td').eq(5).text();
        var parroquia = $fila.find('td').eq(6).text();

        $('#cedula').val(cedula).prop('readonly', true);
        $('#nombres').val(nombre);
        $('#fechadenacimiento').val(fechaNacimiento);
        $('#estado').val(estado);
        $('#municipio').val(municipio);
        $('#parroquia').val(parroquia);

        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');

        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#proceso').on('click', function (e) {
        e.preventDefault();
        var texto = $(this).text();
        if (texto === 'INCLUIR' || texto === 'MODIFICAR') {
            if (!validarenvio()) {
                return;
            }
            var datos = new FormData();
            datos.append('accion', texto === 'INCLUIR' ? 'incluir' : 'modificar');
            datos.append('cedula', $('#cedula').val());
            datos.append('nombres', $('#nombres').val());
            datos.append('fechadenacimiento', $('#fechadenacimiento').val());
            datos.append('estado', $('#estado').val());
            datos.append('municipio', $('#municipio').val());
            datos.append('parroquia', $('#parroquia').val());
            enviaAjax(datos);
        } else if (texto === 'ELIMINAR') {
            if (validarkeyup(/^[0-9]{7,8}$/, $('#cedula'), $('#scedula'), 'El formato debe ser 9999999') == 0) {
                muestraMensaje('La cédula debe coincidir con el formato 9999999', 'error');
            } else {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('cedula', $('#cedula').val());
                enviaAjax(datos);
            }
        }
    });
});
