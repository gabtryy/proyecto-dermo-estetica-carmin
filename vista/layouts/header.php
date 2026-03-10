
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Gastos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color:rgb(66, 102, 0);">
    <?php ?>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a class="navbar-brand me-3" href="index.php?c=login&m=home">Sistema de Gastos</a>
                
                <a href="index.php?c=gastos&m=index" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-receipt"></i> servicios
                </a>
                
                <a href="index.php?c=presupuesto&m=index" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-wallet"></i> productos
                </a>
                
                <a href="index.php?c=reportes&m=index" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
                
                <?php if ($puede_gestionar_usuarios): ?>
                    <a href="index.php?c=usuario&m=index" class="btn btn-outline-light btn-sm me-2">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                <?php endif; ?>
                
                <?php if ($puede_gestionar_categorias): ?>
                    <a href="index.php?c=categorias&m=index" class="btn btn-outline-light btn-sm me-2">
                        <i class="fas fa-tags"></i> citas
                    </a>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="text-white me-3">
                        Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>
                        
                    </span>
                <?php endif; ?>
                <a href="index.php?c=login&m=logout" class="btn btn-outline-light">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    <div class="container">