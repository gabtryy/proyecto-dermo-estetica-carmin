            <footer class="border-top pt-3 mt-4 text-center">
                <small class="text-muted">&copy; <?php echo date('Y'); ?> Sistema de Gestión Dermo Estética Carmin.</small>
            </footer>
        </main>
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
    <?php elseif ($controladorJS === 'citas'): ?>
        <script src="js/citas.js"></script>
    <?php elseif ($controladorJS === 'productos'): ?>
        <script src="js/productos.js"></script>
    <?php endif; ?>
</body>
</html>
