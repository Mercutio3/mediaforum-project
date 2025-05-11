<?php
session_start();
header("Content-Type: application/json");

//Check if user logged in
if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

require "config.php";

$userId = $_SESSION["user_id"];

try {
    $stmt = $conn->prepare("
        SELECT
            notifications.*,
            source_user.username AS source_username,
            reviews.title AS review_title
        FROM notifications
        JOIN users AS source_user ON notifications.source_user_id = source_user_id
        LEFT JOIN reviews ON notifications.review_id = reviews.id
        WHERE notifications.user_id = :user_id
        ORDER BY notifications.created_at DESC
    ");
    $stmt->execute(["user_id" => $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "notifications" => $notifications]);
} catch (PDOException $e){
    echo json_encode(["success" => false, "message" => "Database error, try again!"]);
}
?>