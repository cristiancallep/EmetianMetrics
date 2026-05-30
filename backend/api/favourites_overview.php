<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

$db = getDb();

$statement = $db->query('
    SELECT
        i.id,
        u.id AS user_id,
        u.name AS user_name,
        u.username,
        i.title AS item_title,
        i.crypto_symbol,
        i.created_at
    FROM items i
    INNER JOIN users u ON u.id = i.user_id
    ORDER BY i.created_at DESC
');

$rows = $statement->fetchAll();

json_response([
    'data' => $rows,
]);
