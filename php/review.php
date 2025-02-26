<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);

require "config.php";

if(isset($_GET["id"])){
    $reviewId = intval($_GET["id"]);

    try {
        $stmt = $conn->prepare("
            SELECT reviews.*, users.username
            FROM reviews 
            JOIN users ON reviews.user_id = users.id
            WHERE reviews.id = :id
        ");
        $stmt->execute(["id" => $reviewId]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if($review){
            $stmt = $conn->prepare("
                SELECT comments.*, users.username
                FROM comments 
                JOIN users ON comments.user_id = users.id
                WHERE comments.review_id = :review_id
            ");
            $stmt->execute(["review_id" => $reviewId]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "review" => $review,
                "comments" => $comments
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Review not found."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error! Try again."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Review ID required."]);
}
?>