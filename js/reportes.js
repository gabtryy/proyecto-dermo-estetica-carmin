$(function () {
    $('#tipoReporte').on('change', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('pagina', 'reportes');
        url.searchParams.set('tipo', this.value);
        url.searchParams.set('fechaInicio', $('#fechaInicio').val());
        url.searchParams.set('fechaFin', $('#fechaFin').val());
        window.location.href = url.toString();
    });
});
