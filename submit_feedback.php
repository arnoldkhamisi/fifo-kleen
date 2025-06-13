<?php
// DB connection
$host = 'localhost';
$db = 'fifo_kleen_db';
$user = 'root';
$pass = 'your_db_password';

$conn = new mysqli($host, $user, $pass, $db);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$rating = $_POST['rating'];
$note = $_POST['note'];

// Store in database
$stmt = $conn->prepare("INSERT INTO feedback (rating, note) VALUES (?, ?)");
$stmt->bind_param("is", $rating, $note);
$stmt->execute();
$stmt->close();

// Send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.example.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'khamisiarnold@gmail.com';
    $mail->Password   = '@arnold705';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('info@fifo-cleen_services.com', 'Fifo-Kleen');
    $mail->addAddress('khamisiarnold@gmail.com', 'Admin');
    $mail->addAddress('felisterondieki06@gmail.com', 'Admin');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New 5-Star Feedback';
    $mail->Body    = "Rating: $rating star(s)<br>Note: $note";

    $mail->send();
    echo "Feedback submitted successfully.";
} catch (Exception $e) {
    echo "Email error: {$mail->ErrorInfo}";
}

$conn->close();
?>
