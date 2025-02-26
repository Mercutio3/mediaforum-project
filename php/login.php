<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);

require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = $data["password"];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user["password"])) {
            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid usernaem or password."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error. Please try again!"]);
    }
}
?>