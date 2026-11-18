function muestraMensajeUsuario(mensaje, icono) {
    Swal.fire({
        icon: icono,
        text: mensaje,
        showConfirmButton: true,
        position: 'center'
    });
}

function consultarUsuarios() {
    var datos = new FormData();
    datos.append('accion', 'consultar');
    $.ajax({
        url: 'index.php?pagina=usuarios',
        type: 'POST',
        data: datos,
        processData: false,
        contentType: false,
        dataType: 'json'
    }).done(function (respuesta) {
        if (respuesta.ok) {
            renderTablaUsuarios(respuesta.data);
        } else {
            muestraMensajeUsuario(respuesta.mensaje || 'No se pudieron consultar los usuarios.', 'error');
        }
    }).fail(function (request) {
        muestraMensajeUsuario(
            request.responseJSON?.mensaje || 'No se pudieron consultar los usuarios.',
            'error'
        );
    });
}

function cargarRolesUsuario() {
    var datos = new FormData();
    datos.append('accion', 'roles');
    $.ajax({
        url: 'index.php?pagina=usuarios',
        type: 'POST',
        data: datos,
        processData: false,
        contentType: false,
        dataType: 'json'
    }).done(function (respuesta) {
        if (!respuesta.ok) {
            muestraMensajeUsuario(respuesta.mensaje || 'No se pudieron cargar los roles.', 'error');
            return;
        }

        var $rol = $('#rol').empty();
        $rol.append($('<option>', { value: '', text: 'Seleccione un rol' }));
        respuesta.data.forEach(function (item) {
            $rol.append($('<option>', {
                value: item.id_rol,
                text: item.tipo_usuario
            }));
        });
    }).fail(function (request) {
        muestraMensajeUsuario(
            request.responseJSON?.mensaje || 'No se pudieron cargar los roles.',
            'error'
        );
    });
}

function renderTablaUsuarios(lista) {
    var $tbody = $('#resultadoconsulta').empty();
    if (!lista.length) {
        $tbody.append($('<tr>').append(
            $('<td>', { colspan: 3, class: 'text-center text-muted', text: 'Sin registros' })
        ));
        return;
    }

    lista.forEach(function (item) {
        var $fila = $('<tr>');
        $fila.append($('<td>').text(item.cedula || ''));
        $fila.append($('<td>').text(item.usuario || ''));
        $fila.append($('<td>').text(item.rol || ''));
        $tbody.append($fila);
    });
}

function validarUsuario() {
    var cedula = $('#cedula').val().trim();
    var usuario = $('#usuario').val().trim();
    var clave = $('#clave').val();
    var rol = $('#rol').val();
    var errores = {
        cedula: !/^[1-9]\d{6,9}$/.test(cedula) || Number(cedula) > 2147483647
            ? 'Ingrese una cédula válida de 7 a 10 dígitos.'
            : '',
        usuario: !/^[A-Za-z0-9._-]{3,32}$/.test(usuario)
            ? 'Use entre 3 y 32 caracteres: letras, números, punto, guion o guion bajo.'
            : '',
        clave: !clave || clave.length > 255
            ? 'La clave es obligatoria y debe tener hasta 255 caracteres.'
            : '',
        rol: !rol ? 'Seleccione un rol.' : ''
    };

    Object.keys(errores).forEach(function (campo) {
        $('#s' + campo).text(errores[campo]);
        $('#' + campo).toggleClass('is-invalid', Boolean(errores[campo]));
    });
    return !Object.keys(errores).some(function (campo) { return Boolean(errores[campo]); });
}

$(document).ready(function () {
    consultarUsuarios();
    cargarRolesUsuario();

    $('#incluir').on('click', function () {
        $('#formulario_usuario')[0].reset();
        $('#formulario_usuario .is-invalid').removeClass('is-invalid');
        $('#formulario_usuario .form-text').text('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).show();
    });

    $('#formulario_usuario').on('submit', function (evento) {
        evento.preventDefault();
        if (!validarUsuario()) {
            return;
        }

        var datos = new FormData(this);
        datos.set('accion', 'incluir');
        $.ajax({
            url: 'index.php?pagina=usuarios',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (respuesta) {
            if (!respuesta.ok) {
                muestraMensajeUsuario(respuesta.mensaje || 'No se pudo crear el usuario.', 'error');
                return;
            }

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
            muestraMensajeUsuario(respuesta.mensaje, 'success');
            consultarUsuarios();
        }).fail(function (request) {
            muestraMensajeUsuario(
                request.responseJSON?.mensaje || 'No se pudo crear el usuario.',
                'error'
            );
        });
    });
});
