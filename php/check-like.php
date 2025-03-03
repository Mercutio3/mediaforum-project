<?php
session_start();
header("Content-Type: application/json");
require "config.php";

//Check if user is logged in
if(!isset($_SESSION["user_id"])){
    echo json_encode(["success" => false, "message" => "Not logged in!"]);
    exit();
}

//Check if review ID exists
if (!isset($_GET["review_id"])) {
    echo json_encode(["success" => false, "message" => "Review ID required."]);
    exit();
}

$userId = $_SESSION["user_id"];
$reviewId = intval($_GET["review_id"]);

try {
    //SQL query to check if a user has liked a review
    $stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = :user_id AND review_id = :review_id");
    $stmt->execute(["user_id" => $userId, "review_id" => $reviewId]);
    $like = $stmt->fetch();

    echo json_encode(["success" => true, "liked" => !!$like]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error. Try again."]);
}
?>