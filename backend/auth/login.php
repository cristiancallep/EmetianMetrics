<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/index.html');
}

$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    redirect(BASE_URL . '/index.html?error=missing_fields');
}

$db = getDb();
$statement = $db->prepare('SELECT id, password FROM users WHERE email = :email LIMIT 1');
$statement->execute(['email' => $email]);
$user = $statement->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    redirect(BASE_URL . '/index.html?error=invalid_credentials');
}

$_SESSION['user_id'] = (int)$user['id'];
redirect(BASE_URL . '/views/dashboard.php');
