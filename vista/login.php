<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Iniciar sesión | Dermo Estética</title>
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="vendor/fontawesome/css/all.min.css">
	<script>
		if (sessionStorage.getItem('dermoestetica_login') === '1') {
			window.location.replace('index.php?pagina=home');
		}
	</script>
	<style>
		:root {
			--purple: #6b2d86;
			--purple-strong: #38174d;
		}

		body {
			min-height: 100vh;
			display: grid;
			place-items: center;
			background: linear-gradient(135deg, #faf2ff 0%, #8f6dbe 100%);
		}

		.login-card {
			width: min(100% - 2rem, 420px);
			border: 0;
			border-radius: 1.25rem;
			box-shadow: 0 20px 50px rgba(56, 23, 77, 0.2);
		}

		.login-icon {
			width: 68px;
			height: 68px;
			display: grid;
			place-items: center;
			margin: 0 auto 1rem;
			border-radius: 50%;
			background: linear-gradient(135deg, var(--purple), var(--purple-strong));
			color: #fff;
		}

		.btn-purple {
			color: #fff;
			background-color: var(--purple);
			border-color: var(--purple);
		}

		.btn-purple:hover,
		.btn-purple:focus {
			color: #fff;
			background-color: var(--purple-strong);
			border-color: var(--purple-strong);
		}
	</style>
</head>
<body>
	<main class="card login-card p-4 p-md-5">
		<div class="login-icon">
			<i class="fas fa-spa fa-2x"></i>
		</div>
		<h1 class="h3 text-center mb-1">Dermo Estética</h1>
		<p class="text-center text-muted mb-4">Ingresa al sistema de gestión</p>
		<form action="index.php?pagina=home" method="get" id="loginForm">
			<div class="mb-3">
				<label for="usuario" class="form-label">Usuario</label>
				<input type="text" class="form-control" id="usuario" name="usuario">
			</div>
			<div class="mb-4">
				<label for="contrasena" class="form-label">Contraseña</label>
				<input type="password" class="form-control" id="contrasena" name="contrasena">
			</div>
			<button type="submit" class="btn btn-purple w-100">
				<i class="fas fa-sign-in-alt me-2"></i>Ingresar
			</button>
		</form>
	</main>
	<script>
		document.getElementById('loginForm').addEventListener('submit', function () {
			sessionStorage.setItem('dermoestetica_login', '1');
		});
	</script>
</body>
</html>
