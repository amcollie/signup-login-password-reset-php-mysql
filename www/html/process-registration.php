<?php

declare(strict_types=1);

use App\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    die('Request method not allowed');
}

if (empty($_POST['name'])) {
    http_response_code(400);
    die('Name is required');
}

if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die('Invalid email');
}

if (strlen($_POST['password']) < 8) {
    http_response_code(400);
    die('Password must be at least 8 characters');
}

if (!preg_match('/[a-z]/i', $_POST['password'])) {
    http_response_code(400);
    die('Password must contain at least one letter');
}

if (!preg_match('/[a-z]+/', $_POST['password'])) {
    http_response_code(400);
    die('Password must contain at least one lowercase letter');
}

if (!preg_match('/[A-Z]+/', $_POST['password'])) {
    http_response_code(400);
    die('Password must contain at least one uppercase letter');
}

if (!preg_match('/[0-9]+/', $_POST['password'])) {
    http_response_code(400);
    die('Password must contain at least one number');
}

if ($_POST['password'] !== $_POST['confirm-password']) {
    http_response_code(400);
    die('Passwords do not match');
}

$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

$mysqli = (new Database())->connect();

$stmt = $mysqli->stmt_init();
if (!$stmt->prepare('INSERT INTO users (name, email, password_hash) VALUES (?,?,?)')) {
    http_response_code(500);
    die('SQL error: ' . $mysqli->error);
}

$stmt->bind_param('sss', $_POST['name'], $_POST['email'], $password_hash);
if (!$stmt->execute()) {
    if ($mysqli->errno === 1062) {
        http_response_code(400);
        die('Email already taken');
    } else {
        http_response_code(400);
        die('SQL error: '. $mysqli->error . ' ' . $mysqli->errno);
    }
}

header('Location: registration-success.html');
exit();