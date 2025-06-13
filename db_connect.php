<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fifo_kleen"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
