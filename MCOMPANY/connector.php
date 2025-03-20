<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = $_POST["rating"];
    $feedback = $_POST["feedback"];

    // Send the feedback to the company's email (Replace with real email)
    $to = "company@example.com";
    $subject = "New Feedback Received";
    $message = "Rating: $rating Stars\nFeedback: $feedback";
    $headers = "From: no-reply@yourcompany.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "Thank you for your feedback!";
    } else {
        echo "Error sending feedback. Please try again.";
    }
}
?>
