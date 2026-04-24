<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pagina, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --app-accent: #5a7a2a;
            --app-accent-soft: rgba(90, 122, 42, 0.35);
            --app-navbar: #1a1d21;
        }
        body.app-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg,rgb(129, 198, 255) 48%, #ebece9 100%);
            color: #212529;
        }
        .app-navbar {
            background: var(--app-navbar) !important;
            box-shadow: 0 1px 0 var(--app-accent-soft), 0 8px 24px rgba(0, 0, 0, 0.12);
            padding-top: 0.55rem;
            padding-bottom: 0.55rem;
        }
        .app-navbar .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.02em;
            color: #fff !important;
        }
        .app-navbar .nav-link {
            color: rgba(255, 255, 255, 0.72) !important;
            font-weight: 500;
            padding: 0.45rem 0.75rem !important;
            border-radius: 0.375rem;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .app-navbar .nav-link:hover,
        .app-navbar .nav-link:focus {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.08);
        }
        .app-navbar .nav-link.active {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.12);
        }
        .app-navbar .nav-link i {
            opacity: 0.9;
        }
        .app-navbar .user-pill {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.75);
        }
        .app-navbar .user-pill strong {
            color: #fff;
            font-weight: 600;
        }
        .app-navbar .btn-logout {
            --bs-btn-color: #fff;
            --bs-btn-border-color: rgba(255, 255, 255, 0.35);
            --bs-btn-hover-bg: rgba(255, 255, 255, 0.12);
            --bs-btn-hover-border-color: rgba(255, 255, 255, 0.55);
            --bs-btn-active-bg: rgba(255, 255, 255, 0.18);
            font-weight: 500;
            padding: 0.35rem 0.85rem;
        }
        .app-navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.35);
        }
        .app-main-wrap {
            flex: 1 0 auto;
        }
    </style>
</head>
<body class="app-shell">
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand" href="index.php?c=login&amp;m=home">Dermo Estética</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appMainNav" aria-controls="appMainNav" aria-expanded="false" aria-label="Menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="appMainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=servicios&amp;m=index"><i class="fas fa-spa me-1"></i>Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=productos&amp;m=index"><i class="fas fa-pump-soap me-1"></i>Productos</a>
                    </li>
                    <?php if ($puede_ver_reportes): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=reportes&amp;m=index"><i class="fas fa-chart-line me-1"></i>Reportes</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($puede_gestionar_clientes): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=clientes&amp;m=index"><i class="fas fa-user-friends me-1"></i>Clientes</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($es_admin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=usuario&amp;m=listar"><i class="fas fa-users-cog me-1"></i>Usuarios</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($puede_gestionar_categorias): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=citas&amp;m=index"><i class="fas fa-calendar-check me-1"></i>Citas</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 ms-lg-2">
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="user-pill px-lg-2">
                            Bienvenido, <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </span>
                    <?php endif; ?>
                    <a href="index.php?c=login&amp;m=logout" class="btn btn-outline-light btn-sm btn-logout">
                        <i class="fas fa-right-from-bracket me-1"></i>Cerrar sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container app-main-wrap py-4">
