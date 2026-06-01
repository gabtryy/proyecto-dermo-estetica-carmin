</div> 
    <footer class="bg-dark text-white text-center py-3 mt-4">
        &copy; <?php echo date('Y'); ?> Sistema de Gestión dermon estetica carmin. 
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    
    <?php $scriptPage = $pagina ?? ''; ?>
    <?php if ($scriptPage === 'clientes'): ?>
        <script src="js/cliente.js"></script>
    <?php elseif ($scriptPage === 'esteticistas'): ?>
        <script src="js/esteticista.js"></script>
    <?php elseif ($scriptPage === 'servicios'): ?>
        <script src="js/servicio.js"></script>
    <?php endif; ?>>
  
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>