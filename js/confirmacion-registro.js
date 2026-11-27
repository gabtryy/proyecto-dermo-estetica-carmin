document.addEventListener('submit', function (evento) {
    var formulario = evento.target;
    if (!formulario.matches('[data-confirmacion-registro]')) {
        return;
    }

    if (formulario.dataset.confirmacionAprobada === 'true') {
        delete formulario.dataset.confirmacionAprobada;
        return;
    }

    var accion = formulario.querySelector('[name="accion"]');
    if (accion && accion.value && accion.value !== 'incluir') {
        return;
    }

    evento.preventDefault();
    evento.stopImmediatePropagation();
    Swal.fire({
        title: '¿Confirmas que los datos son correctos?',
        text: formulario.getAttribute('data-mensaje-confirmacion') ||
            'Revisa la información antes de registrar. Algunos datos identificadores no se pueden cambiar después.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Revisar'
    }).then(function (resultado) {
        if (resultado.isConfirmed) {
            formulario.dataset.confirmacionAprobada = 'true';
            formulario.requestSubmit();
        }
    });
}, true);
