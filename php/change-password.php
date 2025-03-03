<?php
session_start();
require "config.php";

//Check if user is logged in
if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $userId = $_SESSION["user_id"];
    $currentPassword = $_POST["current-password"];
    $newPassword = $_POST["new-password"];
    $repeatNewPassword = $_POST["repeat-new-password"];

    //Case where passwords do not match
    if($newPassword !== $repeatNewPassword){
        echo json_encode(["success" => false, "message" => "New passwords don't match."]);
        exit();
    }

    try {
        //SQL query to get a user's password from database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(["id" => $userId]);
        $user = $stmt->fetch();

        //If current password correct, SQL query to update password
        if($user && password_verify($currentPassword, $user["password"])){
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute([
                "password" => $hashedPassword,
                "id" => $userId,
            ]);

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Current password incorrect."]);
        }
    } catch(PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error updating password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>