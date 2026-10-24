            <footer class="border-top pt-3 mt-4 text-center">
                <small class="text-muted">&copy; <?php echo date('Y'); ?> Sistema de Gestión Dermo Estética Carmin.</small>
            </footer>
        </main>
    <?php $controladorJS = $pagina ?? ''; ?>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js" defer></script>
    <?php if ($controladorJS !== 'home'): ?>
        <script src="vendor/jquery/js/jquery-3.6.0.min.js" defer></script>
        <script src="vendor/datatables/js/jquery.dataTables.min.js" defer></script>
        <script src="vendor/datatables/js/dataTables.bootstrap5.min.js" defer></script>
        <script src="vendor/sweetalert2/js/sweetalert2.all.min.js" defer></script>
    <?php endif; ?>
    <?php if ($controladorJS === 'home'): ?>
        <script src="js/home.js" defer></script>
    <?php elseif (in_array($controladorJS, ['clientes', 'esteticistas', 'proveedores'], true)): ?>
        <script src="js/telefono.js" defer></script>
        <?php if ($controladorJS === 'clientes'): ?>
            <script src="js/cliente.js" defer></script>
        <?php elseif ($controladorJS === 'esteticistas'): ?>
            <script src="js/esteticista.js" defer></script>
        <?php else: ?>
            <script src="js/proveedor.js" defer></script>
        <?php endif; ?>
    <?php elseif ($controladorJS === 'servicios'): ?>
        <script src="js/servicio.js" defer></script>
    <?php elseif ($controladorJS === 'antecedentes'): ?>
        <script src="js/antecedentes.js" defer></script>
    <?php elseif ($controladorJS === 'citas'): ?>
        <script src="js/citas.js" defer></script>
    <?php elseif ($controladorJS === 'productos'): ?>
        <script src="js/productos.js" defer></script>
    <?php elseif ($controladorJS === 'ventas'): ?>
        <script src="js/ventas.js" defer></script>
    <?php elseif ($controladorJS === 'configuracion'): ?>
        <script src="js/configuracion.js" defer></script>
    <?php elseif ($controladorJS === 'diagnostico'): ?>
        <script src="js/diagnostico.js?v=2" defer></script>
    <?php endif; ?>
</body>
</html>
