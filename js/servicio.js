// Función centralizada para enviar AJAX (Mantenemos tu motor intacto)
function enviaAjax({datos, done, fail, always, url = 'index.php?pagina=servicios', type = 'POST', dataType = 'json'}) {
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
        $tbody.append('<tr><td colspan="4" class="text-center text-muted">Sin registros</td></tr>');
        return;
    }

    lista.forEach(function (item) {
        var fila = ''
            + '<tr>'
            + '<td class="text-nowrap">'
            +   '<button class="btn btn-sm btn-outline-celeste me-1 btn-editar" title="Modificar" data-id-servicio="' + (item.idServicio || '') + '"><i class="fas fa-edit"></i></button>'
            +   '<button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar" data-id-servicio="' + (item.idServicio || '') + '"><i class="fas fa-trash-alt"></i></button>'
            + '</td>'
            + '<td>' + (item.nombreServicio || '') + '</td>'
            + '<td>' + (item.precio || '') + '</td>'
            + '<td>' + (item.descripcion || '') + '</td>'
            + '</tr>';
        $tbody.append(fila);
    });
}

$(document).ready(function () {
    var currentEditId = null;

  
 
    if (document.getElementById('nombreServicio')) {
        document.getElementById('nombreServicio').maxLength = 50;
        document.getElementById('nombreServicio').onkeypress = function(e){
            er = /^[A-Za-z0-9 áéíóúÁÉÍÓÚñÑ]*$/; 
            validarkeypress(er, e);
        };
        document.getElementById('nombreServicio').onfocus = function(){
            document.getElementById('snombreServicio').innerText = "Solo letras y números hasta 50 caracteres";
        };
        document.getElementById('nombreServicio').onblur = function(){
            document.getElementById('snombreServicio').innerText = "";
        };
        document.getElementById('nombreServicio').onkeyup = function(){
            er = /^[A-Za-z0-9 áéíóúÁÉÍÓÚñÑ]{3,50}$/;
            validarkeyup(er, this, document.getElementById('snombreServicio'), "Nombre inválido (entre 3 y 50 caracteres)");
        };
    }

    if (document.getElementById('precio')) {
        document.getElementById('precio').maxLength = 10;
        document.getElementById('precio').onkeypress = function(e){
            er = /^[0-9.]*$/; 
            validarkeypress(er, e);
        };
        document.getElementById('precio').onfocus = function(){
            document.getElementById('sprecio').innerText = "Solo números y punto para decimales";
        };
        document.getElementById('precio').onblur = function(){
            document.getElementById('sprecio').innerText = "";
        };
        document.getElementById('precio').onkeyup = function(){
            er = /^[0-9]+(\.[0-9]{1,2})?$/;
            validarkeyup(er, this, document.getElementById('sprecio'), "Precio inválido (Ej: 1500 o 15.50)");
        };
    }

  
    $('#incluir').on('click', function () {
        $('#proceso').text('INCLUIR');
        $('#accion').val('incluir');
        currentEditId = null;
        $('#formulario_servicio')[0].reset();
        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

   
    $('#resultadoconsulta').on('click', '.btn-editar', function () {
        var idServicio = $(this).data('idServicio') || $(this).attr('data-id-servicio');
        if (!idServicio) return;

        var $fila = $(this).closest('tr');
        var nombre_servicio = $fila.find('td:eq(1)').text().trim();
        var precio = $fila.find('td:eq(2)').text().trim();
        var descripcion = $fila.find('td:eq(3)').text().trim();

        currentEditId = idServicio;
        $('#nombreServicio').val(nombre_servicio);
        $('#precio').val(precio);
        $('#descripcion').val(descripcion);
        $('#proceso').text('MODIFICAR');
        $('#accion').val('modificar');
        
        var $modal = $('#modal1');
        if ($modal.length && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }
    });

   
    $('#resultadoconsulta').on('click', '.btn-eliminar', function () {
        var idServicio = $(this).data('idServicio') || $(this).attr('data-id-servicio');
        if (!idServicio) return;

        Swal.fire({
            title: '¿Eliminar servicio?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var datos = new FormData();
                datos.append('accion', 'eliminar');
                datos.append('idServicio', idServicio);

                enviaAjax({
                    datos: datos,
                    done: function (resp) {
                        if (resp?.ok) {
                            Swal.fire('Eliminado', resp.mensaje || 'Servicio eliminado.', 'success');
                            consultar();
                        } else {
                            Swal.fire('Error', resp?.mensaje || 'No se pudo eliminar.', 'error');
                        }
                    }
                });
            }
        });
    });

  
    $('#formulario_servicio').on('submit', function (e) {
        e.preventDefault();
        
    
        var a = valida_datos(); 
        if(a != ''){
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                html: a,
                confirmButtonText: 'Aceptar'
            });
            return; 
        }

        var form = this;
        var datos = new FormData(form);
        
        if ($('#accion').val() === 'modificar' && currentEditId) {
            datos.append('idServicio', currentEditId);
        }

        enviaAjax({
            datos: datos,
            done: function (resp) {
                if (!resp || !resp.ok) {
                    Swal.fire('Error', (resp && resp.mensaje) || 'No se pudo guardar.', 'error');
                    return;
                }
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: resp.mensaje || 'Servicio guardado correctamente.',
                    confirmButtonText: 'Aceptar'
                });
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modal1')).hide();
                form.reset();
                currentEditId = null;
                consultar();
            },
            fail: function () {
                Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
            }
        });
    });

    consultar();
});

function valida_datos(){ 
    var error = '';
    
    var er = /^[A-Za-z0-9 áéíóúÁÉÍÓÚñÑ]{3,50}$/;
    var r = validarkeyup(er, document.getElementById('nombreServicio'), document.getElementById('snombreServicio'), "Solo letras y números entre 3 y 50 caracteres");
    if(r == 0){ 
       error = "El Nombre del servicio debe contener letras y números (3 a 50 caracteres).";   
       return error;  
    }
    
    er = /^[0-9]+(\.[0-9]{1,2})?$/;
    r = validarkeyup(er, document.getElementById('precio'), document.getElementById('sprecio'), "Precio inválido");
    if(r == 0){ 
       error = "El precio debe ser un número válido (Ej: 100 o 99.99).";
       return error; 
    }
    
    return error;
}

function validarkeypress(er, e){
    var key = e.keyCode || e.which;
    var tecla = String.fromCharCode(key);
    var a = er.test(tecla);
    if(!a){
        e.preventDefault();
    }
}

function validarkeyup(er, etiqueta, etiquetamensaje, mensaje){
    if(!etiqueta) return 1;
    var a = er.test(etiqueta.value);
    if(a){
        etiquetamensaje.innerText = "";
        return 1;
    }
    else{
        etiquetamensaje.innerText = mensaje;
        return 0;
    }
}