(function () {
    var prefijos = ['0412', '0413', '0414', '0416', '0424', '0426'];

    document.addEventListener('input', function (evento) {
        if (!evento.target.matches('.telefono-numero')) {
            return;
        }

        evento.target.value = evento.target.value.replace(/\D/g, '').slice(0, 7);
    });

    window.cargarTelefono = function (prefijoId, numeroId, originalId, telefono) {
        var prefijo = document.getElementById(prefijoId);
        var numero = document.getElementById(numeroId);
        var original = document.getElementById(originalId);
        var guardado = String(telefono || '');
        var digitos = guardado.replace(/\D/g, '');

        original.value = guardado;
        numero.value = '';

        if (digitos.length === 12 && digitos.indexOf('58') === 0) {
            digitos = '0' + digitos.slice(2);
        }

        if (digitos.length === 11 && prefijos.indexOf(digitos.slice(0, 4)) !== -1) {
            prefijo.value = digitos.slice(0, 4);
            numero.value = digitos.slice(4);
        }
    };

    window.obtenerTelefono = function (prefijoId, numeroId, originalId) {
        var prefijo = document.getElementById(prefijoId).value;
        var numero = document.getElementById(numeroId).value.trim();

        if (!numero) {
            return document.getElementById(originalId).value;
        }
        if (!/^\d{7}$/.test(numero) || prefijos.indexOf(prefijo) === -1) {
            return null;
        }

        return prefijo + numero;
    };
}());
