<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => true, 'message' => 'Método no permitido'], 405);
}

$itemId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($itemId <= 0) {
    json_response(['error' => true, 'message' => 'Item inválido'], 422);
}

$db = getDb();
$currentUser = getCurrentUser();
if (!$currentUser) {
    json_response(['error' => true, 'message' => 'Usuario no autenticado'], 401);
}

$select = $db->prepare('SELECT user_id FROM items WHERE id = :id LIMIT 1');
$select->execute(['id' => $itemId]);
$item = $select->fetch();
if (!$item || $item['user_id'] !== $currentUser['id']) {
    json_response(['error' => true, 'message' => 'Item no encontrado o sin permiso'], 403);
}

$delete = $db->prepare('DELETE FROM items WHERE id = :id');
$delete->execute(['id' => $itemId]);
json_response(['success' => true, 'message' => 'Item eliminado']);
