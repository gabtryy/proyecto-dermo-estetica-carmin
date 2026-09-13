function escaparHtml(valor) {
    return $('<div>').text(valor || '').html();
}

function cargarClientes() {
    $.ajax({
        url: 'index.php?pagina=clientes',
        type: 'POST',
        data: { accion: 'consultar' },
        dataType: 'json'
    }).done(function (respuesta) {
        var $cliente = $('#cedulaCliente');
        var clientes = respuesta && respuesta.ok ? (respuesta.data || []) : [];

        $cliente.empty().append('<option value="">Seleccione un cliente</option>');
        clientes.forEach(function (cliente) {
            $cliente.append(
                '<option value="' + escaparHtml(cliente.cedulaCliente) + '">' +
                escaparHtml(cliente.nombreCliente) + ' (' + escaparHtml(cliente.cedulaCliente) + ')' +
                '</option>'
            );
        });
    }).fail(function () {
        Swal.fire('Error', 'No se pudieron cargar los clientes.', 'error');
    });
}

function cargarTiposDePiel() {
    $.ajax({
        url: 'index.php?pagina=diagnostico',
        type: 'POST',
        data: { accion: 'piel' },
        dataType: 'json'
    }).done(function (respuesta) {
        var $tipoPiel = $('#idPiel');
        var tiposDePiel = respuesta && respuesta.ok ? (respuesta.data || []) : [];

        $tipoPiel.empty().append('<option value="">Seleccione el tipo de piel</option>');
        tiposDePiel.forEach(function (tipo) {
            $tipoPiel.append(
                '<option value="' + escaparHtml(tipo.idPiel) + '">' +
                escaparHtml(tipo.nom_Piel) +
                '</option>'
            );
        });
    }).fail(function () {
        Swal.fire('Error', 'No se pudieron cargar los tipos de piel.', 'error');
    });
}

function guardarDiagnostico(formulario) {
    var datos = new FormData(formulario);
    datos.append('accion', 'incluir');

    $.ajax({
        url: 'index.php?pagina=diagnostico',
        type: 'POST',
        data: datos,
        processData: false,
        contentType: false,
        dataType: 'json'
    }).done(function (respuesta) {
        if (respuesta && respuesta.ok) {
            Swal.fire('Éxito', respuesta.mensaje || 'Diagnóstico guardado correctamente.', 'success');
            formulario.reset();
            cargarDiagnosticos();
        } else {
            Swal.fire('Error', respuesta.mensaje || 'No se pudo guardar el diagnóstico.', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'No se pudo guardar el diagnóstico.', 'error');
    });
}

function cargarDiagnosticos() {
    $.ajax({
        url: 'index.php?pagina=diagnostico',
        type: 'POST',
        data: { accion: 'consultar' },
        dataType: 'json'
    }).done(function (respuesta) {
        var $tbody = $('#resultado-diagnosticos');
        var diagnosticos = respuesta && respuesta.ok ? (respuesta.data || []) : [];

        $tbody.empty();

        if (!diagnosticos.length) {
            $tbody.append('<tr><td colspan="8" class="text-center text-muted">No hay diagnósticos registrados.</td></tr>');
            return;
        }

        diagnosticos.forEach(function (diagnostico) {
            $tbody.append(
                '<tr>' +
                    '<td>' + escaparHtml(diagnostico.nombreCliente || 'Sin cliente') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.nom_Piel || 'Sin tipo') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.fecha_diagnostico || 'Sin fecha') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.frente || 'sin detalle') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.nariz || 'sin detalle') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.mejilla_izq || 'sin detalle') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.mejilla_der || 'sin detalle') + '</td>' +
                    '<td>' + escaparHtml(diagnostico.menton || 'sin detalle') + '</td>' +
                '</tr>'
            );
        });
    }).fail(function () {
        Swal.fire('Error', 'No se pudieron cargar los diagnósticos.', 'error');
    });
}

$(document).ready(function () {
    cargarClientes();
    cargarTiposDePiel();
    cargarDiagnosticos();

    $('#formulario_diagnostico').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        guardarDiagnostico(this);
    });
});
