<?php
session_start();
header("Content-Type: application/json");

//Check if user logged in
if(!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

require "config.php";

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if(!isset($data["review_id"])){
    echo json_encode(["success" => false, "message" => "Review ID required!"]);
    exit();
}

$reviewId = intval($data["review_id"]);
$userId = $_SESSION["user_id"];

try {
    //SQL query to get ID from review user wishes to delete
    $stmt = $conn->prepare("SELECT id FROM reviews WHERE id = :review_id AND user_id = :user_id");
    $stmt->execute(["review_id" => $reviewId, "user_id" => $userId]);
    $review = $stmt->fetch();
    
    if(!$review){
        echo json_encode(["success" => false, "message" => "Review not found or incorrect permissions."]);
        exit();
    }

    //SQL query to delete a review
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error. Try again."]);
}
?>