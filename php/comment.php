<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);

require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $data = json_decode(file_get_contents("php://input"), true);
    $reviewId = intval($data["review_id"]);
    $userId = intval($data["user_id"]);
    $content = htmlspecialchars($data["content"]);

    try {
        $stmt = $conn->prepare("INSERT INTO comments (review_id, user_id, content) VALUES (:review_id, :user_id, :content)");
        $stmt->execute([
            "review_id" => $reviewId,
            "user_id" => $userId,
            "content" => $content,
        ]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error. try again."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "INvalid request!"]);
}
?>