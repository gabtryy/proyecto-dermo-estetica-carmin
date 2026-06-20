</div> 
    <footer class="bg-dark text-white text-center py-3 mt-4">
        &copy; <?php echo date('Y'); ?> Sistema de Gestión dermon estetica carmin. 
    </footer>
    <script src="vendor/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/js/dataTables.bootstrap5.min.js"></script>
    <script src="vendor/sweetalert2/js/sweetalert2.all.min.js"></script>
    <?php $controladorJS = $pagina ?? ''; ?>
    <?php if ($controladorJS === 'clientes'): ?>
        <script src="js/cliente.js"></script>
    <?php elseif ($controladorJS === 'esteticistas'): ?>
        <script src="js/esteticista.js"></script>
    <?php elseif ($controladorJS === 'servicios'): ?>
        <script src="js/servicio.js"></script>
    <?php elseif ($controladorJS === 'productos'): ?>
        <script src="js/productos.js"></script> 
    <?php endif; ?>
</body>
</html>