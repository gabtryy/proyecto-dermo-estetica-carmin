<?php
$paginaActual = $pagina ?? 'home';
$rolUsuario = $_SESSION['nivel'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($paginaActual, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fontawesome/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/layout.css">
    <?php if ($paginaActual === 'home'): ?>
        <link rel="stylesheet" href="css/home.css">
    <?php endif; ?>
</head>
<body class="pagina-modulo">
<div class="app-layout d-flex">
    <nav class="sidebar offcanvas-lg offcanvas-start d-flex flex-column flex-shrink-0 p-3 bg-white" id="sideNavigation" tabindex="-1" aria-label="Navegación principal">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="index.php?pagina=home" class="sidebar-brand d-flex align-items-center gap-2 text-decoration-none">
                <img src="img/logo_dermoestetica.svg" alt="Dermo Estética Carmin" class="sidebar-logo" />
                <span class="fs-5 fw-bold">Dermo Estética</span>
            </a>
            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" aria-label="Cerrar navegación"></button>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <?php if (in_array($rolUsuario, [1, 2], true)): ?>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=home">
                    <i class="fas fa-house me-2"></i>
                    Inicio
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=servicios">
                    <i class="fas fa-spa me-2"></i>
                    Servicios
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=productos">
                    <i class="fas fa-pump-soap me-2"></i>
                    Productos
                </a>
            </li>
            <?php if (in_array($rolUsuario, [1, 2], true)): ?>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=ventas">
                    <i class="fas fa-cash-register me-2"></i>
                    Ventas
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=reportes">
                    <i class="fas fa-file-pdf me-2"></i>
                    Reportes
                </a>
            </li>
            <?php endif; ?>
            <?php if ($rolUsuario === 1): ?>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=proveedores">
                    <i class="fas fa-truck me-2"></i>
                    Proveedores
                </a>
            </li>
            <?php endif; ?>
            <?php if (in_array($rolUsuario, [1, 2], true)): ?>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=antecedentes">
                    <i class="fas fa-notes-medical me-2"></i>
                    Antecedentes
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=clientes">
                    <i class="fas fa-user-friends me-2"></i>
                    Clientes
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=esteticistas">
                    <i class="fas fa-user-tie me-2"></i>
                    Esteticistas
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=citas">
                    <i class="fas fa-calendar-check me-2"></i>
                    Citas
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=diagnostico">
                    <i class="fas fa-face-smile me-2"></i>
                    Diagnóstico
                </a>
            </li>
            <?php endif; ?>
            <?php if ($rolUsuario === 1): ?>
            <li class="nav-item mb-1">
                <a class="btn btn-outline-purple w-100 text-start" href="index.php?pagina=configuracion">
                    <i class="fas fa-cog me-2"></i>
                    Configuración
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="app-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded-3 mb-4">
            <div class="container-fluid px-3 px-lg-4">
                <a class="navbar-brand fw-semibold text-purple-strong d-lg-none d-flex align-items-center gap-2" href="#">
                    <img src="img/logo_dermoestetica.svg" alt="Dermo Estética Carmin" class="navbar-logo" />
                    <span>Dermo Estética</span>
                </a>
                <button class="navbar-toggler border-0 d-lg-none ms-auto me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideNavigation" aria-controls="sideNavigation" aria-label="Abrir navegación">
                    <i class="fas fa-bars text-purple-strong" aria-hidden="true"></i>
                </button>
                <div class="navbar-center d-none d-lg-flex align-items-center gap-2">
                    <img src="img/logo_dermoestetica.svg" alt="Dermo Estética Carmin" class="navbar-logo" />
                    <span>Sistema de Gestión Dermo Estética Carmin</span>
                </div>
                <div class="d-flex align-items-center gap-2 ms-lg-auto">
                    <span class="text-muted small d-none d-sm-inline">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars((string) ($_SESSION['usuario'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <a class="btn btn-outline-purple btn-sm" href="index.php?pagina=salida">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar sesión
                    </a>
                </div>
            </div>
        </nav>
        <main class="container-fluid py-4">
