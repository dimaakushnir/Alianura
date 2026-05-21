<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// PHPMailer
require __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

/**
 * Відправка листа
 */
function sendMail(string $subject, string $body): bool
{
    $mail = new PHPMailer(true);

    try {
        // SMTP налаштування
        $mail->isSMTP();
        $mail->Host = 'mail.alianura.com';        // 🔴 SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'info@alianuraconsulting.com';    // 🔴 
        $mail->Password = 'EMAIL_PASSWORD';       // 🔴
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        $mail->Port = 465;                         // or 587

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('info@alianuraconsulting.com', 'Alianura'); // 🔴
        $mail->addAddress('info@alianuraconsulting.com');         // 🔴

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

/**
 *  Google reCAPTCHA v3
 */
function checkRecaptcha(string $token): bool
{
    $secret = 'SECRET_KEY'; // 🔴 reCAPTCHA Secret Key

    if (!$token) {
        return false;
    }

    $response = file_get_contents(
        'https://www.google.com/recaptcha/api/siteverify?secret=' .
        $secret . '&response=' . $token
    );

    if (!$response) {
        return false;
    }

    $result = json_decode($response, true);

    return (
        isset($result['success'], $result['score']) &&
        $result['success'] === true &&
        $result['score'] >= 0.5
    );
}
