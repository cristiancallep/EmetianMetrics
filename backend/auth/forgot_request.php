<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/forgot-password.html');
}

$email = sanitize_input($_POST['email'] ?? '');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect(BASE_URL . '/views/forgot-password.html?error=invalid_email');
}

$db = getDb();
$statement = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$statement->execute(['email' => $email]);
$user = $statement->fetch();

if ($user) {
    $token = generate_token(20);
    $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
    $insert = $db->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)');
    $insert->execute([
        'user_id' => (int)$user['id'],
        'token' => $token,
        'expires_at' => $expiresAt,
    ]);
}

redirect(BASE_URL . '/views/forgot-password.html?sent=1');
