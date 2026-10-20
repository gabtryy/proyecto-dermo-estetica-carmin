$(function () {
    var productos = {};
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1'));

    function enviaAjax(datos, done, fail) {
        return $.ajax({
            url: 'index.php?pagina=productos',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(done).fail(fail || function () {});
    }

    function mostrarError(xhr, mensajePredeterminado) {
        var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
            ? xhr.responseJSON.mensaje
            : mensajePredeterminado;
        Swal.fire('Error', mensaje, 'error');
    }

    function consultar() {
        var datos = new FormData();
        datos.append('accion', 'consultar');
        enviaAjax(datos, function (respuesta) {
            if (!respuesta.ok) {
                $('#resultadoconsulta').empty().append(
                    $('<tr>').append($('<td>').attr('colspan', 7).addClass('text-center text-danger')
                        .text(respuesta.mensaje || 'No se pudieron consultar los productos.'))
                );
                return;
            }
            renderTabla(respuesta.data || []);
        }, function (xhr) {
            $('#resultadoconsulta').empty().append(
                $('<tr>').append($('<td>').attr('colspan', 7).addClass('text-center text-danger')
                    .text('No se pudieron consultar los productos. Verifique la conexión e intente nuevamente.'))
            );
            console.error(xhr.responseText || xhr.statusText);
        });
    }

    function renderTabla(lista) {
        var $tbody = $('#resultadoconsulta').empty();
        productos = {};
        if (!lista.length) {
            $tbody.append(
                $('<tr>').append($('<td>').attr('colspan', 7).addClass('text-center text-muted').text('Sin registros'))
            );
            return;
        }

        lista.forEach(function (producto) {
            productos[String(producto.idProducto)] = producto;
            var $fila = $('<tr>');
            var $acciones = $('<td>').addClass('text-nowrap');
            $acciones.append(
                $('<button>').attr({
                    type: 'button',
                    title: 'Modificar',
                    'aria-label': 'Modificar ' + producto.nombreProducto,
                    'data-id-producto': producto.idProducto
                }).addClass('btn btn-sm btn-purple me-1 btn-editar')
                    .append($('<i>').addClass('fas fa-edit'))
            );
            $acciones.append(
                $('<button>').attr({
                    type: 'button',
                    title: 'Eliminar',
                    'aria-label': 'Eliminar ' + producto.nombreProducto,
                    'data-id-producto': producto.idProducto
                }).addClass('btn btn-sm btn-outline-danger btn-eliminar')
                    .append($('<i>').addClass('fas fa-trash-alt'))
            );
            $fila.append($acciones);
            $fila.append($('<td>').text(producto.nombreProducto || ''));
            $fila.append($('<td>').text(producto.marca || '—'));
            $fila.append($('<td>').text(Number(producto.precioProducto).toFixed(2)));
            $fila.append($('<td>').text(producto.nombreProveedor || ''));
            $fila.append($('<td>').text(producto.cantidadActual));
            $fila.append($('<td>').text(producto.tipoProducto || 'Sin clasificar'));
            $tbody.append($fila);
        });
    }

    function cargarCatalogos() {
        var datos = new FormData();
        datos.append('accion', 'catalogos');
        enviaAjax(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('Error', respuesta.mensaje || 'No se pudieron cargar los datos.', 'error');
                return;
            }
            var $proveedor = $('#idProveedor').empty()
                .append($('<option>').val('').text('Seleccione proveedor'));
            respuesta.data.proveedores.forEach(function (proveedor) {
                $proveedor.append(
                    $('<option>').val(proveedor.idProveedor).text(proveedor.nombreProveedor)
                );
            });

            var $tipo = $('#tipoProducto').empty()
                .append($('<option>').val('').text('Seleccione el tipo de producto'));
            respuesta.data.tipos.forEach(function (tipo) {
                $tipo.append(
                    $('<option>').val(tipo.idTipo).text(tipo.nombreTipo)
                );
            });
            $('#incluir').prop(
                'disabled',
                respuesta.data.proveedores.length === 0 || respuesta.data.tipos.length === 0
            );
        }, function (xhr) {
            mostrarError(xhr, 'No se pudieron cargar proveedores y tipos de producto.');
        });
    }

    function limpiarFormulario() {
        $('#formulario_producto')[0].reset();
        $('#idProducto').val('');
        $('#accion').val('incluir');
        $('#proceso').text('INCLUIR');
        $('#modal1Label').text('Añadir producto');
        $('#snombreProducto, #smarca, #sidProveedor, #sprecioProducto, #scantidadActual, #stipoProducto').text('');
    }

    $('#incluir').on('click', function () {
        limpiarFormulario();
        modal.show();
    });

    $('#resultadoconsulta').on('click', '.btn-editar', function () {
        var idProducto = String($(this).attr('data-id-producto'));
        var producto = productos[idProducto];
        if (!producto) {
            Swal.fire('Error', 'No se encontró la información del producto.', 'error');
            return;
        }

        limpiarFormulario();
        $('#idProducto').val(producto.idProducto);
        $('#nombreProducto').val(producto.nombreProducto);
        $('#marca').val(producto.marca || '');
        $('#idProveedor').val(producto.idProveedor);
        $('#precioProducto').val(producto.precioProducto);
        $('#cantidadActual').val(producto.cantidadActual);
        $('#tipoProducto').val(producto.idTipo || '');
        $('#accion').val('modificar');
        $('#proceso').text('GUARDAR CAMBIOS');
        $('#modal1Label').text('Modificar producto');
        modal.show();
    });

    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var idProducto = $(this).attr('data-id-producto');
        Swal.fire({
            title: '¿Eliminar producto?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) {
                return;
            }
            var datos = new FormData();
            datos.append('accion', 'eliminar');
            datos.append('idProducto', idProducto);
            enviaAjax(datos, function (respuesta) {
                if (!respuesta.ok) {
                    Swal.fire('Error', respuesta.mensaje || 'No se pudo eliminar el producto.', 'error');
                    return;
                }
                Swal.fire('Eliminado', respuesta.mensaje, 'success');
                consultar();
            }, function (xhr) {
                mostrarError(xhr, 'No se pudo eliminar el producto.');
            });
        });
    });

    $('#formulario_producto').on('submit', function (evento) {
        evento.preventDefault();
        if (!this.reportValidity()) {
            return;
        }
        var datos = new FormData(this);
        $('#proceso').prop('disabled', true);
        enviaAjax(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('Error', respuesta.mensaje || 'No se pudo guardar el producto.', 'error');
                return;
            }
            Swal.fire('Éxito', respuesta.mensaje, 'success');
            modal.hide();
            limpiarFormulario();
            consultar();
        }, function (xhr) {
            mostrarError(xhr, 'No se pudo guardar el producto.');
        }).always(function () {
            $('#proceso').prop('disabled', false);
        });
    });

    document.getElementById('modal1').addEventListener('hidden.bs.modal', limpiarFormulario);
    cargarCatalogos();
    consultar();
});
