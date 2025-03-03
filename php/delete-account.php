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

    try {
        //SQL query to delete user from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(["id" => $userId]);

        session_unset();
        session_destroy();

        echo json_encode(["success" => true]);
    } catch (PDOException $e){
        echo json_encode(["success" => false, "message" => "Error deleting account."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>