<?php require_once 'vista/layouts/header.php'; ?>

<div class="container py-2">
    <div class="mb-4">
        <h1 class="h3 page-title mb-1">Ayuda</h1>
        <p class="page-subtitle mb-0">Consulta y descarga el manual de usuario del sistema.</p>
    </div>

    <section class="card shadow-sm border-0">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 p-4">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-purple-strong" style="width: 3.5rem; height: 3.5rem;" aria-hidden="true">
                    <i class="fas fa-file-pdf fa-lg"></i>
                </span>
                <div>
                    <h2 class="h5 mb-1">Manual de usuario</h2>
                    <p class="text-muted mb-0">Guía de uso del sistema Dermo Estética Carmin (PDF).</p>
                </div>
            </div>
            <a class="btn btn-outline-purple" href="img/Manual_de_Usuario_Dermo_Estetica_Carmin.pdf" download>
                <i class="fas fa-download me-2" aria-hidden="true"></i>Descargar manual
            </a>
        </div>
    </section>
</div>

<?php require_once 'vista/layouts/footer.php'; ?>
