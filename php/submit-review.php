<?php
session_start();
header("Content-Type: application/json");
require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST") {
    //Check if user is logged in
    if(!isset($_SESSION["user_id"])){
        echo json_encode(["success" => false, "message" => "User not logged in."]);
        exit;
    }
    
    if(!$conn){
        echo json_encode(["success" => false, "message" => "Database connection failed."]);
        exit;
    }

    //Get review details
    $userId = $_SESSION["user_id"];
    $title = htmlspecialchars($_POST["title"]);
    $mediaType = htmlspecialchars($_POST["media_type"]);
    $mediaTitle = htmlspecialchars($_POST["media_title"]);
    $mediaCreator = htmlspecialchars($_POST["media_creator"]);
    $mediaYear = intval($_POST["media_year"]);
    $rating = intval($_POST["rating"]);
    $summary = htmlspecialchars($_POST["summary"]);
    $tags = htmlspecialchars($_POST["tags"] ?? "");
    $imageUrl = "";

    //Handle image uploading
    if(isset($_FILES["media-image"]) && $_FILES["media-image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        $uploadFile = $uploadDir . basename($_FILES["media-image"]["name"]);
        if(move_uploaded_file($_FILES["media-image"]["tmp_name"], $uploadFile)) {
            $imageUrl = $uploadFile;
        }
    }

    try {
        //SQL query to add a review to the reviews table
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, title, media_type, media_title, media_creator, media_year, rating, summary, tags, image_url) VALUES (:user_id, :title, :media_type, :media_title, :media_creator, :media_year, :rating, :summary, :tags, :image_url)");
        $stmt->execute([
            "user_id" => $userId,
            "title" => $title,
            "media_type" => $mediaType,
            "media_title" => $mediaTitle,
            "media_creator" => $mediaCreator,
            "media_year" => $mediaYear,
            "rating" => $rating,
            "summary" => $summary,
            "tags" => $tags,
            "image_url" => $imageUrl,
        ]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error. Please try again!"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "INvalid request."]);
}
?>