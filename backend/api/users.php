<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

$db = getDb();
$statement = $db->query('SELECT id, name, email, username, avatar, created_at FROM users ORDER BY created_at DESC');
$users = $statement->fetchAll();
json_response(['data' => $users]);
