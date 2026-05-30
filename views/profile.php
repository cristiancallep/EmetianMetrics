<?php
require_once __DIR__ . '/../backend/helpers.php';
require_login();
$user = getCurrentUser();
$avatarUrl = $user['avatar'] ?? null;
$avatarInitial = strtoupper(mb_substr($user['name'] ?? 'U', 0, 1));
$success = isset($_GET['success']);
$error = $_GET['error'] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>EmetianMetrics - Perfil</title>
	<link rel="stylesheet" href="../styles/profile.css" />
	<link rel="icon" href="../assets/public/icono_pg.png" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
	<div class="profile-shell">
		<aside class="sidebar">
			<div class="brand">
				<img src="../assets/public/icono_pg.png" alt="EmetianMetrics" />
				<div>
					<strong>EmetianMetrics</strong>
					<span>Perfil de usuario</span>
				</div>
			</div>
			<nav class="menu" aria-label="Menú lateral">
				<a class="menu-item" href="dashboard.php"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
				<a class="menu-item" href="items.php"><i class="bi bi-box-seam"></i><span>Items</span></a>
				<a class="menu-item" href="users.php"><i class="bi bi-people"></i><span>Usuarios</span></a>
				<a class="menu-item" href="favourites.php"><i class="bi bi-heart"></i><span>Fav. por usuario</span></a>
				<a class="menu-item active" href="profile.php"><i class="bi bi-person"></i><span>Perfil</span></a>
				<a class="menu-item" href="../backend/auth/logout.php"><i class="bi bi-box-arrow-right"></i><span>Salir</span></a>
			</nav>
		</aside>

		<main class="main">
			<header class="topbar">
				<div>
					<h1>Editar perfil</h1>
					<p>Actualiza tus datos personales y preferencias</p>
				</div>
				<a class="back-link" href="dashboard.php"><i class="bi bi-arrow-left"></i> Volver al dashboard</a>
			</header>

			<section class="profile-grid">
				<article class="profile-card summary-card">
					<div class="avatar" id="profileAvatar">
						<?php if ($avatarUrl): ?>
							<img src="<?php echo htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar" />
						<?php else: ?>
							<?php echo $avatarInitial; ?>
						<?php endif; ?>
					</div>
					<h2 id="profileNamePreview"><?php echo htmlspecialchars($user['name'] ?? 'Emetian User', ENT_QUOTES, 'UTF-8'); ?></h2>
					<p id="profileEmailPreview"><?php echo htmlspecialchars($user['email'] ?? 'correo@ejemplo.com', ENT_QUOTES, 'UTF-8'); ?></p>
					<div class="summary-chip">Perfil sincronizado con la base de datos</div>
				</article>

				<article class="profile-card form-card">
					<?php if ($success): ?>
						<div class="alert success">Perfil actualizado correctamente.</div>
					<?php endif; ?>
					<?php if ($error): ?>
						<div class="alert error">Error: <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
					<?php endif; ?>
					<form id="profileForm" class="profile-form" action="../backend/api/profile_update.php" method="post" enctype="multipart/form-data">
						<div class="field-grid">
							<label>
								<span>Nombre completo</span>
								<input id="name" name="name" type="text" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Tu nombre" required />
							</label>
							<label>
								<span>Correo electrónico</span>
								<input id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="tu@correo.com" required />
							</label>
							<label>
								<span>Ciudad</span>
								<input id="city" name="city" type="text" value="<?php echo htmlspecialchars($user['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ciudad" />
							</label>
							<label>
								<span>Usuario</span>
								<input id="username" name="username" type="text" value="<?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nombre de usuario" required />
							</label>
							<label>
								<span>Biografía</span>
								<textarea id="bio" name="bio" placeholder="Descripción breve"><?php echo htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
							</label>
							<label>
								<span>Avatar</span>
								<input id="avatar" name="avatar" type="file" accept="image/*" />
							</label>
						</div>
						<div class="form-actions">
							<button type="reset" class="secondary-btn">Restablecer</button>
							<button type="submit" class="primary-btn">Guardar cambios</button>
						</div>
					</form>
				</article>
			</section>
		</main>
	</div>
</body>
</html>
