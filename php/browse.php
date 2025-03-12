<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set("display_errors", 0);
require "config.php";

try {
    $query = isset($_GET["query"]) ? trim($_GET["query"]) : "";

    //SQL query to get all reviews in the database
    $sql = "
        SELECT reviews.*, users.username
        FROM reviews
        JOIN users ON reviews.user_id = users.id
    ";

    if(!empty($query)){
        $sql .= " WHERE reviews.title LIKE :query OR reviews.summary LIKE :query OR reviews.tags LIKE :query";
    }

    $stmt = $conn->prepare($sql);

    if(!empty($query)){
        $searchQuery = "%" . $query . "%";
        $stmt->bindValue(":query", $searchQuery, PDO::PARAM_STR);
    }

    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "reviews" => $reviews]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error. Try again."]);
}
?>