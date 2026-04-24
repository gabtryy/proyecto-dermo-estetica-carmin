function consultar() {
	var datos = new FormData();
	datos.append('accion', 'consultar');

	$.ajax({
		url: 'index.php?c=clientes',
		type: 'POST',
		data: datos,
		processData: false,
		contentType: false,
		dataType: 'json'
	}).done(function (resp) {
		if (!resp || !resp.ok) {
			return;
		}
		renderTabla(resp.data || []);
	});
}

function renderTabla(lista) {
	var $tbody = $('#resultadoconsulta');
	$tbody.empty();

	if (!lista.length) {
		$tbody.append('<tr><td colspan="7" class="text-center text-muted">Sin registros</td></tr>');
		return;
	}

	lista.forEach(function (item) {
		var fila = ''
			+ '<tr>'
			+ '<td><button class="btn btn-sm btn-outline-secondary" disabled>...</button></td>'
			+ '<td>' + (item.cedula_cliente || '') + '</td>'
			+ '<td>' + (item.nombre_cliente || '') + '</td>'
			+ '<td>' + (item.telefono_cliente || '') + '</td>'
			+ '<td>' + (item.direccion_cliente || '') + '</td>'
			+ '<td>' + (item.fecha_nacimiento || '') + '</td>'
			+ '<td>' + (item.genero || '') + '</td>'
			+ '</tr>';
		$tbody.append(fila);
	});
}

$(document).ready(function () {
	$('#incluir').on('click', function () {
		$('#proceso').text('INCLUIR');
		$('#accion').val('incluir');
		var $modal = $('#modal1');
		if (!$modal.length || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
			return;
		}
		bootstrap.Modal.getOrCreateInstance($modal[0]).show();
	});

	$('#proceso').on('click', function () {
		var form = document.getElementById('formulario_cliente');
		if (!form) {
			return;
		}
		var datos = new FormData(form);
		datos.set('accion', 'incluir');

		$.ajax({
			url: 'index.php?c=clientes',
			type: 'POST',
			data: datos,
			processData: false,
			contentType: false,
			dataType: 'json'
		}).done(function (resp) {
			if (!resp || !resp.ok) {
				alert((resp && resp.mensaje) || 'No se pudo guardar.');
				return;
			}
			alert(resp.mensaje || 'Cliente guardado.');
			bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
			form.reset();
			consultar();
		}).fail(function (xhr) {
			var msg = 'Error al guardar cliente.';
			if (xhr.responseJSON && xhr.responseJSON.mensaje) {
				msg = xhr.responseJSON.mensaje;
			}
			alert(msg);
		});
	});

	consultar();
});
