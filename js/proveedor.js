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
        var $fila = $('<tr>');
        [
            item.rif, item.nombreProveedor, item.estadoDirProveedor,
            item.municipioDirProveedor, item.parroquiaDirProveedor,
            item.telefono, item.correo
        ].forEach(function (valor) {
            $fila.append($('<td>').text(valor || ''));
        });
        $fila.append($('<td>').addClass('text-nowrap').append(
            $('<button>').attr({
                type: 'button',
                title: 'Modificar',
                'aria-label': 'Modificar proveedor',
                'data-id-proveedor': item.idProveedor || ''
            }).addClass('btn btn-sm btn-purple me-1 btn-editar')
                .append($('<i>').addClass('fas fa-edit'))
        ).append(
            $('<button>').attr({
                type: 'button',
                title: 'Eliminar',
                'aria-label': 'Eliminar proveedor',
                'data-id-proveedor': item.idProveedor || ''
            }).addClass('btn btn-sm btn-danger btn-eliminar')
                .append($('<i>').addClass('fas fa-trash-alt'))
        ));
        $tbody.append($fila);
    });
}

$(document).ready(function () {
    var currentEditId = null;

    function obtieneEntidadProveedor() {
        return direccionesVenezuela.find(function (entidad) {
            return entidad.nombre === $('#estadoDirProveedor').val();
        });
    }

    function actualizaParroquiasProveedor(parroquiaSeleccionada) {
        var entidad = obtieneEntidadProveedor();
        var nombreMunicipio = entidad ? $('#municipioDirProveedorSelect').val() : '';
        var municipio = entidad && entidad.municipios.find(function (item) {
            return item.nombre === nombreMunicipio;
        });
        var parroquias = municipio ? municipio.parroquias : [];
        var tieneParroquias = parroquias.length > 0;
        var tieneMunicipio = entidad
            ? Boolean(nombreMunicipio)
            : Boolean($('#municipioDirProveedor').val().trim());
        var permiteParroquiaManual = !entidad || (tieneMunicipio && !tieneParroquias);
        var mostrarSelectParroquia = Boolean(entidad) && !permiteParroquiaManual;
        var $parroquia = $('#parroquiaDirProveedorSelect');
        var $parroquiaInput = $('#parroquiaDirProveedor');

        $parroquia.empty().append($('<option>', {
            value: '',
            text: 'Seleccione una parroquia'
        }));
        if (tieneParroquias) {
            parroquias.forEach(function (parroquia) {
                $parroquia.append($('<option>', { value: parroquia, text: parroquia }));
            });
        }
        $parroquia.toggleClass('d-none', !mostrarSelectParroquia).prop('disabled', !tieneParroquias);
        $parroquiaInput.toggleClass('d-none', !permiteParroquiaManual)
            .prop('disabled', !permiteParroquiaManual || !tieneMunicipio)
            .val(permiteParroquiaManual ? (parroquiaSeleccionada || '') : '');
        $('#labelParroquiaDirProveedor').attr(
            'for',
            mostrarSelectParroquia
                ? 'parroquiaDirProveedorSelect'
                : 'parroquiaDirProveedor'
        );
        if (tieneParroquias && parroquiaSeleccionada) {
            $parroquia.val(parroquiaSeleccionada);
        }
    }

    function actualizaDireccionProveedor(municipioSeleccionado, parroquiaSeleccionada) {
        var estadoSeleccionado = $('#estadoDirProveedor').val();
        var entidad = obtieneEntidadProveedor();
        var usaCatalogo = Boolean(entidad);
        var tieneEstado = Boolean(estadoSeleccionado);
        var $municipioInput = $('#municipioDirProveedor');
        var $parroquiaInput = $('#parroquiaDirProveedor');
        var $municipioSelect = $('#municipioDirProveedorSelect');

        $municipioInput.toggleClass('d-none', usaCatalogo)
            .prop('disabled', usaCatalogo || !tieneEstado)
            .val(usaCatalogo ? '' : (municipioSeleccionado || ''));
        $parroquiaInput.toggleClass('d-none', usaCatalogo)
            .prop('disabled', usaCatalogo || !tieneEstado)
            .val(usaCatalogo ? '' : (parroquiaSeleccionada || ''));
        $municipioSelect.toggleClass('d-none', !usaCatalogo).prop('disabled', !usaCatalogo);

        $('#labelMunicipioDirProveedor').attr('for', usaCatalogo ? 'municipioDirProveedorSelect' : 'municipioDirProveedor');

        $municipioSelect.empty().append($('<option>', {
            value: '',
            text: 'Seleccione un municipio'
        }));
        if (entidad) {
            entidad.municipios.forEach(function (municipio) {
                $municipioSelect.append($('<option>', { value: municipio.nombre, text: municipio.nombre }));
            });
            if (municipioSeleccionado) {
                var existeMunicipio = entidad.municipios.some(function (municipio) {
                    return municipio.nombre === municipioSeleccionado;
                });
                if (!existeMunicipio) {
                    $municipioSelect.append($('<option>', {
                        value: municipioSeleccionado,
                        text: municipioSeleccionado
                    }));
                }
                $municipioSelect.val(municipioSeleccionado);
            }
        }
        actualizaParroquiasProveedor(parroquiaSeleccionada);
    }

    function errorProveedor(campo) {
        var valor = String($('#' + campo).val() || '').trim();
        if (campo === 'rif' && !/^\d{8}-?\d$/.test(valor)) {
            return 'Ingresa los 8 dígitos del negocio y el dígito final del RIF.';
        }
        if (campo === 'nombreProveedor' && (Array.from(valor).length < 2 || Array.from(valor).length > 100)) {
            return 'El nombre debe tener entre 2 y 100 caracteres.';
        }
        if (campo === 'telefono') {
            if (!$('#telefono').val().trim()) {
                return 'El teléfono es obligatorio.';
            }
            var telefono = obtenerTelefono('prefijoTelefono', 'telefono', 'telefonoOriginal');
            if (telefono === null || !/^04(?:12|13|14|16|24|26)\d{7}$/.test(telefono)) {
                return 'Ingresa 7 dígitos después del prefijo seleccionado.';
            }
        }
        if (campo === 'correo' && (!valor || valor.length > 30 || document.getElementById('correo').validity.typeMismatch)) {
            return 'Ingresa un correo válido de hasta 30 caracteres.';
        }
        return '';
    }

    function mostrarErrorProveedor(campo) {
        var error = errorProveedor(campo);
        $('#s' + campo).text(error);
        $('#' + campo).toggleClass('is-invalid', Boolean(error));
        return !error;
    }

    function validarProveedor() {
        var valido = true;
        ['rif', 'nombreProveedor', 'telefono', 'correo'].forEach(function (campo) {
            if (!mostrarErrorProveedor(campo)) {
                valido = false;
            }
        });
        if (!valido) {
            $('#formulario_proveedor .is-invalid').first().trigger('focus');
        }
        return valido;
    }

    $('#formulario_proveedor').on('input change', 'input, select', function () {
        if (this.id === 'prefijoTelefono') {
            mostrarErrorProveedor('telefono');
        } else if (['rif', 'nombreProveedor', 'telefono', 'correo'].indexOf(this.id) !== -1) {
            mostrarErrorProveedor(this.id);
        }
    });
    $('#estadoDirProveedor').on('change', function () {
        actualizaDireccionProveedor();
    });
    $('#municipioDirProveedorSelect').on('change', function () {
        actualizaParroquiasProveedor();
    });
    $('#municipioDirProveedor').on('input', function () {
        $('#parroquiaDirProveedor').val('').prop('disabled', !this.value.trim());
    });
    actualizaDireccionProveedor();
    document.getElementById('modal1').addEventListener('hidden.bs.modal', function () {
        $('#formulario_proveedor .is-invalid').removeClass('is-invalid');
        $('#srif, #snombreProveedor, #sestadoDirProveedor, #smunicipioDirProveedor, #sparroquiaDirProveedor, #stelefono, #scorreo').text('');
    });

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
        var rifGuardado = $fila.find('td:eq(0)').text().trim();
        var partesRif = rifGuardado.match(/^([VEJGPC])-(\d{8}-?\d)$/i);
        var tipoRif = partesRif ? partesRif[1].toUpperCase() : 'J';
        var rif = partesRif ? partesRif[2] : rifGuardado.replace(/^[VEJGPC]-/i, '');
        var nombreProveedor = $fila.find('td:eq(1)').text().trim();
        var estado = $fila.find('td:eq(2)').text().trim();
        var municipio = $fila.find('td:eq(3)').text().trim();
        var parroquia = $fila.find('td:eq(4)').text().trim();
        var telefono = $fila.find('td:eq(5)').text().trim();
        var correo = $fila.find('td:eq(6)').text().trim();

        currentEditId = idProveedor;
        $('#idProveedor').val(idProveedor);
        $('#tipoRif').val(tipoRif);
        $('#rif').val(rif);
        $('#nombreProveedor').val(nombreProveedor);
        $('#estadoDirProveedor').val(estado);
        actualizaDireccionProveedor(municipio, parroquia);
        cargarTelefono('prefijoTelefono', 'telefono', 'telefonoOriginal', telefono);
        $('#correo').val(correo);

        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');

        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

    $('#incluir').on('click', function () {
        $('#proceso').text('REGISTRAR');
        $('#accion').val('incluir');
        $('#formulario_proveedor')[0].reset();
        actualizaDireccionProveedor();
        $('#telefonoOriginal').val('');
        $('#prefijoTelefono').val('0414');
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
        if (!validarProveedor() || !form.reportValidity()) {
            return;
        }
        var numeroRif = $('#rif').val().replace(/\D/g, '');
        var telefono = obtenerTelefono('prefijoTelefono', 'telefono', 'telefonoOriginal');
        var datos = new FormData(form);
        datos.set('rif', numeroRif);
        datos.set('tipoRif', $('#tipoRif').val());
        datos.set('telefono', telefono);

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
                actualizaDireccionProveedor();
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
