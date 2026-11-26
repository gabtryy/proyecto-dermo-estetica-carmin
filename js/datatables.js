(function ($) {
    'use strict';

    var tableSelector = 'table';
    var language = {
        emptyTable: '',
        zeroRecords: 'No se encontraron resultados',
        search: 'Buscar:',
        paginate: {
            first: 'Primero',
            last: 'Último',
            next: 'Siguiente',
            previous: 'Anterior'
        },
        aria: {
            sortAscending: ': ordenar de manera ascendente',
            sortDescending: ': ordenar de manera descendente'
        }
    };

    function esTablaDeListado(tabla) {
        return $(tabla).closest('.modal').length === 0;
    }

    function opcionesTabla(tabla) {
        var columnasNoOrdenables = [];

        $(tabla).find('thead th').each(function (indice) {
            if (/acciones|quitar/i.test($(this).text().trim())) {
                columnasNoOrdenables.push(indice);
            }
        });

        return {
            paging: true,
            searching: true,
            ordering: true,
            info: false,
            lengthChange: false,
            language: language,
            columnDefs: columnasNoOrdenables.length
                ? [{ targets: columnasNoOrdenables, orderable: false }]
                : []
        };
    }

    function obtenerContenido(tabla) {
        return tabla.tBodies.length ? tabla.tBodies[0].innerHTML : '';
    }

    function inicializarTabla(tabla) {
        if (!tabla.tHead || !$.fn.dataTable || $.fn.dataTable.isDataTable(tabla)) {
            return;
        }
        if ($(tabla).find('tbody td[colspan]').length) {
            return;
        }

        var api = $(tabla).DataTable(opcionesTabla(tabla));
        tabla.__contenidoDataTable = obtenerContenido(tabla);
        api.on('draw', function () {
            tabla.__contenidoDataTable = obtenerContenido(tabla);
        });
    }

    function reconstruirTabla(tabla) {
        var contenido = obtenerContenido(tabla);
        var api = $.fn.dataTable.isDataTable(tabla) ? $(tabla).DataTable() : null;

        if (api) {
            api.destroy();
        }

        if (tabla.tBodies.length) {
            tabla.tBodies[0].innerHTML = contenido;
        }

        delete tabla.__contenidoDataTable;
        inicializarTabla(tabla);
    }

    function sincronizarTablas() {
        $(tableSelector).filter(function () {
            return esTablaDeListado(this) && this.tHead;
        }).each(function () {
            if (!$.fn.dataTable.isDataTable(this)) {
                inicializarTabla(this);
            } else if (this.__contenidoDataTable !== obtenerContenido(this)) {
                reconstruirTabla(this);
            }
        });
    }

    window.actualizarTablaDataTable = function (selector, actualizar) {
        $(selector).each(function () {
            var tabla = $(this).closest('table')[0];
            if (!tabla || !esTablaDeListado(tabla) || !tabla.tBodies.length) {
                return;
            }

            var api = $.fn.dataTable.isDataTable(tabla) ? $(tabla).DataTable() : null;
            if (api) {
                api.destroy();
            }

            var $tbody = $(tabla.tBodies[0]).empty();
            if (typeof actualizar === 'function') {
                actualizar($tbody);
            }

            delete tabla.__contenidoDataTable;
            inicializarTabla(tabla);
        });
    };

    $(function () {
        sincronizarTablas();
        $(document).on('ajaxComplete', sincronizarTablas);
    });
}(jQuery));