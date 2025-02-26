<?php
session_start();

if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $userId = $_SESSION["user_id"];

    try {
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