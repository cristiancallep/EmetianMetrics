<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_login();

$user = getCurrentUser();
if (!$user) {
    json_response(['error' => true, 'message' => 'Usuario no autenticado'], 401);
}

$json = [
    'data' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'username' => $user['username'],
        'avatar' => $user['avatar'],
        'city' => $user['city'],
        'bio' => $user['bio'],
        'created_at' => $user['created_at'],
    ],
];

json_response($json);
