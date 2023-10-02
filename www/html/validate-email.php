<?php

declare(strict_types=1);

use App\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

$mysqli = (new Database())->connect();

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare('SELECT * FROM users WHERE email = ?')) {
    http_response_code(500);
    die('An error occurred');
}

$email = filter_var(htmlspecialchars($_GET['email']), FILTER_SANITIZE_EMAIL);
$stmt->bind_param('s', $email);

if (!$stmt->execute()) {
    http_response_code(500);
    die('An error occurred while retrieving user data');
}

$result = $stmt->get_result();

$is_available = $result->num_rows === 0;

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['is_available' => $is_available]);