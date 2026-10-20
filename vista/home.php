<?php require_once('vista/layouts/header.php'); ?>
<style>
    body {
        background-color: #f3effb;
        background-image:
            radial-gradient(at 10% 10%, rgba(92, 45, 145, 0.35) 0, transparent 50%),
            radial-gradient(at 85% 25%, rgba(216, 180, 248, 0.5) 0, transparent 55%),
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%236b3fa0' fill-opacity='0.25' d='M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,149.3C672,149,768,203,864,224C960,245,1056,235,1152,208C1248,181,1344,139,1392,117.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3Cpath fill='%234a2373' fill-opacity='0.4' d='M0,224L60,208C120,192,240,160,360,165.3C480,171,600,213,720,229.3C840,245,960,235,1080,202.7C1200,171,1320,117,1380,90.7L1440,64L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z'%3E%3C/path%3E%3C/svg%3E"),
            linear-gradient(135deg, #431c68 0%, #8e55b5 45%, #e8dff5 100%);
        background-size: 100% 100%, 100% 100%, 100% auto, 100% 100%;
        background-position: top left, top right, bottom center, center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .home-page {
        min-height: calc(100vh - 170px);
    }

    .home-face {
        display: block;
        width: min(100%, 310px);
        height: 220px;
        object-fit: contain;
        flex: 0 0 auto;
    }

    @media (max-width: 767.98px) {
        .home-page {
            min-height: calc(100vh - 140px);
        }

        .home-face {
            width: 220px;
            height: 160px;
            align-self: center;
        }
    }
</style>
<div class="home-page">
<div class="container py-4">
    <div class="page-header p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="img/logo_dermoestetica.svg" alt="Dermo Estética Carmin" class="page-header-logo" />
                    <div>
                        <h1 class="h3 page-title mb-1">Panel de inicio</h1>
                        <p class="mb-0 page-subtitle">Resumen general del sistema</p>
                    </div>
                </div>
                <img src="img/cara-home.svg" alt="Rostro de Dermo Estética Carmin" class="home-face" width="310" height="220" decoding="async" fetchpriority="high" />
            </div>
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
                <div class="card-footer bg-transparent border-top pt-3 d-flex flex-wrap gap-2">
                    <a href="index.php?pagina=clientes" class="btn btn-sm btn-purple">Ver clientes</a>
                    <a href="index.php?pagina=clientes" class="btn btn-sm btn-outline-purple">Administrar clientes</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once('vista/layouts/footer.php'); ?>
