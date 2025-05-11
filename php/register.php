<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);
require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = $data["username"];
    $email = $data["email"];
    $password = password_hash($data["password"], PASSWORD_BCRYPT);

    try {
        //SQL query to get username from users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(["username" => $username, "email" => $email]);
        $user = $stmt->fetch();

        //Verify if username+email are available.
        if($user) {
            echo json_encode(["success" => false, "message" => "Username/Email already taken."]);
        } else {
            //Generate verification token
            $verificationToken = bin2hex(random_bytes(32));

            //SQL query to add user to users table
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, verification_token, bio)
                VALUES (:username, :email, :password, :verification_token, :bio)
            ");
            $stmt->execute([
                "username" => $username,
                "email" => $email,
                "password" => $password,
                "verification_token" => $verificationToken,
                "bio" => "Hi there! This is my profile.",
            ]);

            //Send verification email
            $verificationLink = "http://localhost:8888/php/verify.php?token=$verificationToken";
            $subject = "Media Review Forum — Verify your email";
            $message = "Thank you for registering to the Media Review Forum! Click this link to verify your email: $verificationLink";
            $headers = "From: no-reply@medrev.com";

            // Save token to a text file
            $tokenData = [
                "username" => $username,
                "email" => $email,
                "token" => $verificationToken,
                "generated_at" => date("Y-m-d H:i:s")
            ];
            
            $filePath = "tokens/" . $username . "_token.txt";
            
            // Create tokens directory if it doesn't exist
            if (!file_exists("tokens")) {
                mkdir("tokens", 0777, true);
            }

            // Write token to file
            file_put_contents($filePath, json_encode($tokenData, JSON_PRETTY_PRINT));

            if(mail($email, $subject, $message, $headers)){
                echo json_encode(["success" => true, "message" => "Registration successful! Check the email you entered to verify your account."]);
            } else {
                echo json_encode(["success" => false, "message" => "Could not send verification email."]);
            }
        }
    }  catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
}
?>