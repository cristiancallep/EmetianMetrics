<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => true, 'message' => 'Método no permitido'], 405);
}

$logFile = __DIR__ . '/../logs/item_save.log';
if (!is_dir(dirname($logFile))) {
    @mkdir(dirname($logFile), 0777, true);
}

try {
    $title = sanitize_input($_POST['title'] ?? '');
    $symbol = sanitize_input($_POST['crypto_symbol'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

    if (!$title || !$symbol) {
        json_response(['error' => true, 'message' => 'El título y el símbolo son obligatorios'], 422);
    }

    $db = getDb();
    $currentUser = getCurrentUser();
    if (!$currentUser) {
        json_response(['error' => true, 'message' => 'Usuario no autenticado'], 401);
    }

    // Ensure the crypto_symbol column exists to avoid prepare-time SQL errors
    try {
        $colCheck = $db->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = 'items' AND COLUMN_NAME = 'crypto_symbol'");
        $colCheck->execute(['schema' => DB_NAME]);
        $row = $colCheck->fetch();
        if (empty($row) || ((int)$row['cnt'] === 0)) {
            // add column
            $db->exec("ALTER TABLE items ADD COLUMN crypto_symbol VARCHAR(20) DEFAULT NULL AFTER title");
            @file_put_contents($logFile, date('c') . " | auto-added crypto_symbol column\n", FILE_APPEND);
        }
    } catch (Throwable $e) {
        @file_put_contents($logFile, date('c') . " | ensure column error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        // proceed; insertion will handle errors and log
    }

    $imagePath = null;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $saved = save_uploaded_file($_FILES['image'], __DIR__ . '/../uploads/items', ['jpg', 'jpeg', 'png', 'webp'], 5 * 1024 * 1024);
        if ($saved === null) {
            json_response(['error' => true, 'message' => 'Error al subir la imagen']);
        }
        $imagePath = BASE_URL . '/backend/uploads/items/' . $saved;
    }

    if ($itemId > 0) {
        $select = $db->prepare('SELECT user_id FROM items WHERE id = :id LIMIT 1');
        $select->execute(['id' => $itemId]);
        $item = $select->fetch();
        if (!$item || $item['user_id'] !== $currentUser['id']) {
            json_response(['error' => true, 'message' => 'Item no encontrado o sin permiso'], 403);
        }

        if ($imagePath !== null) {
            $sql = 'UPDATE items SET title = :title, crypto_symbol = :crypto_symbol, description = :description, image_path = :image_path WHERE id = :id';
            $params = ['title' => $title, 'crypto_symbol' => $symbol, 'description' => $description, 'image_path' => $imagePath, 'id' => $itemId];
        } else {
            $sql = 'UPDATE items SET title = :title, crypto_symbol = :crypto_symbol, description = :description WHERE id = :id';
            $params = ['title' => $title, 'crypto_symbol' => $symbol, 'description' => $description, 'id' => $itemId];
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        json_response(['success' => true, 'message' => 'Item actualizado']);
    }

    $sql = 'INSERT INTO items (user_id, title, crypto_symbol, description, image_path) VALUES (:user_id, :title, :crypto_symbol, :description, :image_path)';
    $stmt = $db->prepare($sql);
    try {
        $stmt->execute([
            'user_id' => $currentUser['id'],
            'title' => $title,
            'crypto_symbol' => $symbol,
            'description' => $description,
            'image_path' => $imagePath,
        ]);
        json_response(['success' => true, 'message' => 'Item creado']);
    } catch (PDOException $e) {
        // Detect unknown column error (MySQL 1054). Attempt to add the column and retry once.
        $errorCode = (int)($e->errorInfo[1] ?? 0);
        if ($errorCode === 1054) {
            try {
                $db->exec("ALTER TABLE items ADD COLUMN crypto_symbol VARCHAR(20) DEFAULT NULL AFTER title");
                // retry
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'user_id' => $currentUser['id'],
                    'title' => $title,
                    'crypto_symbol' => $symbol,
                    'description' => $description,
                    'image_path' => $imagePath,
                ]);
                json_response(['success' => true, 'message' => 'Item creado (columna añadida automáticamente)']);
            } catch (Throwable $e2) {
                $msg2 = date('c') . " | retry error: " . $e2->getMessage() . PHP_EOL . $e2->getTraceAsString() . PHP_EOL;
                @file_put_contents($logFile, $msg2, FILE_APPEND);
                json_response(['error' => true, 'message' => 'Error al crear favorito tras intentar reparar la BD'], 500);
            }
        }
        throw $e; // rethrow to be caught by outer handler
    }

} catch (Throwable $e) {
    $msg = date('c') . " | " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
    @file_put_contents($logFile, $msg, FILE_APPEND);
    json_response(['error' => true, 'message' => 'Error interno del servidor'], 500);
}
