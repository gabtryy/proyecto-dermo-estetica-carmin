$(document).ready(function () {
    function enviar(datos, done, fail) {
        return $.ajax({
            url: 'index.php?pagina=configuracion',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(done).fail(fail);
    }

    function mostrarError(xhr, mensajePredeterminado) {
        var mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
            ? xhr.responseJSON.mensaje
            : mensajePredeterminado;
        Swal.fire('Error', mensaje, 'error');
    }

    function filaVacia(columnas, mensaje) {
        return $('<tr>').append(
            $('<td>').attr('colspan', columnas).addClass('text-center text-muted').text(mensaje)
        );
    }

    function formatoTasa(valor) {
        return Number(valor).toLocaleString('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function consultar() {
        var datos = new FormData();
        datos.append('accion', 'consultar');
        enviar(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('Error', respuesta.mensaje || 'No se pudo cargar la configuración.', 'error');
                return;
            }

            var monedas = respuesta.data.monedas || [];
            var metodos = respuesta.data.metodosPago || [];
            var tasas = respuesta.data.tasas || [];
            var $monedas = $('#listaMonedas').empty();
            var $selector = $('#monedaTasa').empty().append(
                $('<option>').val('').text('Seleccione moneda')
            );
            var $metodos = $('#listaMetodos').empty();
            var $tasas = $('#listaTasas').empty();

            if (!monedas.length) {
                $monedas.append(filaVacia(3, 'No hay monedas registradas.'));
            }
            monedas.forEach(function (moneda) {
                var $fila = $('<tr>');
                $fila.append($('<td>').text(moneda.nombre_moneda));
                $fila.append($('<td>').text(moneda.simbolo));
                $fila.append($('<td>').text(
                    moneda.tasa_actual === null
                        ? (moneda.es_base ? '1,00 (moneda base)' : 'Sin tasa para hoy')
                        : moneda.simbolo + ' ' + formatoTasa(moneda.tasa_actual)
                ));
                $monedas.append($fila);
                if (Number(moneda.es_base) !== 1) {
                    $selector.append(
                        $('<option>').val(moneda.id_moneda).text(moneda.nombre_moneda + ' (' + moneda.simbolo + ')')
                    );
                }
            });

            if (!metodos.length) {
                $metodos.append(filaVacia(1, 'No hay métodos de pago registrados.'));
            }
            metodos.forEach(function (metodo) {
                $metodos.append($('<tr>').append(
                    $('<td>').text(metodo.nom_MetodoPago)
                ));
            });

            if (!tasas.length) {
                $tasas.append(filaVacia(3, 'Todavía no hay tasas registradas.'));
            }
            tasas.forEach(function (tasa) {
                var $fila = $('<tr>');
                $fila.append($('<td>').text(tasa.fecha_tasa));
                $fila.append($('<td>').text(tasa.nombre_moneda + ' (' + tasa.simbolo + ')'));
                $fila.append($('<td>').text(formatoTasa(tasa.monto_tasa)));
                $tasas.append($fila);
            });
        }, function (xhr) {
            mostrarError(xhr, 'No se pudo cargar la configuración.');
        });
    }

    function validarCampoConfiguracion(campo) {
        var $campo = $('#' + campo);
        var valor = String($campo.val() || '').trim();
        var error = '';

        if (campo === 'nombre_moneda' && (!valor || Array.from(valor).length > 50)) {
            error = 'Ingrese el nombre de la moneda, con un máximo de 50 caracteres.';
        } else if (campo === 'simbolo' && (!valor || Array.from(valor).length > 5)) {
            error = 'Ingrese un símbolo de hasta 5 caracteres.';
        } else if (campo === 'nom_MetodoPago' && (!valor || Array.from(valor).length > 50)) {
            error = 'Ingrese el nombre del método de pago, con un máximo de 50 caracteres.';
        } else if (campo === 'monedaTasa') {
            var opcionValida = $campo.find('option').filter(function () {
                return this.value === valor && /^[1-9]\d*$/.test(this.value);
            }).length > 0;
            if (!opcionValida) {
                error = 'Seleccione una moneda válida.';
            }
        } else if (campo === 'fecha_tasa') {
            var partes = /^(\d{4})-(\d{2})-(\d{2})$/.exec(valor);
            var fechaValida = false;
            if (partes) {
                var fecha = new Date(Number(partes[1]), Number(partes[2]) - 1, Number(partes[3]));
                fechaValida = fecha.getFullYear() === Number(partes[1]) &&
                    fecha.getMonth() === Number(partes[2]) - 1 &&
                    fecha.getDate() === Number(partes[3]);
            }
            if (!fechaValida || valor > $campo.attr('max')) {
                error = 'Indique una fecha válida que no sea futura.';
            }
        } else if (campo === 'monto_tasa') {
            var tasa = Number(valor);
        if (!/^\d{1,6}(?:\.\d{1,4})?$/.test(valor) || !Number.isFinite(tasa) || tasa <= 0) {
                error = 'La tasa debe ser mayor que cero y tener hasta 4 decimales.';
            }
        }

        $('#s' + campo).text(error);
        $campo.toggleClass('is-invalid', Boolean(error));
        return !error;
    }

    function validarFormularioConfiguracion(formulario) {
        var campos = {
            formMoneda: ['nombre_moneda', 'simbolo'],
            formMetodo: ['nom_MetodoPago'],
            formTasa: ['monedaTasa', 'fecha_tasa', 'monto_tasa']
        }[formulario.id];
        var valido = true;
        campos.forEach(function (campo) {
            if (!validarCampoConfiguracion(campo)) {
                valido = false;
            }
        });
        if (!valido) {
            $(formulario).find('.is-invalid').first().trigger('focus');
        }
        return valido;
    }

    $('#formMoneda, #formMetodo, #formTasa').on('input change', 'input, select', function () {
        validarCampoConfiguracion(this.id);
    }).on('reset', function () {
        var formulario = this;
        window.setTimeout(function () {
            $(formulario).find('.is-invalid').removeClass('is-invalid');
            $(formulario).find('.form-text.text-danger').text('');
        }, 0);
    });

    $('#formMoneda, #formMetodo, #formTasa').on('submit', function (evento) {
        evento.preventDefault();
        var formulario = this;
        if (!validarFormularioConfiguracion(formulario) || !formulario.reportValidity()) {
            return;
        }
        var datos = new FormData(formulario);

        enviar(datos, function (respuesta) {
            if (!respuesta.ok) {
                Swal.fire('Error', respuesta.mensaje || 'No se pudo guardar.', 'error');
                return;
            }
            Swal.fire('Guardado', respuesta.mensaje, 'success');
            formulario.reset();
            if (formulario.id === 'formTasa') {
                $('#fecha_tasa').val($('#fecha_tasa').attr('max'));
            }
            consultar();
        }, function (xhr) {
            mostrarError(xhr, 'No se pudo guardar la configuración.');
        });
    });

    consultar();
});
