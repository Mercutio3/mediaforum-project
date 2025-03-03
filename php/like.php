<?php
session_start();
header("Content-Type: application/json");

//Check if user logged in
if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

require "config.php";

if(!isset($_GET["review_id"])){
    error_log("Reviwew ID missing.");
    echo json_encode(["success" => false, "message" => "Review ID required!"]);
    exit();
}

$userId = $_SESSION["user_id"];
$reviewId = intval($_GET["review_id"]);

error_log("User ID: $userId, ReviewID: $reviewId");

try{
    //Check if already liked
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = :user_id AND review_id = :review_id");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId]);
    $existingLike = $stmt->fetch();

    if($existingLike){
        error_log("User already liked review");
        echo json_encode(["success" => false, "message" => "Already liked this review!"]);
        exit();
    }

    //Like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, review_id) VALUES (:user_id, :review_id)");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId]);

    //Update like count
    $stmt = $conn->prepare("UPDATE reviews SET likes = likes + 1 WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);

    //Get review owner ID
    $stmt = $conn->prepare("SELECT user_id FROM reviews WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);
    $reviewOwner = $stmt->fetch();

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
    
    error_log("Like added");
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error. Try agian!!"]);
}
?>