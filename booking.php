<?php
$host = "localhost";
$port = "3305";
$user = "root";
$password = ""; // Change if needed
$dbname = "fifo-kleen";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form values
$name = $_POST['name'];
$email = $_POST['email'];
$service = $_POST['service'];
$date = $_POST['date'];

// Insert into database
$sql = "INSERT INTO bookings (name, email, service, date)
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $service, $date);

if ($stmt->execute()) {
    echo "✅ Booking successful. We'll contact you soon!";
} else {
    echo "❌ Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
