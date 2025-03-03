<?php
//Small script to test password hashing.
$password = "test123";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password: " . $hashedPassword;
?>