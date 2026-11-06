<?php require_once('vista/layouts/header.php'); ?>
<?php
$maxCitasDia = max(array_column($diasSemana, 'total'));
$primerDiaSemana = new DateTimeImmutable($diasSemana[0]['fecha']);
$ultimoDiaSemana = new DateTimeImmutable($diasSemana[6]['fecha']);
?>
<div class="home-page">
    <div class="container py-2">
        <section class="page-header home-hero mb-4" aria-labelledby="home-title">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="home-hero-content">
                    <p class="home-eyebrow mb-2"><?= htmlspecialchars($fechaHoyTexto, ENT_QUOTES, 'UTF-8') ?></p>
                    <h1 class="page-title mb-2" id="home-title">Un día para cuidar y hacer brillar</h1>
                    <p class="page-subtitle mb-4">Tu espacio de trabajo está listo. Revisa las citas, organiza tu agenda y ofrece a cada cliente una experiencia especial.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="index.php?pagina=citas" class="btn btn-light">
                            <i class="fas fa-calendar-plus me-2" aria-hidden="true"></i>Gestionar citas
                        </a>
                        <a href="index.php?pagina=clientes" class="btn btn-outline-light">
                            <i class="fas fa-user-friends me-2" aria-hidden="true"></i>Ver clientes
                        </a>
                    </div>
                </div>
                <img src="img/cara-home.svg" alt="" class="home-face" width="250" height="190" decoding="async" fetchpriority="high">
            </div>
        </section>

        <section class="row g-3 mb-4" aria-label="Resumen de actividad">
            <div class="col-xl-4 col-md-6">
                <a href="index.php?pagina=clientes" class="card home-stat-card text-decoration-none">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <span class="home-stat-icon clients"><i class="fas fa-users" aria-hidden="true"></i></span>
                        <div>
                            <div class="home-stat-number"><?= (int) $totalClientes ?></div>
                            <p class="home-stat-label">Clientes que confían en ti</p>
                        </div>
                        <i class="fas fa-arrow-up-right-from-square ms-auto text-muted small" aria-hidden="true"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-md-6">
                <a href="index.php?pagina=citas" class="card home-stat-card text-decoration-none">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <span class="home-stat-icon today"><i class="fas fa-calendar-day" aria-hidden="true"></i></span>
                        <div>
                            <div class="home-stat-number"><?= count($citasHoy) ?></div>
                            <p class="home-stat-label">Citas programadas para hoy</p>
                        </div>
                        <i class="fas fa-arrow-up-right-from-square ms-auto text-muted small" aria-hidden="true"></i>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="card home-stat-card">
                    <div class="card-body d-flex align-items-center gap-3 p-4">
                        <span class="home-stat-icon week"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                        <div>
                            <div class="home-stat-number"><?= $totalCitasSemana ?></div>
                            <p class="home-stat-label">Citas de esta semana</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-3 mb-4" aria-label="Agenda y actividad semanal">
            <div class="col-xl-7">
                <div class="card home-panel">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                            <div>
                                <h2 class="home-panel-title">Tu semana, de un vistazo</h2>
                                <p class="home-panel-subtitle">
                                    Citas del <?= $primerDiaSemana->format('j') ?>
                                    de <?= htmlspecialchars($meses[(int) $primerDiaSemana->format('n')], ENT_QUOTES, 'UTF-8') ?>
                                    al <?= $ultimoDiaSemana->format('j') ?>
                                    de <?= htmlspecialchars($meses[(int) $ultimoDiaSemana->format('n')], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                            <span class="badge rounded-pill text-bg-light border px-3 py-2"><?= $totalCitasSemana ?> en total</span>
                        </div>

                        <div class="home-chart" role="img" aria-label="Gráfica de citas por día durante esta semana">
                            <?php foreach ($diasSemana as $dia): ?>
                                <?php
                                $altura = $maxCitasDia > 0
                                    ? max(4, (int) round(($dia['total'] / $maxCitasDia) * 100))
                                    : 4;
                                $esHoy = $dia['fecha'] === $hoy->format('Y-m-d');
                                ?>
                                <div class="home-chart-day<?= $esHoy ? ' is-today' : '' ?>" aria-label="<?= htmlspecialchars($dia['nombre'], ENT_QUOTES, 'UTF-8') ?>: <?= (int) $dia['total'] ?> citas">
                                    <span class="home-chart-count"><?= (int) $dia['total'] ?></span>
                                    <span class="home-chart-track" aria-hidden="true">
                                        <span class="home-chart-bar" style="height: <?= $altura ?>%"></span>
                                    </span>
                                    <span class="home-chart-label"><?= htmlspecialchars($dia['nombre'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card home-panel">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                                <h2 class="home-panel-title">Agenda de hoy</h2>
                                <p class="home-panel-subtitle">Un vistazo a las personas que te visitan</p>
                            </div>
                            <span class="home-stat-icon today" style="width: 42px; height: 42px; min-width: 42px; font-size: 1rem;">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                            </span>
                        </div>
                        <?php if ($citasHoy): ?>
                            <ul class="home-agenda-list">
                                <?php foreach ($citasHoy as $cita): ?>
                                    <li class="home-agenda-item">
                                        <time class="home-agenda-time" datetime="<?= htmlspecialchars($cita['hora'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars(substr($cita['hora'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                                        </time>
                                        <div class="min-w-0">
                                            <div class="home-agenda-name"><?= htmlspecialchars($cita['nombreCliente'] ?: 'Cliente sin nombre', ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="home-agenda-detail">
                                                <?= htmlspecialchars($cita['nombreEsteticista'] ?: 'Profesional por asignar', ENT_QUOTES, 'UTF-8') ?>
                                                <?php if (!empty($cita['estado_cita'])): ?>
                                                    · <?= htmlspecialchars(ucfirst($cita['estado_cita']), ENT_QUOTES, 'UTF-8') ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="home-empty">
                                <div><i class="far fa-calendar-check d-block" aria-hidden="true"></i></div>
                                <strong>Tu agenda está libre por ahora</strong>
                                <span class="small mt-1">Aprovecha para preparar tu espacio o agendar una visita.</span>
                            </div>
                        <?php endif; ?>
                        <a href="index.php?pagina=citas" class="btn btn-outline-purple btn-sm w-100 mt-3">Ir a todas las citas</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-rate p-3 p-md-4 mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" aria-labelledby="home-rate-title">
            <div class="d-flex align-items-center gap-3">
                <span class="home-stat-icon clients"><i class="fas fa-coins" aria-hidden="true"></i></span>
                <div>
                    <h2 class="home-panel-title" id="home-rate-title">¿Actualizaste la tasa de hoy?</h2>
                    <p class="home-panel-subtitle">Mantén tus precios y ventas al día con la tasa vigente.</p>
                </div>
            </div>
            <button type="button" class="btn btn-purple" data-bs-toggle="modal" data-bs-target="#modalTasaDia">
                <i class="fas fa-plus me-1" aria-hidden="true"></i>Añadir tasa de hoy
            </button>
        </section>
    </div>
</div>

<div class="modal fade" id="modalTasaDia" tabindex="-1" aria-labelledby="modalTasaDiaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-purple">
                <h2 class="modal-title fs-5" id="modalTasaDiaLabel">Registrar tasa del día</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formTasaDia" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="guardarTasa">
                    <div class="mb-3">
                        <label class="form-label" for="monedaTasaDia">Moneda</label>
                        <select class="form-select" id="monedaTasaDia" name="id_moneda" required disabled>
                            <option value="">Cargando monedas...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="fechaTasaDia">Fecha</label>
                        <input class="form-control" type="date" id="fechaTasaDia" name="fecha_tasa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="montoTasaDia">Tasa por cada $1</label>
                        <input class="form-control" type="number" id="montoTasaDia" name="monto_tasa" min="0.0001" max="999999.9999" step="0.0001" required>
                        <div class="form-text">Indica cuántos bolívares equivalen a un dólar. Las tasas anteriores se conservarán.</div>
                    </div>
                    <div id="mensajeTasaDia" class="alert d-none mb-0" role="status" aria-live="polite"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple" id="guardarTasaDia">Guardar tasa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('vista/layouts/footer.php'); ?>
