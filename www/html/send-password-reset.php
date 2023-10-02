<?php

declare(strict_types=1);

use App\Database;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die('Invalid email format');
    }

    $token = bin2hex(random_bytes(16));
    $token_harsh = hash('sha256', $token);

    $expiry = date('Y-m-d H:i:s', time() + (60 * 30));

    $mysqli = (new Database())->connect();

    $stmt = $mysqli->stmt_init();
    if (!$stmt->prepare(
        'UPDATE users 
        SET reset_token_hash = ?
            , reset_token_expires_at = ? 
        WHERE email = ?'
    )) {
        http_response_code(500);
        die('Unable to retrieve data: '. $mysqli->error);
    }

    $stmt->bind_param(
        'sss',
        $token_harsh,
        $expiry,
        $email
    );
    if (!$stmt->execute()) {
        http_response_code(500);
        die('Unable to execute statement: '. $mysqli->error);
    }

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        die('No user found: '. $mysqli->error);
    }

    $mail = require __DIR__ . '/mailer.php';

    $mail->From = $_ENV['EMAIL_FROM'];
    $mail->Subject = 'Password Reset';
    $mail->addAddress($email);
    $mail->Body = <<<EOM

    <p>
        Click <a href="http://127.0.0.1/reset-password.php?token=$token">here</a> to reset password.
    </p>

    EOM;

    try {
        $mail->send();
    } catch (Exception $e) {
        http_response_code(500);
        die('Email could not be sent. Mailer Error: '. $mail->ErrorInfo);
    }

    die('Email has been sent, please check your inbox.');
} else {
    http_response_code(405);
    die('Method not allowed. Allowed methods are POST');
}