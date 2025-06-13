<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include database connection
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve booking details from the form
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_POST['service'];
     $date = $_POST['date'];

    // Prepare the SQL query
    $sql = "INSERT INTO bookings (id, name, email,  service, date) VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

   // Bind parameters
    $stmt->bind_param("sssss", $id, $name, $email, $service, $date);

    // Execute the query
    if ($stmt->execute()) {
        echo "Booking successful!";


require 'C:/xampp/htdocs/fifo/PHPMailer-master/src/Exception.php';
require 'C:/xampp/htdocs/fifo/PHPMailer-master/src/PHPMailer.php';
require 'C:/xampp/htdocs/fifo/PHPMailer-master/src/SMTP.php';



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
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email using PHPMailer';

    $mail->send();
    echo 'Email sent successfully';
} catch (Exception $e) {
    echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
}
}
}
?>