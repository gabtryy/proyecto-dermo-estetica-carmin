<?php require_once ('vista/layouts/header.php'); ?>
<style>
    :root{
        --green-1: #42d6d6;
        --green-2: #119797;
        --green-3: #3bc5dd;
        --green-4: #dbeff0;
        --tile-top: #4ad5df;
        --tile-bottom: #66d4cb;
        --tile-text: #133322;
    }
    .tiles-wrap { padding-top: 3.5rem; padding-bottom: 3.5rem; }
    .tiles-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2.5rem; width: 100%; max-width: 1200px; margin: 0 auto; align-items: stretch; }

    /* Estilo unificado para todas las tarjetas */
    .btn-cuadrado {
        position: relative;
        overflow: hidden;
        height: 200px;
        border-radius: 14px;
        color: var(--tile-text);
        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:center;
        gap:12px;
        font-weight:700;
        text-decoration:none;
        background: linear-gradient(180deg, var(--tile-top) 0%, var(--tile-bottom) 100%);
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 12px 28px rgba(6, 30, 18, 0.08);
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }

    /* brillo sutil superior */
    .btn-cuadrado::after{
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 14px;
        background: linear-gradient(180deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0) 30%);
        pointer-events: none;
        mix-blend-mode: overlay;
    }

    .btn-cuadrado i { font-size: 2.4rem; color: var(--tile-text); opacity:0.95; }
    .btn-cuadrado span { font-size: 1rem; margin-top:6px; color: var(--tile-text); }

    .btn-cuadrado:hover{ transform: translateY(-6px); box-shadow:0 22px 48px rgba(6,30,18,0.14); filter:brightness(1.02);} 

    /* Asegurar aspecto idéntico por si alguna tile-# está presente */
    .tile-1, .tile-2, .tile-3, .tile-4, .tile-5, .tile-6 {
        background: linear-gradient(180deg, var(--tile-top) 0%, var(--tile-bottom) 100%) !important;
        color: var(--tile-text) !important;
    }

    @media (max-width:768px){ .btn-cuadrado{ height:170px; } }
</style>
<div class="tiles-wrap d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
   
    <div class="tiles-grid">
        <a href="index.php?c=servicios&m=index" class="btn-cuadrado tile-1 text-decoration-none" role="button">
            <i class="fas fa-coins"></i>
            Servicios
        </a>
        <a href="index.php?c=productos&m=index" class="btn-cuadrado tile-2 text-decoration-none" role="button">
            <i class="fas fa-receipt"></i>
            Productos
        </a>
    
        
        <?php if ($puede_gestionar_clientes): ?>
        <a href="index.php?c=clientes&m=index" class="btn-cuadrado tile-3 text-decoration-none" role="button">
            <i class="fas fa-users"></i>
            Gestionar clientes
        </a>
        <?php endif; ?>
        
         <?php if ($puede_gestionar_esteticista): ?>
         <a href="index.php?c=esteticistas&m=index" class="btn-cuadrado tile-4 text-decoration-none" role="button">
            <i class="fas fa-user-tie"></i>
            Esteticistas
        </a>
        <?php endif; ?>
        
        <?php if ($puede_gestionar_categorias): ?>
        <a href="index.php?c=citas&m=index" class="btn-cuadrado tile-5 text-decoration-none" role="button">
            <i class="fas fa-tags"></i>
            Gestionar citas
        </a>
        <?php endif; ?>
        
        
        
        <?php if ($puede_ver_analisis): ?>
        <a href="index.php?c=reportes&m=index" class="btn-cuadrado tile-6 text-decoration-none" role="button">
            <i class="fas fa-chart-line"></i>
            diagnostico
        </a>
        <?php endif; ?>
      
 
        
        
    </div>
    
   
</div>

<?php require_once ('vista/layouts/footer.php'); ?>