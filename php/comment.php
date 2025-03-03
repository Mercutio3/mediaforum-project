<?php
session_start();
header("Content-Type: application/json");
require "config.php";

//Check if user logged in
if(!issset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

$rawData = file_get_contents("php://input");
$data = json_encode($rawData, true);

if(!isset($data["review_id"]) || !isset($data["content"])){
    echo json_encode(["success" => false, "message" => "Review ID and content required."]);
    exit();
}

$userId = $_SESSION["user_id"];
$reviewId = intval($data["review_id"]);
$content = trim($data["content"]);

if(empty($content)) {
    echo json_encode(["success" => false, "message" => "Please write something in comment."]);
    exit();
}

try {
    //SQL query to add comment to comments table
    $stmt = $conn->prepare("INSERT INTO comments (user_id, review_id, content) VALUES (:user_id, :review_id, :content)");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId, "content" => $content]);

    //SQL query to get review owner's ID
    $stmt = $conn->prepare("SELECT user_id FROM reviews WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);
    $reviewOwner = $stmt->fetch();

    if($reviewOwner) {
        //SQL query to add notification to notifications table
        $stmt = $conn->prepare("
            INSERT INTO notificiations (user_id, type, source_user_id, review_id, content)
            VALUES (:user_id, 'comment', :source_user_id, :review_id, :content)
        ");
        $stmt->execute([
            "user_id" => $reviewOwner["user_id"],
            "source_user_id" => $userId,
            "review_id" => $reviewId,
            "content" => $content,
        ]);
    }

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}   
?>