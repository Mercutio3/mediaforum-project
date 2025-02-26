<?php
$password = "test123"; // Plain text password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hashedPassword;
?>