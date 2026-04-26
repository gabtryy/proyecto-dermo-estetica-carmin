<?php require_once("vista/layouts/header.php"); ?>
<style>
	body.app-shell {
		background: linear-gradient(180deg, #b3e6ff 60%, #fff 100%) !important;
		color: #222;
	}
	.celeste-card {
		background: #e6f7ff;
		border: 1px solid #b3e6ff;
		border-radius: 1rem;
		box-shadow: 0 2px 12px 0 rgba(0, 180, 255, 0.08);
	}
	.celeste-header {
		background: #b3e6ff !important;
		color: #0077b6 !important;
		border-bottom: 2px solid #90caf9;
		font-weight: 600;
		font-size: 1.1rem;
	}
	.celeste-table th {
		background: #e6f7ff !important;
		color: #0077b6 !important;
		border-bottom: 2px solid #b3e6ff !important;
	}
	.celeste-table td {
		background: #fff !important;
		color: #222;
	}
	.btn-celeste {
		background: #00b4d8;
		color: #fff;
		border: none;
		font-weight: 500;
		transition: background 0.2s;
	}
	.btn-celeste:hover, .btn-celeste:focus {
		background: #0096c7;
		color: #fff;
	}
	.btn-outline-celeste {
		border: 1.5px solid #00b4d8;
		color: #00b4d8;
		background: #fff;
		font-weight: 500;
		transition: background 0.2s, color 0.2s;
	}
	.btn-outline-celeste:hover, .btn-outline-celeste:focus {
		background: #00b4d8;
		color: #fff;
	}
	.modal-content {
		border-radius: 1.2rem;
		border: 1.5px solid #b3e6ff;
		box-shadow: 0 4px 24px 0 rgba(0, 180, 255, 0.10);
	}
	.modal-header.text-bg-info {
		background: #00b4d8 !important;
		color: #fff !important;
		border-top-left-radius: 1.2rem;
		border-top-right-radius: 1.2rem;
	}
	.form-label {
		color: #0077b6;
		font-weight: 500;
	}
	.form-control:focus {
		border-color: #00b4d8;
		box-shadow: 0 0 0 0.15rem #b3e6ff;
	}
	.modal-footer.bg-light {
		background: #e6f7ff !important;
		border-bottom-left-radius: 1.2rem;
		border-bottom-right-radius: 1.2rem;
	}
</style>


<div class="container py-2">
	<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
		<button type="button" class="btn btn-celeste shadow-sm" id="incluir">
			<i class="fas fa-user-plus me-1"></i>INCLUIR
		</button>
		<a href="index.php?c=login&amp;m=home" class="btn btn-outline-celeste">
			<i class="fas fa-arrow-left me-1"></i>REGRESAR
		</a>
	</div>

	<h1 class="h3 mb-3" style="color:#00b4d8;font-weight:700;letter-spacing:0.01em;">Clientes</h1>

	<div class="celeste-card card shadow-sm border-0">
		<div class="celeste-header card-header border-bottom py-3">
			<span class="fw-semibold">Listado de clientes</span>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table
					class="table celeste-table table-striped table-hover table-bordered align-middle mb-0"
					id="tablausuarios"
					aria-describedby="tabla-clientes-desc"
				>
					<thead>
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
					<tbody id="resultadoconsulta">
					
					</tbody>
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
				
			</div>
			<div class="modal-footer bg-light">
				<button type="button" class="btn btn-outline-celeste" data-bs-dismiss="modal">Cerrar</button>
				<button class="btn btn-celeste" type="submit" id="proceso">INCLUIR</button>
			</div>
				</form>
		</div>

	</div>
</div>

<?php require_once("vista/layouts/footer.php"); ?>
