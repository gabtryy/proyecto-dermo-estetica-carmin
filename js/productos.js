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

    function obtenerErroresFormulario() {
        var nombre = $('#nombreProducto').val().trim();
        var marca = $('#marca').val().trim();
        var proveedor = $('#idProveedor').val();
        var precio = $('#precioProducto').val().trim();
        var cantidad = $('#cantidadActual').val().trim();
        var unidadMedida = $('#unidad_medida').val();
        var tipo = $('#tipoProducto').val();
        var accion = $('#accion').val();
        var idProducto = $('#idProducto').val();
        var errores = {};
        var proveedorExiste = $('#idProveedor option').filter(function () {
            return this.value === proveedor;
        }).length > 0;
        var tipoExiste = $('#tipoProducto option').filter(function () {
            return this.value === tipo;
        }).length > 0;

        if (Array.from(nombre).length < 1 || Array.from(nombre).length > 100) {
            errores.nombreProducto = 'Ingrese un nombre de hasta 100 caracteres.';
        }
        if (Array.from(marca).length > 50) {
            errores.marca = 'La marca no puede superar los 50 caracteres.';
        }
        if (!/^[1-9]\d*$/.test(proveedor) || Number(proveedor) > 2147483647 || !proveedorExiste) {
            errores.idProveedor = 'Seleccione un proveedor válido.';
        }
        if (
            !/^\d+(?:\.\d{1,2})?$/.test(precio) ||
            Number(precio) <= 0 ||
            Number(precio) > 99999999.99
        ) {
            errores.precioProducto = 'Ingrese un precio mayor que cero, con hasta 2 decimales.';
        }
        if (!/^\d+(?:\.\d{1,2})?$/.test(cantidad) || Number(cantidad) > 99999999.99) {
            errores.cantidadActual = 'Ingrese una cantidad entre 0 y 99999999.99, con hasta 2 decimales.';
        }
        if (['und', 'ml', 'L', 'g', 'kg'].indexOf(unidadMedida) === -1) {
            errores.unidad_medida = 'Seleccione una unidad de medida válida.';
        }
        if (!/^[1-9]\d*$/.test(tipo) || Number(tipo) > 2147483647 || !tipoExiste) {
            errores.tipoProducto = 'Seleccione un tipo de producto válido.';
        }
        if (accion === 'modificar' && (!/^[1-9]\d*$/.test(idProducto) || Number(idProducto) > 2147483647)) {
            errores.idProducto = 'El producto seleccionado no es válido.';
        }

        return errores;
    }

    function mostrarErrorCampo(campo, error) {
        $('#s' + campo).text(error || '');
        $('#' + campo).toggleClass('is-invalid', Boolean(error));
    }

    function validarFormulario() {
        var errores = obtenerErroresFormulario();
        $('#snombreProducto, #smarca, #sidProveedor, #sprecioProducto, #scantidadActual, #sunidad_medida, #stipoProducto').text('');
        $('#formulario_producto .is-invalid').removeClass('is-invalid');
        Object.keys(errores).forEach(function (campo) {
            mostrarErrorCampo(campo, errores[campo]);
        });

        if (errores.idProducto) {
            Swal.fire('Error', errores.idProducto, 'error');
            return false;
        }

        var primerCampoInvalido = Object.keys(errores).find(function (campo) {
            return errores[campo] !== '';
        });
        if (primerCampoInvalido) {
            $('#' + primerCampoInvalido).trigger('focus');
            return false;
        }
        return true;
    }

    function consultar() {
        var datos = new FormData();
        datos.append('accion', 'consultar');
        enviaAjax(datos, function (respuesta) {
            if (!respuesta.ok) {
                actualizarTablaDataTable(
                    '#resultadoconsulta',
                    null,
                    respuesta.mensaje || 'No se pudieron consultar los productos.'
                );
                return;
            }
            renderTabla(respuesta.data || []);
        }, function (xhr) {
            actualizarTablaDataTable(
                '#resultadoconsulta',
                null,
                'No se pudieron consultar los productos. Verifique la conexión e intente nuevamente.'
            );
            console.error(xhr.responseText || xhr.statusText);
        });
    }

    function renderTabla(lista) {
        productos = {};

        actualizarTablaDataTable('#resultadoconsulta', function ($tbody) {
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
                $fila.append($('<td>').text(producto.nombreProducto || ''));
                $fila.append($('<td>').text(producto.marca || '—'));
                $fila.append($('<td>').text(Number(producto.precioProducto).toFixed(2)));
                $fila.append($('<td>').text(producto.nombreProveedor || ''));
                $fila.append($('<td>').text(producto.cantidadActual));
                $fila.append($('<td>').text(producto.unidad_medida || 'und'));
                $fila.append($('<td>').text(producto.tipoProducto || 'Sin clasificar'));
                $fila.append($acciones);
                $tbody.append($fila);
            });
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
        $('#formulario_producto .is-invalid').removeClass('is-invalid');
        $('#idProducto').val('');
        $('#accion').val('incluir');
        $('#proceso').text('REGISTRAR');
        $('#modal1Label').text('Añadir producto');
        $('#snombreProducto, #smarca, #sidProveedor, #sprecioProducto, #scantidadActual, #sunidad_medida, #stipoProducto').text('');
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
        $('#unidad_medida').val(producto.unidad_medida || 'und');
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
        if (!validarFormulario() || !this.reportValidity()) {
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

    $('#formulario_producto').on('input change', 'input, select', function () {
        if (this.id) {
            mostrarErrorCampo(this.id, obtenerErroresFormulario()[this.id] || '');
        }
    });

    document.getElementById('modal1').addEventListener('hidden.bs.modal', limpiarFormulario);
    cargarCatalogos();
    consultar();
});
