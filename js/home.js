document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('formTasaDia');
    if (!form) {
        return;
    }

    var selectMoneda = document.getElementById('monedaTasaDia');
    var inputFecha = document.getElementById('fechaTasaDia');
    var inputMonto = document.getElementById('montoTasaDia');
    var mensaje = document.getElementById('mensajeTasaDia');
    var botonGuardar = document.getElementById('guardarTasaDia');

    function fechaLocal() {
        var ahora = new Date();
        var mes = String(ahora.getMonth() + 1).padStart(2, '0');
        var dia = String(ahora.getDate()).padStart(2, '0');
        return ahora.getFullYear() + '-' + mes + '-' + dia;
    }

    function mostrarMensaje(texto, tipo) {
        mensaje.textContent = texto;
        mensaje.className = 'alert alert-' + tipo + ' mb-0';
    }

    function normalizar(texto) {
        return texto.toLocaleLowerCase('es').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    inputFecha.value = fechaLocal();
    inputFecha.max = inputFecha.value;

    var datosConsulta = new FormData();
    datosConsulta.append('accion', 'consultar');

    fetch('index.php?pagina=configuracion', {
        method: 'POST',
        body: datosConsulta
    })
        .then(function (respuesta) {
            return respuesta.json().then(function (datos) {
                if (!respuesta.ok || !datos.ok) {
                    throw new Error(datos.mensaje || 'No se pudieron cargar las monedas.');
                }
                return datos.data.monedas || [];
            });
        })
        .then(function (monedas) {
            selectMoneda.replaceChildren();
            monedas.forEach(function (moneda) {
                var opcion = document.createElement('option');
                opcion.value = moneda.id_moneda;
                opcion.textContent = moneda.nombre_moneda + ' (' + moneda.simbolo + ')';
                selectMoneda.appendChild(opcion);
            });

            var monedaBolivar = monedas.find(function (moneda) {
                return normalizar(moneda.simbolo).indexOf('bs') !== -1 ||
                    normalizar(moneda.nombre_moneda).indexOf('bolivar') !== -1;
            });
            if (monedaBolivar) {
                selectMoneda.value = monedaBolivar.id_moneda;
            } else if (monedas.length) {
                mostrarMensaje('No se encontró Bolívar (Bs); selecciona la moneda que corresponda.', 'warning');
            } else {
                mostrarMensaje('No hay monedas registradas. Agrega una en Configuración.', 'warning');
            }

            selectMoneda.disabled = monedas.length === 0;
        })
        .catch(function (error) {
            selectMoneda.replaceChildren();
            selectMoneda.appendChild(new Option('Error al cargar monedas', ''));
            mostrarMensaje(error.message || 'No se pudieron cargar las monedas.', 'danger');
        });

    form.addEventListener('submit', function (evento) {
        evento.preventDefault();
        botonGuardar.disabled = true;
        mensaje.className = 'alert d-none mb-0';

        fetch('index.php?pagina=configuracion', {
            method: 'POST',
            body: new FormData(form)
        })
            .then(function (respuesta) {
                return respuesta.json().then(function (datos) {
                    if (!respuesta.ok || !datos.ok) {
                        throw new Error(datos.mensaje || 'No se pudo guardar la tasa.');
                    }
                    return datos;
                });
            })
            .then(function (datos) {
                mostrarMensaje(datos.mensaje, 'success');
                inputFecha.value = fechaLocal();
                inputMonto.value = '';
            })
            .catch(function (error) {
                mostrarMensaje(error.message || 'No se pudo guardar la tasa.', 'danger');
            })
            .then(function () {
                botonGuardar.disabled = false;
            });
    });
});
