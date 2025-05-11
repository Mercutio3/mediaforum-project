<?php
session_start();
header("Content-Type: application/json");

if(!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Not logged in." ]);
    exit();
}

require "config.php";

$userId = $_SESSION["user_id"];
$data = json_decode(file_get_contents("php://input"), true);
$publicProfile = filter_var($data["public_profile"], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
error_log("converted setting: " . $publicProfile);

try {
    $stmt = $conn->prepare("UPDATE users SET public_profile = :public_profile WHERE id = :id");
    $stmt->execute([
        "public_profile" => $publicProfile,
        "id" => $userId,
    ]);

    if($stmt->rowCount() > 0){
        error_log("Privacy settings updated for user " . $userId);
        $_SESSION["public_profile"] = (bool)$publicProfile;
        echo json_encode(["success" => true]);
    } else {
        error_log("No rows updated for user " . $userId);
        echo json_encode(["success" => false, "message" => "No changes made."]);
    }

    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error updating privacy settings."]);
}
?>