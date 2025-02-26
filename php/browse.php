<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);

require "config.php";

try {
    $stmt = $conn->query("
        SELECT reviews.*, users.username
        FROM reviews
        JOIN users ON reviews.user_id = users.id
    ");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "reviews" => $reviews]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error. Try again :3"]);
}
?>