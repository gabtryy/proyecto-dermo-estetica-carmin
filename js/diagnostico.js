function escaparHtml(valor) {
    return $('<div>').text(valor || '').html();
}

function enviaAjax({datos, done, fail, always, url = 'index.php?pagina=diagnostico', type = 'POST', dataType = 'json'}) {
    $.ajax({
        url: url,
        type: type,
        data: datos,
        processData: false,
        contentType: false,
        dataType: dataType
    })
    .done(done || function(){})
    .fail(fail || function(){})
    .always(always || function(){});
}

function cargarClientes() {
    var datos = new FormData();
    datos.append('accion', 'consultar');
    enviaAjax({
        datos: datos,
        url: 'index.php?pagina=clientes',
        done: function (respuesta) {
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
        },
        fail: function () {
            Swal.fire('Error', 'No se pudieron cargar los clientes.', 'error');
        }
    });
}

function cargarTiposDePiel() {
    var datos = new FormData();
    datos.append('accion', 'piel');
    enviaAjax({
        datos: datos,
        done: function (respuesta) {
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
        },
        fail: function () {
            Swal.fire('Error', 'No se pudieron cargar los tipos de piel.', 'error');
        }
    });
}

function guardarDiagnostico(formulario) {
    var datos = new FormData(formulario);
    var idDiagnostico = $('#idDiagnostico').val();
    datos.append('accion', idDiagnostico ? 'modificar' : 'incluir');

    enviaAjax({
        datos: datos,
        done: function (respuesta) {
            if (respuesta && respuesta.ok) {
                Swal.fire('Éxito', respuesta.mensaje || 'Diagnóstico guardado correctamente.', 'success');
                formulario.reset();
                $('#idDiagnostico').val('');
                $('#guardar-diagnostico').html('<i class="fas fa-save me-1"></i>Guardar diagnóstico');
                cargarDiagnosticos();
            } else {
                Swal.fire('Error', respuesta.mensaje || 'No se pudo guardar el diagnóstico.', 'error');
            }
        },
        fail: function () {
            Swal.fire('Error', 'No se pudo guardar el diagnóstico.', 'error');
        }
    });
}

function cargarDiagnosticos() {
    var datos = new FormData();
    datos.append('accion', 'consultar');
    enviaAjax({
        datos: datos,
        done: function (respuesta) {
            var $tbody = $('#resultado-diagnosticos');
            var diagnosticos = respuesta && respuesta.ok ? (respuesta.data || []) : [];

            $tbody.empty();

            if (!diagnosticos.length) {
                $tbody.append('<tr><td colspan="9" class="text-center text-muted">No hay diagnósticos registrados.</td></tr>');
                return;
            }

            diagnosticos.forEach(function (diagnostico) {
                var $editar = $('<button type="button" title="Modificar">')
                    .addClass('btn btn-sm btn-purple me-1 btn-editar-diagnostico')
                    .attr('data-id', diagnostico.idDiagnostico)
                    .append('<i class="fas fa-edit"></i>');
                var $eliminar = $('<button type="button" title="Eliminar">')
                    .addClass('btn btn-sm btn-danger btn-eliminar-diagnostico')
                    .attr('data-id', diagnostico.idDiagnostico)
                    .append('<i class="fas fa-trash-alt"></i>');
                var $fila = $('<tr>');

                [
                    diagnostico.nombreCliente || 'Sin cliente',
                    diagnostico.nom_Piel || 'Sin tipo',
                    diagnostico.fecha_diagnostico || 'Sin fecha',
                    diagnostico.frente || 'sin detalle',
                    diagnostico.nariz || 'sin detalle',
                    diagnostico.mejilla_izq || 'sin detalle',
                    diagnostico.mejilla_der || 'sin detalle',
                    diagnostico.menton || 'sin detalle'
                ].forEach(function (valor) {
                    $fila.append($('<td>').text(valor));
                });
                $fila.append($('<td>').addClass('text-nowrap').append($editar, $eliminar));
                $tbody.append($fila);
            });
        },
        fail: function () {
            Swal.fire('Error', 'No se pudieron cargar los diagnósticos.', 'error');
        }
    });
}

$(document).ready(function () {
    cargarClientes();
    cargarTiposDePiel();
    cargarDiagnosticos();

    $('#resultado-diagnosticos').on('click', '.btn-editar-diagnostico', function () {
        var id = $(this).attr('data-id');
        var datos = new FormData();
        datos.append('accion', 'consultar');
        enviaAjax({
            datos: datos,
            done: function (respuesta) {
                var diagnostico = (respuesta.data || []).find(function (item) {
                    return String(item.idDiagnostico) === String(id);
                });
                if (!diagnostico) {
                    Swal.fire('Error', 'No se encontró el diagnóstico.', 'error');
                    return;
                }

                $('#idDiagnostico').val(diagnostico.idDiagnostico);
                $('#cedulaCliente').val(diagnostico.cedulaCliente);
                $('#idPiel').val(diagnostico.idPiel);
                $('#frente').val(diagnostico.frente);
                $('#naris').val(diagnostico.nariz);
                $('#mejilla_izquierda').val(diagnostico.mejilla_izq);
                $('#mejilla_derecha').val(diagnostico.mejilla_der);
                $('#menton').val(diagnostico.menton);
                $('#guardar-diagnostico').html('<i class="fas fa-save me-1"></i>Guardar cambios');
                document.getElementById('formulario_diagnostico').scrollIntoView({ behavior: 'smooth' });
            },
            fail: function () {
                Swal.fire('Error', 'No se pudo cargar el diagnóstico.', 'error');
            }
        });
    });

    $('#resultado-diagnosticos').on('click', '.btn-eliminar-diagnostico', function () {
        var id = $(this).attr('data-id');
        Swal.fire({
            title: '¿Eliminar diagnóstico?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.isConfirmed) return;
            var datos = new FormData();
            datos.append('accion', 'eliminar');
            datos.append('idDiagnostico', id);
            enviaAjax({
                datos: datos,
                done: function (respuesta) {
                    if (respuesta && respuesta.ok) {
                        Swal.fire('Eliminado', respuesta.mensaje, 'success');
                        cargarDiagnosticos();
                    } else {
                        Swal.fire('Error', respuesta.mensaje || 'No se pudo eliminar.', 'error');
                    }
                },
                fail: function () {
                    Swal.fire('Error', 'No se pudo eliminar el diagnóstico.', 'error');
                }
            });
        });
    });

    $('#formulario_diagnostico').on('submit', function (e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        guardarDiagnostico(this);
    });

    $('#formulario_diagnostico').on('reset', function () {
        $('#idDiagnostico').val('');
        $('#guardar-diagnostico').html('<i class="fas fa-save me-1"></i>Guardar diagnóstico');
    });
});
