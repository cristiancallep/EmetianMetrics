<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/forgot-password.html');
}

$token = sanitize_input($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (!$token || !$password || !$confirmPassword) {
    redirect(BASE_URL . '/views/forgot-password.html?error=missing_fields');
}

if ($password !== $confirmPassword) {
    redirect(BASE_URL . '/views/forgot-password.html?error=password_mismatch');
}

$db = getDb();
$statement = $db->prepare('SELECT pr.id, pr.user_id FROM password_resets pr WHERE pr.token = :token AND pr.expires_at >= NOW() AND pr.used = 0 LIMIT 1');
$statement->execute(['token' => $token]);
$reset = $statement->fetch();

if (!$reset) {
    redirect(BASE_URL . '/views/forgot-password.html?error=invalid_token');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$update = $db->prepare('UPDATE users SET password = :password WHERE id = :user_id');
$update->execute(['password' => $hash, 'user_id' => (int)$reset['user_id']]);

$markUsed = $db->prepare('UPDATE password_resets SET used = 1 WHERE id = :id');
$markUsed->execute(['id' => (int)$reset['id']]);

redirect(BASE_URL . '/index.html?reset=success');
