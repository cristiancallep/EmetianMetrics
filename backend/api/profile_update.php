<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/views/profile.php');
}

$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$username = sanitize_input($_POST['username'] ?? '');
$city = sanitize_input($_POST['city'] ?? '');
$bio = sanitize_input($_POST['bio'] ?? '');

if (!$name || !$email || !$username) {
    redirect(BASE_URL . '/views/profile.php?error=missing_fields');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect(BASE_URL . '/views/profile.php?error=invalid_email');
}

$db = getDb();
$currentUser = getCurrentUser();
if (!$currentUser) {
    redirect(BASE_URL . '/index.html');
}

$exists = $db->prepare('SELECT id FROM users WHERE (email = :email OR username = :username) AND id != :current_id LIMIT 1');
$exists->execute(['email' => $email, 'username' => $username, 'current_id' => $currentUser['id']]);
if ($exists->fetch()) {
    redirect(BASE_URL . '/views/profile.php?error=user_exists');
}

$updateFields = [
    'name' => $name,
    'email' => $email,
    'username' => $username,
    'city' => $city,
    'bio' => $bio,
    'id' => $currentUser['id'],
];

$avatarFilename = null;
if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    $saved = save_uploaded_file($_FILES['avatar'], __DIR__ . '/../uploads/avatars', ['jpg', 'jpeg', 'png', 'webp'], 5 * 1024 * 1024);
    if ($saved !== null) {
        $avatarFilename = $saved;
        $updateFields['avatar'] = BASE_URL . '/backend/uploads/avatars/' . $avatarFilename;
        $sql = 'UPDATE users SET name = :name, email = :email, username = :username, city = :city, bio = :bio, avatar = :avatar WHERE id = :id';
    } else {
        redirect(BASE_URL . '/views/profile.php?error=avatar_error');
    }
} else {
    $sql = 'UPDATE users SET name = :name, email = :email, username = :username, city = :city, bio = :bio WHERE id = :id';
}

$update = $db->prepare($sql);
$update->execute($updateFields);

redirect(BASE_URL . '/views/profile.php?success=1');
