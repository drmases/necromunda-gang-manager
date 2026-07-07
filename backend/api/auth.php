<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/config.local.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    jsonResponse(['authed' => !empty($_SESSION['authed'])]);
}

if ($method === 'POST') {
    $body     = getBody();
    $password = $body['password'] ?? '';
    if (!password_verify($password, ADMIN_PASSWORD_HASH)) {
        jsonError('Invalid password', 401);
    }
    $_SESSION['authed'] = true;
    jsonResponse(['authed' => true]);
}

if ($method === 'DELETE') {
    $_SESSION = [];
    session_destroy();
    jsonResponse(['authed' => false]);
}

jsonError('Method not allowed', 405);
