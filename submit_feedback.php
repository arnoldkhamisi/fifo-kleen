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

$rating = (int) ($_POST['rating'] ?? 0);
$note = trim($_POST['note'] ?? '');

if ($rating < 1 || $rating > 5 || $note === '') {
    http_response_code(400);
    echo 'Invalid feedback input.';
    exit;
}

$stmt = $conn->prepare('INSERT INTO feedback (rating, note) VALUES (?, ?)');
if (!$stmt) {
    http_response_code(500);
    echo 'Database preparation failed.';
    exit;
}

$stmt->bind_param('is', $rating, $note);
if (!$stmt->execute()) {
    http_response_code(500);
    echo 'Database write failed.';
    $stmt->close();
    exit;
}
$stmt->close();

sendFeedbackEmail($rating, $note);
echo 'success';

function sendFeedbackEmail(int $rating, string $note): void
{
    $smtpHost = getenv('SMTP_HOST') ?: '';
    $smtpUser = getenv('SMTP_USER') ?: '';
    $smtpPass = getenv('SMTP_PASS') ?: '';
    $smtpPort = (int) (getenv('SMTP_PORT') ?: 587);
    $smtpSecure = getenv('SMTP_SECURE') ?: 'tls';
    $fromEmail = getenv('SMTP_FROM') ?: $smtpUser;
    $toEmail = getenv('FEEDBACK_NOTIFY_EMAIL') ?: '';

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
        $mail->Subject = 'New Feedback Received';
        $mail->Body = sprintf(
            'Rating: %d star(s)<br>Feedback: %s',
            $rating,
            htmlspecialchars($note, ENT_QUOTES, 'UTF-8')
        );
        $mail->send();
    } catch (Exception $e) {
        error_log('Feedback email send failed: ' . $e->getMessage());
    }
}
