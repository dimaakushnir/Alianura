<?php

require 'mailer.php';

header('Content-Type: application/json');

$name = trim($_POST['yourname'] ?? '');
$email = trim($_POST['youremail'] ?? '');
$company = trim($_POST['yourcompany'] ?? '');
$help = trim($_POST['yourhelp'] ?? '');
$message = trim($_POST['yourmessage'] ?? '');
$token = $_POST['recaptcha_token'] ?? '';

if (mb_strlen($name) < 2) {
    exit(json_encode(['success' => false, 'message' => 'Invalid name']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(['success' => false, 'message' => 'Invalid email address']));
}


if (mb_strlen($message) < 10) {
    exit(json_encode(['success' => false, 'message' => 'Message too short']));
}

if (!checkRecaptcha($token)) {
    exit(json_encode(['success' => false, 'message' => 'Spam detected']));
}

$body = "Name: $name\nEmail: $email\nCompany: $company\nHelp: $help\n\nMessage:\n$message";

if (!sendMail('New contact request', $body)) {
    exit(json_encode(['success' => false, 'message' => 'Mail error']));
}

echo json_encode(['success' => true]);

