<?php
header("Content-Type: application/json");
require "config.php";

try {
    //SQL query to get trending reviews from database
    //The "trending" reviews are the 6 with the greatest "engagement points"
    //A like is worth one point, and a comment is worth two points
    $stmt = $conn->prepare("
        SELECT
            reviews.*,
            users.username AS poster,
            COUNT(DISTINCT likes.id) AS like_count,
            COUNT(DISTINCT comments.id) AS comment_count,
            (COUNT(DISTINCT likes.id) * 1 + COUNT(DISTINCT comments.id) * 2) AS trending_score
        FROM reviews
        LEFT JOIN likes ON reviews.id = likes.review_id
        LEFT JOIN comments ON reviews.id = comments.review_id
        JOIN users ON reviews.user_id = users.id
        GROUP BY reviews.id
        ORDER BY trending_score DESC
        LIMIT 4
    ");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["success" => true, "reviews" => $reviews]);
} catch (PDOException $e){
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error. Please try again."]);
}
?>