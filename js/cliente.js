// ==========================================
// 1. FUNCIONES PRINCIPALES Y AJAX
// ==========================================

function enviaAjax({datos, done, fail, always, url = '', type = 'POST', dataType = 'json'}) {
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

function pone_fecha() {
    var datos = new FormData();
    datos.append('accion', 'obtienefecha');
    
    enviaAjax({
        datos: datos,
        done: function(resp) {
            var fecha = resp.data || resp.mensaje || "";
            $("#fechadenacimiento").val(fecha);
        }
    });
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
            +   '<button class="btn btn-sm btn-outline-celeste me-1 btn-editar" title="Modificar" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-cedula="' + (item.cedulaCliente || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '<td>' + (item.cedulaCliente || '') + '</td>'
            + '<td>' + (item.nombreCliente || '') + '</td>'
            + '<td>' + (item.fechaNacimiento || '') + '</td>'
            + '<td>' + (item.estadoDirCliente || '') + '</td>'
            + '<td>' + (item.municipioDirCliente || '') + '</td>'
            + '<td>' + (item.parroquiaDirCliente || '') + '</td>'
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
        Swal.fire('Atención', 'La cédula debe coincidir con el formato 9999999', 'warning');
        return false;
    } else if (validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,150}$/, $("#nombres"), $("#snombres"), "Solo letras entre 3 y 150 caracteres") == 0) {
        Swal.fire('Atención', 'Nombres: Solo letras entre 3 y 150 caracteres', 'warning');
        return false;
    } else if (validarkeyup(/^(?:(?:1[6-9]|[2-9]\d)?\d{2})(?:(?:(\/|-|\.)(?:0?[13578]|1[02])\1(?:31))|(?:(\/|-|\.)(?:0?[13-9]|1[0-2])\2(?:29|30)))$|^(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\/|-|\.)0?2\3(?:29)$|^(?:(?:1[6-9]|[2-9]\d)?\d{2})(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:0?[1-9]|1\d|2[0-8])$/, $("#fechadenacimiento"), $("#sfechadenacimiento"), "Ingrese una fecha válida") == 0) {
        Swal.fire('Atención', 'Ingrese una fecha de nacimiento válida', 'warning');
        return false;
    } else if (validarkeyup(regexTexto, $("#estado"), $("#sestado"), "Solo letras entre 3 y 50 caracteres") == 0) {
        Swal.fire('Atención', 'Estado: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } else if (validarkeyup(regexTexto, $("#municipio"), $("#smunicipio"), "Solo letras entre 3 y 50 caracteres") == 0) {
        Swal.fire('Atención', 'Municipio: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } else if (validarkeyup(regexTexto, $("#parroquia"), $("#sparroquia"), "Solo letras entre 3 y 50 caracteres") == 0) {
        Swal.fire('Atención', 'Parroquia: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } else {
        var f1 = new Date(1950, 0, 1);
        var f2 = new Date($("#fechadenacimiento").val());

        if (f2 < f1) {
            Swal.fire('Atención', 'La fecha de nacimiento debe ser mayor o igual a 01/01/1950', 'warning');
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

// ==========================================
// 3. EVENTOS Y DELEGACIONES (DOCUMENT READY)
// ==========================================

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

                enviaAjax({
                    datos: datos,
                    done: function (resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Cliente eliminado correctamente.', 'success');
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

    $('#formulario_cliente').on('submit', function (e) {
        e.preventDefault();
        if (!validarenvio()) {
            return;
        }

        var datos = new FormData(this);
        enviaAjax({
            datos: datos,
            done: function (resp) {
                if (!resp?.ok) {
                    Swal.fire('Error', resp?.mensaje || 'No se pudo guardar el cliente.', 'error');
                    return;
                }
                Swal.fire('Éxito', resp.mensaje || 'Cliente guardado correctamente.', 'success');
                var modalEl = document.getElementById('modal1');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                }
                $('#formulario_cliente')[0].reset();
                limpia();
                consultar();
            },
            fail: function (xhr) {
                var msg = 'Error al guardar el cliente.';
                if (xhr?.responseJSON?.mensaje) {
                    msg = xhr.responseJSON.mensaje;
                } else if (xhr?.responseText) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.mensaje) msg = json.mensaje;
                    } catch (e) {}
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
