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
    datos.append('accion');
    
    enviaAjax({
        datos: datos,
        done: function(resp) {
            var fecha = resp.data || resp.mensaje || "";
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
            +   '<button class="btn btn-sm btn-outline-primary me-1 btn-editar" title="Modificar" data-idProducto="' + (item.idProducto || '') + '" data-idProveedor="' + (item.idProveedor || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-idProducto="' + (item.idProducto || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
                + '<td>' + (item.nombreProducto || '') + '</td>'
                + '<td>' + (item.marca || '') + '</td>'
                + '<td>' + (item.precioProducto || '') + '</td>'
                + '<td>' + (item.nombreProveedor || item.idProveedor || '') + '</td>'
                + '<td>' + (item.cantidadActual || '') + '</td>'
                + '<td>' + (item.tipoProducto || '') + '</td>'
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

    if (validarkeyup(/^[A-Za-z0-9\s\u00f1\u00d1\u00C0-\u017F\-\_\.\,\'\"()\&\/ºª%#\+\?\¡\!\:\;\@]{3,150}$/, $("#nombreProducto"), $("#snombreProducto"), "Solo caracteres válidos entre 3 y 150 caracteres") == 0) {
        Swal.fire('Atención', 'Nombre del producto: Solo caracteres válidos entre 3 y 150 caracteres', 'warning');
        return false;
    } else if (validarkeyup(/^[A-Za-z0-9\s\u00f1\u00d1\u00C0-\u017F\-\_\.\,\'\"()\&\/ºª%#\+\?\¡\!\:\;\@]{3,150}$/, $("#marca"), $("#smarca"), "Solo caracteres válidos entre 3 y 150 caracteres") == 0) {
        Swal.fire('Atención', 'Marca: Solo caracteres válidos entre 3 y 150 caracteres', 'warning');
        return false;
    } else if (validarkeyup(/^[0-9]+([.,][0-9]{1,2})?$/, $("#precioProducto"), $("#sprecioProducto"), "Ingrese un precio válido 1.00") == 0) {
        Swal.fire('Atención', 'Precio del producto: Ingrese un precio válido 1.00', 'warning');
        return false;
    } else if (validarkeyup(/^[1-9][0-9]?$|^100$/, $("#cantidadActual"), $("#scantidadActual"), "Ingrese una cantidad válida entre 1 y 100") == 0) {
        Swal.fire('Atención', 'Cantidad actual: Ingrese una cantidad válida entre 1 y 100', 'warning');
        return false;
    } else if (validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,50}$/, $("#tipoProducto"), $("#stipoProducto"), "Solo letras entre 3 y 50 caracteres") == 0) {
        Swal.fire('Atención', 'Tipo de producto: Solo letras entre 3 y 50 caracteres', 'warning');
        return false;
    } 
    return true;
}

function limpia() {
    $("#idProducto").val("").prop("readonly", false);
    $("#nombreProducto").val("");
    $("#marca").val("");
    $("#idProveedor").val("");
    $("#precioProducto").val("");
    $("#cantidadActual").val("");
    $("#tipoProducto").val("");
    $("#snombreProducto").text("");
    $("#smarca").text("");
    $("#sprecioProducto").text("");
    $("#scantidadActual").text("");
    $("#stipoProducto").text("");
}

// ==========================================
// 3. EVENTOS Y DELEGACIONES (DOCUMENT READY)
// ==========================================

$(document).ready(function () {
    console.log('productos.js: document ready');
    consultar();

    // Cargar proveedores para el select
    (function cargarProveedores(){
        var datos = new FormData();
        datos.append('accion', 'proveedores');
        enviaAjax({
            datos: datos,
            done: function(resp){
                if (!resp?.ok) return;
                var $sel = $('#idProveedor');
                if (!$sel.length) return;
                $sel.empty();
                $sel.append('<option value="">Seleccione proveedor</option>');
                (resp.data || []).forEach(function(p){
                    $sel.append('<option value="'+ (p.idProveedor || '') +'">'+ (p.nombreProveedor || p.idProveedor) +'</option>');
                });
            }
        });
    })();

    $("#nombreProducto").on("keypress", function (e) { validarkeypress(/^[A-Za-z0-9\s\u00f1\u00d1\u00C0-\u017F\-\_\.\,\'\"()\&\/ºª%#\+\?\¡\!\:\;\@]*$/, e); });
    $("#nombreProducto").on("keyup", function () { validarkeyup(/^[A-Za-z0-9\s\u00f1\u00d1\u00C0-\u017F\-\_\.\,\'\"()\&\/ºª%#\+\?\¡\!\:\;\@]{3,150}$/, $(this), $("#snombreProducto"), "Solo caracteres válidos entre 3 y 150 caracteres"); });
    
    $("#marca").on("keypress", function (e) { validarkeypress(/^[A-Za-z0-9\s\u00f1\u00d1\u00C0-\u017F\-\_\.\,\'\"()\&\/ºª%#\+\?\¡\!\:\;\@]*$/, e); });
    $("#marca").on("keyup", function () { validarkeyup(/^[A-Za-z0-9\s\u00f1\u00d1\u00C0-\u017F\-\_\.\,\'\"()\&\/ºª%#\+\?\¡\!\:\;\@]{3,150}$/, $(this), $("#smarca"), "Solo caracteres válidos entre 3 y 150 caracteres"); });

    $("#precioProducto").on("keypress", function (e) { validarkeypress(/^[0-9.,]*$/, e); });
    $("#precioProducto").on("keyup", function () { validarkeyup(/^[0-9]+([.,][0-9]{1,2})?$/, $(this), $("#sprecioProducto"), "Ingrese un precio válido 1.00"); });

    $("#cantidadActual").on("keypress", function (e) { validarkeypress(/^[0-9]*$/, e); });
    $("#cantidadActual").on("keyup", function () { validarkeyup(/^[1-9][0-9]?$|^100$/, $(this), $("#scantidadActual"), "Ingrese una cantidad válida entre 1 y 100"); });

    $("#tipoProducto").on("keypress", function (e) { validarkeypress(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]*$/, e); });
    $("#tipoProducto").on("keyup", function () { validarkeyup(/^[A-Za-z\b\s\u00f1\u00d1\u00E0-\u00FC]{3,50}$/, $(this), $("#stipoProducto"), "Solo letras entre 3 y 50 caracteres"); });

    $('#incluir').on('click', function () {
        console.log('productos.js: incluir clicked');
        limpia();
        $('#proceso').text('INCLUIR');
        $('#accion').val('incluir');
        var $modal = $('#modal1');
        if (!$modal.length) $modal = $('#mostrarmodal');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var idProducto = $(this).data('idproducto');
        if (!idProducto) return;

        Swal.fire({
            title: '¿Eliminar producto?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('idProducto', idProducto);

                enviaAjax({
                    datos: datos,
                    done: function (resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Producto eliminado correctamente.', 'success');
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
        var idProducto = $(this).data('idproducto');
        if (!idProducto) return;

        var idProveedor = $(this).data('idproveedor') || '';
        var $fila = $(this).closest('tr');
        var nombreProducto = $fila.find('td').eq(1).text();
        var marca = $fila.find('td').eq(2).text();
        var precioProducto = $fila.find('td').eq(3).text();
        var cantidadActual = $fila.find('td').eq(5).text();
        var tipoProducto = $fila.find('td').eq(6).text();

        $('#idProducto').val(idProducto).prop('readonly', true);
        $('#nombreProducto').val(nombreProducto);
        $('#marca').val(marca);
        $('#idProveedor').val(idProveedor);
        $('#precioProducto').val(precioProducto);
        $('#cantidadActual').val(cantidadActual);
        $('#tipoProducto').val(tipoProducto);

        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');

        var $modal = $('#modal1');
        if (!$modal.length) $modal = $('#mostrarmodal');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#formulario_producto').on('submit', function (e) {
        e.preventDefault();
        if (!validarenvio()) {
            return;
        }

        var datos = new FormData(this);
        enviaAjax({
            datos: datos,
            done: function (resp) {
                if (!resp?.ok) {
                    Swal.fire('Error', resp?.mensaje || 'No se pudo guardar el producto.', 'error');
                    return;
                }
                Swal.fire('Éxito', resp.mensaje || 'Producto guardado correctamente.', 'success');
                var modalEl = document.getElementById('modal1');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                }
                $('#formulario_producto')[0].reset();
                limpia();
                consultar();
            },
            fail: function (xhr) {
                var msg = 'Error al guardar el producto.';
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
