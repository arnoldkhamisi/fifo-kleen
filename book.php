<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$service = trim($_POST['service'] ?? '');
$date = trim($_POST['date'] ?? '');

if ($name === '' || $service === '' || $date === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Invalid booking input.';
    exit;
}

$stmt = $conn->prepare('INSERT INTO bookings (name, email, service, date) VALUES (?, ?, ?, ?)');
if (!$stmt) {
    http_response_code(500);
    echo 'Database preparation failed.';
    exit;
}

$stmt->bind_param('ssss', $name, $email, $service, $date);
if (!$stmt->execute()) {
    http_response_code(500);
    echo 'Database write failed.';
    $stmt->close();
    exit;
}
$stmt->close();

sendBookingEmail($name, $email, $service, $date);
echo 'success';

function sendBookingEmail(string $name, string $email, string $service, string $date): void
{
    $smtpHost = getenv('SMTP_HOST') ?: '';
    $smtpUser = getenv('SMTP_USER') ?: '';
    $smtpPass = getenv('SMTP_PASS') ?: '';
    $smtpPort = (int) (getenv('SMTP_PORT') ?: 587);
    $smtpSecure = getenv('SMTP_SECURE') ?: 'tls';
    $fromEmail = getenv('SMTP_FROM') ?: $smtpUser;
    $toEmail = getenv('BOOKING_NOTIFY_EMAIL') ?: '';

    if ($smtpHost === '' || $smtpUser === '' || $smtpPass === '' || $fromEmail === '' || $toEmail === '') {
        return;
    }

    require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
    require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = $smtpSecure;
        $mail->Port = $smtpPort;
        $mail->setFrom($fromEmail, 'Fifo-Kleen');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'New Booking Received';
        $mail->Body = sprintf(
            'Name: %s<br>Email: %s<br>Service: %s<br>Date: %s',
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($service, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($date, ENT_QUOTES, 'UTF-8')
        );
        $mail->send();
    } catch (Exception $e) {
        error_log('Booking email send failed: ' . $e->getMessage());
    }
}
