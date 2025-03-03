<?php
session_start();
header("Content-Type: application/json");
require "config.php";

//Check if user logged in
if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

//Check if Review ID exists
if(!isset($_GET["review_id"])){
    error_log("Review ID missing.");
    echo json_encode(["success" => false, "message" => "Review ID required!"]);
    exit();
}

$userId = $_SESSION["user_id"];
$reviewId = intval($_GET["review_id"]);
error_log("User ID: $userId, ReviewID: $reviewId"); //For debugging

try{
    //Check if like already exists
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = :user_id AND review_id = :review_id");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId]);
    $existingLike = $stmt->fetch();

    if($existingLike){
        error_log("User already liked review");
        echo json_encode(["success" => false, "message" => "Already liked this review!"]);
        exit();
    }

    //SQL query to add like to likes table
    $stmt = $conn->prepare("INSERT INTO likes (user_id, review_id) VALUES (:user_id, :review_id)");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId]);

    //SQL query to update review's like count in reviews table
    $stmt = $conn->prepare("UPDATE reviews SET likes = likes + 1 WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);

    //Get review owner's ID
    $stmt = $conn->prepare("SELECT user_id FROM reviews WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);
    $reviewOwner = $stmt->fetch();

    //SQL query to add a notification for the review owner
    if($reviewOwner){
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, source_user_id, review_id, content)
            VALUES (:user_id, 'like', :source_user_id, :review_id, NULL)
        ");
        $stmt->execute([
            "user_id" => $reviewOwner["user_id"],
            "source_user_id" => $userId,
            "review_id" => $reviewId,
        ]);
    }
    
    error_log("Like added"); //For debugging
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error. Try again."]);
}
?>