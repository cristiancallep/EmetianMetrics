<?php
require_once __DIR__ . '/../backend/helpers.php';
require_login();
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>EmetianMetrics - Favoritos cripto</title>
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
					<span>Favoritos cripto</span>
				</div>
			</div>
			<nav class="menu" aria-label="Menú lateral">
				<a class="menu-item" href="dashboard.php"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
				<a class="menu-item active" href="items.php"><i class="bi bi-star"></i><span>Favoritos</span></a>
				<a class="menu-item" href="users.php"><i class="bi bi-people"></i><span>Usuarios</span></a>
				<a class="menu-item" href="favourites.php"><i class="bi bi-heart"></i><span>Fav. por usuario</span></a>
				<a class="menu-item" href="profile.php"><i class="bi bi-person"></i><span>Perfil</span></a>
				<a class="menu-item" href="../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i><span>Salir</span></a>
			</nav>
		</aside>
		<main class="main">
			<header class="topbar">
				<div>
					<h1>Favoritos cripto</h1>
					<p>Gestiona tus criptomonedas favoritas y notas de seguimiento.</p>
				</div>
			</header>
			<section class="panel table-panel">
				<div class="panel-header compact">
					<div>
						<h2>Crear / editar cripto favorita</h2>
						<p>Agrega y administra criptomonedas favoritas con notas e imagen.</p>
				</div>
				</div>
				<form id="itemForm" class="item-form" enctype="multipart/form-data">
					<input type="hidden" name="item_id" id="item_id" value="" />
					<div class="field-grid">
						<label>
							<span>Nombre de la criptomoneda</span>
							<input id="title" name="title" type="text" placeholder="Bitcoin" required />
						</label>
						<label>
							<span>Símbolo</span>
							<input id="crypto_symbol" name="crypto_symbol" type="text" placeholder="BTC" required />
						</label>
						<label>
							<span>Notas</span>
							<textarea id="description" name="description" placeholder="Razón para seguir esta cripto"></textarea>
						</label>
						<label>
							<span>Imagen</span>
							<input id="image" name="image" type="file" accept="image/*" />
						</label>
					</div>
					<div class="form-actions">
						<button type="button" id="resetItem" class="secondary-btn">Limpiar</button>
						<button type="submit" class="primary-btn">Guardar favorito</button>
					</div>
				</form>
			</section>
			<section class="panel table-panel">
				<div class="panel-header compact">
					<div>
						<h2>Tabla de favoritos</h2>
						<p>Edición y eliminación de registros con DataTables.</p>
					</div>
				</div>
				<div class="table-wrap">
					<table id="itemsTable" class="display" style="width:100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Cripto</th>
								<th>Símbolo</th>
								<th>Notas</th>
								<th>Propietario</th>
								<th>Creado</th>
								<th>Acciones</th>
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
	<script src="../assets/js/items.js"></script>
</body>
</html>
