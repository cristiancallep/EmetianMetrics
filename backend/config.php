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

// Gmail SMTP settings for PHPMailer
// Rellena estos valores con tu cuenta y contraseña de aplicación.
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'emetianmail@gmail.com');
define('SMTP_PASS', 'asej cpmx rxjv hgcr');
define('EMAIL_FROM_ADDRESS', 'emetianmail@gmail.com');
define('EMAIL_FROM_NAME', 'EmetianMetrics');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
