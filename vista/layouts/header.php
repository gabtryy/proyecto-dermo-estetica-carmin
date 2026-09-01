<?php
$paginaActual = $pagina ?? 'home';
$modulosSidebar = [
    ['pagina' => 'home',         'icono' => 'fa-house',           'label' => 'Inicio'],
    ['pagina' => 'servicios',    'icono' => 'fa-spa',             'label' => 'Servicios'],
    ['pagina' => 'productos',    'icono' => 'fa-pump-soap',       'label' => 'Productos'],
    ['pagina' => 'proveedores',    'icono' => 'fa-truck',       'label' => 'Proveedores'],
    ['pagina' => 'clientes',     'icono' => 'fa-user-friends',    'label' => 'Clientes'],
    ['pagina' => 'esteticistas', 'icono' => 'fa-user-tie',        'label' => 'Esteticistas'],
    ['pagina' => 'citas',        'icono' => 'fa-calendar-check',  'label' => 'Citas'],
    ['pagina' => 'reportes',     'icono' => 'fa-chart-line',      'label' => 'Diagnóstico'],
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
    <link rel="stylesheet" href="vendor/datatables/css/dataTables.bootstrap5.min.css">
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
        }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid rgba(107, 45, 134, 0.16);
            box-shadow: 4px 0 30px rgba(107, 45, 134, 0.08);
        }
        .sidebar .sidebar-brand {
            color: var(--purple-strong);
            font-size: 1.15rem;
            letter-spacing: 0.02em;
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
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-white">
        <a href="index.php" class="sidebar-brand d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
            <span class="fs-5 fw-bold">Dermo Estética</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <?php foreach ($modulosSidebar as $modulo): ?>
                <?php
                $activo = ($paginaActual === $modulo['pagina']) ? ' active' : '';
                $href = ($modulo['pagina'] === 'home')
                    ? 'index.php'
                    : 'index.php?pagina=' . urlencode($modulo['pagina']);
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
            <div class="container-fluid px-4">
                <a class="navbar-brand fw-semibold text-purple-strong d-lg-none" href="#">Dermo Estética</a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Mostrar navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="navbar-center d-none d-lg-block">Sistema de Gestión Dermo Estética Carmin</div>
                <div class="collapse navbar-collapse " id="topNavbar" >
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                      
                        <li class="nav-item">
                            <a class="nav-link active d-lg-none" aria-current="page" href="#">Sistema de Gestión Dermo Estética Carmin</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">

                        <button class="btn btn-purple btn-sm" type="button"><i class="fas fa-user me-1"></i>admin</button>
                    </div>
                </div>
            </div>
        </nav>
        <main class="container-fluid py-4">

