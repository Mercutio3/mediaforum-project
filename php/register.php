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
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(["username" => $username, "email" => $email]);
        $user = $stmt->fetch();

        if($user) {
            echo json_encode(["success" => false, "message" => "Username/Email already taken."]);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute(["username" => $username, "email" => $email, "password" => $password]);
            echo json_encode(["success" => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error. Please try again!"]);
    }
}
?>