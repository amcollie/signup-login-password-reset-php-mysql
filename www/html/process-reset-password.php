<?php

declare(strict_types=1);

use App\Database;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = htmlspecialchars($_POST['token']);  

    $token_hash = hash('sha256', $token);
    $mysqli = (new Database())->connect();

    $stmt = $mysqli->stmt_init();
    if (!$stmt->prepare('
        SELECT * 
        FROM users 
        WHERE reset_token_hash = ?'
    )) {
        http_response_code(500);
        die('Unable to retrieve data: '. $mysqli->error);
    }

    $stmt->bind_param('s', $token_hash);
    if (!$stmt->execute()) {
        http_response_code(500);
        die('Unable to execute statement: '. $mysqli->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(404);
        die('No user found: '. $mysqli->error);
    }
    $user = $result->fetch_assoc();

    if ($user['reset_token_expires_at'] < date('Y-m-d H:i:s')) {
        http_response_code(400);
        die('Token has expired');
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
    if (!$stmt->prepare('
        UPDATE users  
        SET password_hash = ?
            , reset_token_hash = NULL
            , reset_token_expires_at = NULL
        WHERE id = ?;'
    )) {
        http_response_code(500);
        die('SQL error: ' . $mysqli->error);
    }

    $stmt->bind_param(
        'ss', 
        $password_hash, 
        $user['id']
    );
    if (!$stmt->execute()) {
        http_response_code(500);
        die('SQL error: '. $mysqli->error . ' ' . $mysqli->errno);
    }

    header('Location: login.php');

} else {
    http_response_code(405);
    die('Method not allowed. Allowed methods are POST');
}