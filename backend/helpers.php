<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function sanitize_input(string $value): string
{
    return trim(htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}

function json_response(array $data, int $statusCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(BASE_URL . '/index.html');
    }
}

function getCurrentUser(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    $db = getDb();
    $statement = $db->prepare('SELECT id, name, email, username, avatar, city, bio, created_at FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $_SESSION['user_id']]);
    $user = $statement->fetch();

    return $user ?: null;
}

function save_uploaded_file(array $file, string $folder, array $allowedExtensions, int $maxSize = 5242880): ?string
{
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > $maxSize) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        return null;
    }

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $filename = sprintf('%s_%s.%s', bin2hex(random_bytes(8)), time(), $ext);
    $destination = rtrim($folder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return $filename;
}

function generate_token(int $length = 40): string
{
    return bin2hex(random_bytes($length));
}
