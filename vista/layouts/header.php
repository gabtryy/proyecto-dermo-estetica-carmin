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
           --green-1: #42d6d6;
        --green-2: #119797;
        --green-3: #22a59a;
        --green-4: #3dd9ee;
        --tile-top: #4ad5df;
        --tile-bottom: #66d4cb;
        --tile-text: #133322;
            --app-accent: var(--green-1);
            --app-accent-soft: rgba(66, 214, 116, 0.18);
            --app-navbar: var(--green-1);
        }
        body.app-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, var(--green-4) 0%, #F7FFF7 100%);
            color: #21322a;
        }
        .app-navbar {
            background: linear-gradient(90deg, var(--green-1), var(--green-2)) !important;
            box-shadow: 0 2px 0 rgba(0,0,0,0.06), 0 8px 24px rgba(0, 0, 0, 0.08);
            padding-top: 0.55rem;
            padding-bottom: 0.55rem;
        }
        .app-navbar .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.02em;
            color: var(--tile-text) !important;
        }
        .app-navbar .nav-link {
            color: rgba(19, 51, 34, 0.95) !important;
            font-weight: 600;
            padding: 0.45rem 0.75rem !important;
            border-radius: 0.375rem;
            transition: color 0.15s ease, background-color 0.15s ease, transform 0.12s ease;
        }
        .app-navbar .nav-link:hover,
        .app-navbar .nav-link:focus {
            color: rgba(19, 51, 34, 1) !important;
            background-color: rgba(255, 255, 255, 0.06);
            transform: translateY(-1px);
        }
        .app-navbar .nav-link.active {
            color: rgba(19, 51, 34, 1) !important;
            background-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .app-navbar .nav-link i {
            opacity: 0.9;
        }
        .app-navbar .user-pill {
            font-size: 0.875rem;
            color: rgba(19, 51, 34, 0.8);
        }
        .app-navbar .user-pill strong {
            color: var(--tile-text);
            font-weight: 600;
        }
        .app-navbar .btn-logout {
            --bs-btn-color: var(--tile-text);
            --bs-btn-border-color: rgba(19, 51, 34, 0.12);
            --bs-btn-hover-bg: rgba(0, 0, 0, 0.04);
            --bs-btn-hover-border-color: rgba(19, 51, 34, 0.2);
            --bs-btn-active-bg: rgba(0, 0, 0, 0.06);
            font-weight: 500;
            padding: 0.35rem 0.85rem;
        }
        .app-navbar .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.08);
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
                    <?php if ($puede_gestionar_esteticista): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?c=esteticistas&amp;m=index"><i class="fas fa-user-tie me-1"></i>Esteticistas</a>
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
                                Bienvenido, <strong><?= htmlspecialchars($_SESSION['cedula'], ENT_QUOTES, 'UTF-8') ?></strong>
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
