<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

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

function send_email(string $subject, string $body, string $recipientEmail, string $recipientName = ''): bool
{
    if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
        return false;
    }

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName ?: $recipientEmail);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(false);
        $mail->send();
        return true;
    } catch (Throwable $e) {
        @file_put_contents(__DIR__ . '/logs/email.log', date('c') . ' | ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return false;
    }
}

function send_item_created_notification(array $user, string $title, string $symbol, string $description, ?string $imagePath): bool
{
    $subject = 'Nuevo favorito cripto guardado: ' . $title;
    $body = "Se ha guardado un nuevo favorito cripto en EmetianMetrics:\n\n" .
        "Nombre: {$title}\n" .
        "Símbolo: {$symbol}\n" .
        "Notas: {$description}\n" .
        ($imagePath ? "Imagen: {$imagePath}\n" : '') .
        "\nGracias por usar EmetianMetrics!";

    return send_email($subject, $body, $user['email'], $user['name']);
}

function generate_token(int $length = 40): string
{
    return bin2hex(random_bytes($length));
}
