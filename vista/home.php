<?php require_once ('vista/layouts/header.php'); ?>
<style>
    .btn-cuadrado {
        width: 200px;
        height: 200px;
        border-radius: 15px;
        font-size: 1.2rem;
        font-weight: bold;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }
    
    .btn-cuadrado:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.4);
    }
    
    .btn-cuadrado i {
        font-size: 2.5rem;
    }
</style>
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
   
    <div class="d-flex justify-content-center gap-4 flex-wrap">
        <a href="index.php?c=servicios&m=index" class="btn btn-dark btn-cuadrado text-decoration-none">
            <i class="fas fa-coins"></i>
            Servicios
        </a>
        <a href="index.php?c=productos&m=index" class="btn btn-dark btn-cuadrado text-decoration-none">
            <i class="fas fa-receipt"></i>
            Productos
        </a>
    
        
        <?php if ($puede_gestionar_clientes): ?>
        <a href="index.php?c=clientes&m=index" class="btn btn-dark btn-cuadrado text-decoration-none">
            <i class="fas fa-users"></i>
            Gestionar clientes
        </a>
        <?php endif; ?>
        
        <?php if ($puede_gestionar_categorias): ?>
        <a href="index.php?c=citas&m=index" class="btn btn-dark btn-cuadrado text-decoration-none">
            <i class="fas fa-tags"></i>
            Gestionar citas
        </a>
        <?php endif; ?>
        
        
        
        <?php if ($puede_ver_analisis): ?>
        <a href="index.php?c=reportes&m=index" class="btn btn-dark btn-cuadrado text-decoration-none">
            <i class="fas fa-chart-line"></i>
            Reportes
        </a>
        <?php endif; ?>
      
 
        
        
    </div>
    
   
</div>

<?php require_once ('vista/layouts/footer.php'); ?>