<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/register.html');
}

$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$username = sanitize_input($_POST['username'] ?? '') ?: preg_replace('/[^a-z0-9_]/i', '', strstr($email, '@', true) ?: $email);

if (!$name || !$email || !$password || !$confirmPassword) {
    redirect(BASE_URL . '/views/register.html?error=missing_fields');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect(BASE_URL . '/views/register.html?error=invalid_email');
}

if ($password !== $confirmPassword) {
    redirect(BASE_URL . '/views/register.html?error=password_mismatch');
}

$db = getDb();

$exists = $db->prepare('SELECT id FROM users WHERE email = :email OR username = :username');
$exists->execute(['email' => $email, 'username' => $username]);
if ($exists->fetch()) {
    redirect(BASE_URL . '/views/register.html?error=user_exists');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = $db->prepare('INSERT INTO users (name, email, username, password) VALUES (:name, :email, :username, :password)');
$insert->execute([
    'name' => $name,
    'email' => $email,
    'username' => $username,
    'password' => $hash,
]);

$_SESSION['user_id'] = (int)$db->lastInsertId();
redirect(BASE_URL . '/views/dashboard.php');
