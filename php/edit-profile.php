<?php
session_start();

if(!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit();
}

require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $userId = $_SESSION["user_id"];
    $bio = $_POST["bio"];

    $profilePictureUrl = null;
    if (isset($_FILES["profile-picture"]) && $_FILES["profile-picture"]["error"] === UPLOAD_ERR_OK){
        $uploadDir = "uploads/profile-pictures/";
        if(!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $uploadFile = $uploadDir . basename($_FILES["profile-picture"]["name"]);
        if(move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $uploadFile)){
            $profilePictureUrl = $uploadFile;
        }
    }

    try {
        $stmt = $conn->prepare("UPDATE users SET bio = :bio, profile_picture = :profile_picture WHERE id = :id");
        $stmt->execute([
            "bio" => $bio,
            "profile_picture" => $profilePictureUrl,
            "id" => $userId,
        ]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error updating profile, try again."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>