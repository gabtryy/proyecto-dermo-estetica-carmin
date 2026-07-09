<?php require_once('vista/layouts/header.php'); ?>
<div class="container py-4">
    <div class="page-header p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 page-title mb-1">Panel de inicio</h1>
                <p class="mb-0 page-subtitle">Resumen general del sistema</p>
            </div>
            <a href="index.php?pagina=clientes" class="btn btn-purple btn-lg">Ver clientes</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="h3 mb-1"><?= (int) ($totalClientes ?? 0) ?></h2>
                        <p class="mb-1 text-muted">Clientes registrados</p>
                        <small class="text-muted">Actualizado recientemente</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top pt-3">
                    <a href="index.php?pagina=clientes" class="btn btn-sm btn-outline-purple">Administrar clientes</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('vista/layouts/footer.php'); ?>
