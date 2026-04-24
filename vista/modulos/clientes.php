
<?php require_once("vista/layouts/header.php"); ?>

<div class="container py-2">
	<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
		<button type="button" class="btn btn-primary" id="incluir">
			<i class="fas fa-user-plus me-1"></i>INCLUIR
		</button>
		<a href="index.php?c=login&amp;m=home" class="btn btn-outline-secondary">
			<i class="fas fa-arrow-left me-1"></i>REGRESAR
		</a>
	</div>

	<h1 class="h3 text-primary mb-3">Pantalla personal</h1>

	<div class="card shadow-sm border-0">
		<div class="card-header bg-white border-bottom py-3">
			<span class="fw-semibold text-secondary">Listado de personas</span>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table
					class="table table-striped table-hover table-bordered align-middle mb-0"
					id="tablausuarios"
					aria-describedby="tabla-personas-desc"
				>
					<caption id="tabla-personas-desc" class="caption-top px-3 pt-3 text-muted small">
						Listado actualizado al consultar desde el sistema.
					</caption>
					<thead class="table-light">
						<tr>
							<th scope="col" class="text-nowrap" style="width: 1%">Acciones</th>
							<th scope="col">Cédula</th>
							<th scope="col">Nombre</th>
							<th scope="col">Teléfono</th>
							<th scope="col">Dirección</th>
							<th scope="col" class="text-nowrap">Fecha nac.</th>
							<th scope="col" class="text-center text-nowrap">Género</th>
						</tr>
					</thead>
					<tbody id="resultadoconsulta"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Modal formulario persona (Bootstrap 5) -->
<div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="modal1Label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header text-bg-info">
				<h2 class="modal-title fs-5" id="modal1Label">Añadir cliente</h2>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				<form method="post" id="formulario_cliente" autocomplete="off">
					<input type="hidden" name="accion" id="accion" value="">
					<div class="row g-3">
						<div class="col-md-4">
							<label class="form-label" for="cedula">Cédula</label>
							<input class="form-control" type="text" id="cedula" name="cedula" autocomplete="off">
							<span class="form-text text-danger" id="scedula"></span>
						</div>
						<div class="col-md-8">
							<label class="form-label" for="nombres">Nombre completo</label>
							<input class="form-control" type="text" id="nombres" name="nombres" autocomplete="off">
							<span class="form-text text-danger" id="snombres"></span>
						</div>
					</div>
					<div class="row g-3 mt-0">
						<div class="col-md-4">
							<label class="form-label" for="fechadenacimiento">Fecha de nacimiento</label>
							<input class="form-control" type="date" id="fechadenacimiento" name="fechadenacimiento">
							<span class="form-text text-danger" id="sfechadenacimiento"></span>
						</div>
						<div class="col-md-8">
							<label class="form-label" for="direccion">Dirección</label>
							<input class="form-control" type="text" id="direccion" name="direccion" autocomplete="off">
							<span class="form-text text-danger" id="sdireccion"></span>
						</div>
					</div>
					<div class="row g-3 mt-0">
						<div class="col-md-4">
							<label class="form-label" for="telefono">Teléfono</label>
							<input class="form-control" type="text" id="telefono" name="telefono" autocomplete="off">
							<span class="form-text text-danger" id="stelefono"></span>
						</div>
						<div class="col-md-4">
							<span class="form-label d-block mb-1">Sexo</span>
							<div class="d-flex flex-wrap gap-3">
								<div class="form-check">
									<input class="form-check-input" type="radio" name="sexo" id="masculino" value="M">
									<label class="form-check-label" for="masculino">Masculino</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="sexo" id="femenino" value="F">
									<label class="form-check-label" for="femenino">Femenino</label>
								</div>
							</div>
							<span class="form-text text-danger" id="ssexo"></span>
						</div>
						
					</div>
				</form>
			</div>
			<div class="modal-footer bg-light">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-primary" id="proceso">INCLUIR</button>
			</div>
		</div>
	</div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>
