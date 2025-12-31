<?php
// Check if the expected POST variables are set
if (isset($_POST['foot_subscribe']) && isset($_POST['foot_email'])) {
    $email = $_POST['foot_email'];

    // Basic validation for the email format
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Normally, you would store the email in a database here.
        // For now, we'll just return a success message.

        echo '<p class="text-success">Thank you for subscribing! You will now receive updates to your email.</p>';
    } else {
        // If the email is invalid, return an error message.
        echo '<p class="text-danger">Please enter a valid email address.</p>';
    }
} else {
    // If the POST data is missing or incorrect
    echo '<p class="text-danger">There was an issue with your request. Please try again.</p>';
}
?>
