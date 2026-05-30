<?php
declare(strict_types=1);

// Configuración de la base de datos para XAMPP / MySQL.
// Ajusta los valores si tu instancia usa otra contraseña o puerto.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'emetian_metrics');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', '/EmetianMetrics');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
