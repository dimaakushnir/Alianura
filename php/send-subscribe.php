<?php

require 'mailer.php';

header('Content-Type: application/json');

$email = filter_var($_POST['youremail'] ?? '', FILTER_VALIDATE_EMAIL);
$token = $_POST['recaptcha_token'] ?? '';

if (!$email) {
    exit(json_encode(['success' => false, 'message' => 'Invalid email address']));
}

if (!checkRecaptcha($token)) {
    exit(json_encode(['success' => false, 'message' => 'Spam detected']));
}

if (!sendMail('New subscription', "Email: $email")) {
    exit(json_encode(['success' => false, 'message' => 'Mail error']));
}

echo json_encode(['success' => true]);

