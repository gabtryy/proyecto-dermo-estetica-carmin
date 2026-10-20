<?php
$paginaActual = $pagina ?? 'home';
$modulosSidebar = [
    ['pagina' => 'home',         'icono' => 'fa-house',           'label' => 'Inicio'],
    ['pagina' => 'servicios',    'icono' => 'fa-spa',             'label' => 'Servicios'],
    ['pagina' => 'productos',    'icono' => 'fa-pump-soap',       'label' => 'Productos'],
    ['pagina' => 'ventas',       'icono' => 'fa-cash-register',   'label' => 'Ventas'],
    ['pagina' => 'proveedores',  'icono' => 'fa-truck',           'label' => 'Proveedores'],
    ['pagina' => 'antecedentes', 'icono' => 'fa-notes-medical',   'label' => 'Antecedentes'],
    ['pagina' => 'clientes',     'icono' => 'fa-user-friends',    'label' => 'Clientes'],
    ['pagina' => 'esteticistas', 'icono' => 'fa-user-tie',        'label' => 'Esteticistas'],
    ['pagina' => 'citas',        'icono' => 'fa-calendar-check',  'label' => 'Citas'],
    ['pagina' => 'diagnostico',  'icono' => 'fa-face-smile',      'label' => 'Diagnóstico'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($paginaActual, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/fontawesome/css/all.min.css">
    <?php if ($paginaActual !== 'home'): ?>
        <link rel="stylesheet" href="vendor/datatables/css/dataTables.bootstrap5.min.css">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple: #6b2d86;
            --purple-soft: #f4ecff;
            --purple-strong: #38174d;
            --purple-muted: #7d559d;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(180deg, #faf2ff 0%, #8f6dbe 100%);
            color: #2e1a45;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            flex: 0 0 260px;
            background: #ffffff;
            border-right: 1px solid rgba(107, 45, 134, 0.16);
            box-shadow: 4px 0 30px rgba(107, 45, 134, 0.08);
        }
        .sidebar .sidebar-brand {
            color: var(--purple-strong);
            font-size: 1.15rem;
            letter-spacing: 0.02em;
        }
        .sidebar-logo,
        .navbar-logo,
        .page-header-logo {
            display: block;
            width: auto;
            max-height: 42px;
            background: transparent;
            filter: none;
        }
        .navbar-logo {
            max-height: 34px;
        }
        .page-header-logo {
            max-height: 52px;
        }
        .sidebar .btn-outline-purple {
            color: var(--purple-strong);
            border-color: var(--purple-soft);
            background-color: transparent;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .sidebar .btn-outline-purple:hover,
        .sidebar .btn-outline-purple:focus {
            color: #ffffff;
            background-color: var(--purple);
            border-color: var(--purple);
            box-shadow: 0 12px 24px rgba(107, 45, 134, 0.12);
        }
        .sidebar .btn-outline-purple.active {
            color: #ffffff;
            background-color: var(--purple-strong);
            border-color: var(--purple-strong);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        .sidebar hr {
            border-color: rgba(107, 45, 134, 0.12);
        }
        .sidebar .nav-item {
            margin-bottom: 0.65rem;
        }
        .sidebar .btn {
            text-align: left;
            justify-content: flex-start;
            border-radius: 1rem;
            padding: 0.95rem 1.1rem;
        }
        .sidebar .btn i {
            width: 1.25rem;
        }
        .sidebar .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(107, 45, 134, 0.16);
        }
        .page-header {
            background: #ffffff;
            border-radius: 1.2rem;
            box-shadow: 0 18px 45px rgba(107, 45, 134, 0.08);
            border: 1px solid rgba(107, 45, 134, 0.08);
        }
        .page-title {
            color: var(--purple-strong);
        }
        .page-subtitle {
            color: var(--purple-muted);
        }
        .stat-icon {
            width: 72px;
            height: 72px;
            min-width: 72px;
            border-radius: 1rem;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--purple), var(--purple-strong));
            color: #ffffff;
        }
        .btn-purple {
            color: #ffffff;
            background-color: var(--purple);
            border-color: var(--purple);
        }
        .btn-purple:hover,
        .btn-purple:focus {
            color: #ffffff;
            background-color: var(--purple-strong);
            border-color: var(--purple-strong);
        }
        .bg-purple {
            background-color: var(--purple) !important;
            color: #ffffff !important;
        }
        .text-purple-strong {
            color: var(--purple-strong) !important;
        }
        .container-fluid {
            position: relative;
        }
        .flex-grow-1,
        main {
            min-width: 0;
        }
        .navbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Montserrat', sans-serif;
            font-size: 1.125rem; /* 18px */
            font-weight: 600;
            color: var(--purple-strong);
            text-align: center;
            white-space: nowrap;
        }
        .dataTables_wrapper {
            width: 100%;
            min-width: 0;
        }
        .dataTables_wrapper > .row {
            align-items: center;
            row-gap: 0.75rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            max-width: min(100%, 15rem);
        }
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }
        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 1rem);
        }
        .modal-dialog-scrollable .modal-body {
            min-height: 0;
            max-height: calc(100vh - 10rem);
            overflow-y: auto;
        }
        .pagina-formularios .modal-content {
            overflow: hidden;
            border: 1px solid rgba(56, 23, 77, 0.1);
            border-radius: 1rem;
            box-shadow: 0 1.25rem 3.5rem rgba(39, 18, 55, 0.2);
        }
        .pagina-formularios .modal-header {
            padding: 1.1rem 1.4rem;
            color: #ffffff;
            background: linear-gradient(120deg, var(--purple), var(--purple-strong));
            border-bottom: 0;
        }
        .pagina-formularios .modal-header.text-bg-info {
            color: #ffffff !important;
            background: linear-gradient(120deg, var(--purple), var(--purple-strong)) !important;
        }
        .pagina-formularios .modal-title {
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .pagina-formularios .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.85;
        }
        .pagina-formularios .modal-header .btn-close:hover {
            opacity: 1;
        }
        .pagina-formularios .modal-body {
            padding: 1.5rem;
            background: #ffffff;
        }
        .pagina-formularios .modal-body .form-label {
            margin-bottom: 0.45rem;
            color: var(--purple-strong);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .pagina-formularios .modal-body .form-control,
        .pagina-formularios .modal-body .form-select {
            min-height: 2.75rem;
            border-color: #ded4e8;
            border-radius: 0.65rem;
            background-color: #ffffff;
        }
        .pagina-formularios .modal-body textarea.form-control {
            min-height: auto;
        }
        .pagina-formularios .modal-body .form-control:focus,
        .pagina-formularios .modal-body .form-select:focus {
            border-color: var(--purple-muted);
            box-shadow: 0 0 0 0.2rem rgba(107, 45, 134, 0.14);
        }
        .pagina-formularios .modal-body .border.rounded.bg-light,
        .pagina-formularios .modal-body .border.rounded.p-3.bg-light {
            border-color: #e8dff0 !important;
            border-radius: 0.75rem !important;
            background-color: #faf7fd !important;
        }
        .pagina-formularios .modal-footer {
            gap: 0.55rem;
            padding: 1rem 1.4rem;
            background: #faf8fc !important;
            border-top: 1px solid #eee8f3;
        }
        .pagina-formularios .btn {
            border-radius: 0.65rem;
            font-weight: 500;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }
        .pagina-formularios .btn-primary {
            color: #ffffff;
            background-color: var(--purple);
            border-color: var(--purple);
        }
        .pagina-formularios .btn-primary:hover,
        .pagina-formularios .btn-primary:focus {
            color: #ffffff;
            background-color: var(--purple-strong);
            border-color: var(--purple-strong);
            box-shadow: 0 0.35rem 0.9rem rgba(56, 23, 77, 0.18);
        }
        .pagina-formularios .btn-outline-primary {
            color: var(--purple);
            border-color: var(--purple);
            background-color: transparent;
        }
        .pagina-formularios .btn-outline-primary:hover,
        .pagina-formularios .btn-outline-primary:focus {
            color: #ffffff;
            background-color: var(--purple);
            border-color: var(--purple);
        }
        .pagina-formularios .btn:focus-visible {
            box-shadow: 0 0 0 0.2rem rgba(107, 45, 134, 0.2);
        }
        @media (max-width: 575.98px) {
            .pagina-formularios .modal-body {
                padding: 1.1rem;
            }
            .pagina-formularios .modal-header,
            .pagina-formularios .modal-footer {
                padding-right: 1.1rem;
                padding-left: 1.1rem;
            }
        }
        @media (max-width: 991.98px) {
            .sidebar {
                --bs-offcanvas-width: min(280px, 85vw);
                min-height: 100vh;
                border: 0;
                border-right: 1px solid rgba(107, 45, 134, 0.16);
            }
            .navbar-center {
                display: none !important;
            }
        }
        @media (max-width: 767.98px) {
            main.container-fluid {
                padding: 1rem 0.75rem !important;
            }
            .page-header {
                padding: 1.25rem !important;
            }
            .card-body {
                min-width: 0;
            }
            .modal-dialog {
                margin: 0.5rem;
            }
            .modal-content {
                max-height: calc(100vh - 1rem);
            }
            .modal-body {
                overflow-x: hidden;
            }
        }
        @media (max-width: 575.98px) {
            .navbar {
                margin-right: 0.5rem;
                margin-left: 0.5rem;
            }
            .navbar .container-fluid {
                padding-right: 0.75rem !important;
                padding-left: 0.75rem !important;
            }
            .navbar-brand {
                margin-right: 0.25rem;
                font-size: 1rem;
            }
            .navbar-logo {
                max-height: 30px;
            }
            .navbar .btn {
                white-space: nowrap;
            }
            .modal-footer .btn {
                flex: 1 1 auto;
            }
        }
    </style>
</head>
<body class="<?= in_array($paginaActual, ['productos', 'servicios', 'proveedores', 'esteticistas', 'antecedentes'], true) ? 'pagina-formularios' : '' ?>">
<div class="d-flex">
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
            <?php foreach ($modulosSidebar as $modulo): ?>
                <?php
                $activo = ($paginaActual === $modulo['pagina']) ? ' active' : '';
                $href = 'index.php?pagina=' . urlencode($modulo['pagina']);
                ?>
                <li class="nav-item mb-1">
                    <a class="btn btn-outline-purple w-100 text-start <?= $activo ?>" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"<?php if ($activo): ?> aria-current="page"<?php endif; ?>>
                        <i class="fas <?= htmlspecialchars($modulo['icono'], ENT_QUOTES, 'UTF-8') ?> me-2"></i>
                        <?= htmlspecialchars($modulo['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="flex-grow-1">
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
                    <button class="btn btn-purple btn-sm" type="button"><i class="fas fa-user me-1"></i>admin</button>
                </div>
            </div>
        </nav>
        <main class="container-fluid py-4">
