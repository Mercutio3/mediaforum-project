<?php
$to = "santiagodmzham@gmail.com";
$subject = "Test Email";
$message = "This is a test email.";
$headers = "From: no-reply@medrev.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>