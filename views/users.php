<?php
require_once __DIR__ . '/../backend/helpers.php';
require_login();
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>EmetianMetrics - Usuarios</title>
	<link rel="stylesheet" href="../styles/dashboard.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
	<link rel="icon" href="../assets/public/icono_pg.png" />
</head>
<body>
	<div class="app-shell">
		<aside class="sidebar">
			<div class="brand">
				<img src="../assets/public/icono_pg.png" alt="EmetianMetrics" />
				<div>
					<strong>EmetianMetrics</strong>
					<span>Usuarios</span>
				</div>
			</div>
			<nav class="menu" aria-label="Menú lateral">
				<a class="menu-item" href="dashboard.php"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
				<a class="menu-item" href="items.php"><i class="bi bi-box-seam"></i><span>Items</span></a>
				<a class="menu-item active" href="users.php"><i class="bi bi-people"></i><span>Usuarios</span></a>
				<a class="menu-item" href="profile.php"><i class="bi bi-person"></i><span>Perfil</span></a>
				<a class="menu-item" href="../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i><span>Salir</span></a>
			</nav>
		</aside>
		<main class="main">
			<header class="topbar">
				<div>
					<h1>Usuarios registrados</h1>
					<p>Listado de usuarios almacenados en la base de datos.</p>
				</div>
			</header>
			<section class="panel table-panel">
				<div class="panel-header compact">
					<div>
						<h2>Lista de usuarios</h2>
						<p>Tabla interactiva generada con DataTables.</p>
					</div>
				</div>
				<div class="table-wrap">
					<table id="usersTable" class="display" style="width:100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Nombre</th>
								<th>Correo</th>
								<th>Usuario</th>
								<th>Registrado</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</section>
		</main>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="../assets/js/users.js"></script>
</body>
</html>
