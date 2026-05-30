<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

$db = getDb();
$currentUser = getCurrentUser();
$statement = $db->prepare('SELECT i.id, i.title, i.crypto_symbol, i.description, i.image_path, i.created_at, i.user_id, u.name AS owner_name FROM items i JOIN users u ON u.id = i.user_id ORDER BY i.created_at DESC');
$statement->execute();
$items = $statement->fetchAll();
json_response([
    'current_user_id' => $currentUser['id'] ?? null,
    'data' => $items,
]);
