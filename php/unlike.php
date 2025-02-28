<?php
session_start();
header("Content-Type: application/json");

//Check if logged in
if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

require "config.php";

//Check if Review ID exists
if(!isset($_GET["review_id"])){
    echo json_encode(["success" => false, "message" => "Review ID required"]);
    exit();
}

$userId = $_SESSION["user_id"];
$reviewId = intval($_GET["review_id"]);

try{
    //Unike
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = :user_id AND review_id = :review_id");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId]);

    //Update like count
    $stmt = $conn->prepare("UPDATE reviews SET likes = likes - 1 WHERE id = :review_id");
    $stmt->execute(["review_id" => $reviewId]);
    
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error. Try agian!!"]);
}
?>