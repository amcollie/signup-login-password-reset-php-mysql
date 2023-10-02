<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once dirname(__DIR__). '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->load();

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = $_ENV['EMAIL_HOST'];
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = $_ENV['EMAIL_PORT'];
$mail->Username = $_ENV['EMAIL_USERNAME'];
$mail->Password = $_ENV['EMAIL_PASSWORD'];

$mail->isHTML(true);

return  $mail;