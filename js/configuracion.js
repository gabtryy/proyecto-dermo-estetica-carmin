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
                        ? 'Sin tasa para hoy'
                        : moneda.simbolo + ' ' + formatoTasa(moneda.tasa_actual)
                ));
                $monedas.append($fila);
                $selector.append(
                    $('<option>').val(moneda.id_moneda).text(moneda.nombre_moneda + ' (' + moneda.simbolo + ')')
                );
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

    $('#formMoneda, #formMetodo, #formTasa').on('submit', function (evento) {
        evento.preventDefault();
        var formulario = this;
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
