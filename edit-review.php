<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.html");
    exit();
}

require "php/config.php";

$reviewId = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if($reviewId === 0) {
    die("Review ID invalid.");
}

try {
    $stmt = $conn->prepare("
        SELECT id, title, media_type, media_title, media_creator, media_year, rating, summary, tags, image_url
        FROM reviews
        WHERE id = :review_id AND user_id = :user_id
    ");
    $stmt->execute(["review_id" => $reviewId, "user_id" => $_SESSION["user_id"]]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$review){
        die("Missing review or editing permission.");
    }
} catch (PDOException $e) {
    die("Error fetching review: " . $e->getMessage());
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $title = trim($_POST["title"]);
    $mediaType = trim($_POST["media_type"]);
    $mediaTitle = trim($_POST["media_title"]);
    $mediaCreator = trim($_POST["media_creator"]);
    $mediaYear = intval($_POST["media_year"]);
    $rating = intval($_POST["rating"]);
    $summary = trim($_POST["summary"]);
    $tags = trim($_POST["tags"]);
    $imageUrl = trim($_POST["image_url"]);

    if(empty($title) || empty($mediaType) || empty($mediaTitle) || empty($mediaCreator) || $mediaYear < 0 || $mediaYear > 2025 || $rating < 1 || $rating > 5 || empty($summary)) {
        die("Please fill all fields correctly.");
    }

    try {
        $stmt = $conn->prepare("
            UPDATE reviews
            SET title = :title,
                media_type = :media_type,
                media_title = :media_title,
                media_creator = :media_creator,
                media_year = :media_year,
                rating = :rating,
                summary = :summary,
                tags = :tags,
                image_url = :image_url
            WHERE id = :review_id AND user_id = :user_id
        ");
        $stmt->execute([
            "title" => $title,
            "media_type" => $mediaType,
            "media_title" => $mediaTitle,
            "media_creator" => $mediaCreator,
            "media_year" => $mediaYear,
            "rating" => $rating,
            "summary" => $summary,
            "tags" => $tags,
            "image_url" => $imageUrl,
            "review_id" => $reviewId,
            "user_id" => $_SESSION["user_id"]
        ]);

        //Go back to profile after updating
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating review: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>MedRev - Edit Review</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/global.css">
        <link rel="stylesheet" type="text/css" href="css/edit-review.css">
    </head>
    <body>
        <header>
            <h1>MedRev - Edit Review</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="browse.html">Browse</a></li>
                    <li><a href="submit.php">Submit</a></li>
                    <li><a href="notifications.php">Notifications</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="about.html">About/Contact</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <h2>Edit Review</h2>
            <form id="edit-review-form" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>Media Details</legend>
                    <label for="media_type">Media Type:</label>
                    <select id="media_type" name="media_type" required>
                        <option value="Book" <?php echo $review["media_type"] === "Book" ? "selected" : ""; ?>>Book</option>
                        <option value="Movie" <?php echo $review["media_type"] === "Movie" ? "selected" : ""; ?>>Movie</option>
                        <option value="Tv_show" <?php echo $review["media_type"] === "Tv_show" ? "selected" : ""; ?>>TV Show</option>
                        <option value="Game" <?php echo $review["media_type"] === "Game" ? "selected" : ""; ?>>Game</option>
                        <option value="Videogame" <?php echo $review["media_type"] === "Videogame" ? "selected" : ""; ?>>Videogame</option>
                        <option value="Podcast" <?php echo $review["media_type"] === "Podcast" ? "selected" : ""; ?>>Podcast</option>
                        <option value="Art" <?php echo $review["media_type"] === "Art" ? "selected" : ""; ?>>Art</option>
                        <option value="Music" <?php echo $review["media_type"] === "Music" ? "selected" : ""; ?>>Music</option>
                    </select>

                    <label for="media_title">Media Title:</label>
                    <input type="text" id="media_title" name="media_title" value="<?php echo htmlspecialchars($review["media_title"]); ?>" required>

                    <label for="media_creator">Media Creator:</label>
                    <input type="text" id="media_creator" name="media_creator" value="<?php echo htmlspecialchars($review["media_creator"]); ?>" required>
                    
                    <label for="media_year">Media Year:</label>
                    <input type="number" id="media_year" name="media_year" value="<?php echo htmlspecialchars($review["media_year"]); ?>" min="0", max="2025" required>
                </fieldset>
                
                <fieldset>
                    <legend>Review Details</legend>
                    <label for="title">Review Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($review["title"]); ?>" required>
                
                    <label for="rating">Rating:</label>
                    <input type="number" id="rating" name="rating" value="<?php echo htmlspecialchars($review["rating"]); ?>" min="1", max="5" required>
                    <label for="summary">Summary:</label>
                    <textarea id="summary" name="summary" rows="4" required><?php echo htmlspecialchars($review["summary"]); ?></textarea>
                </fieldset>
                
                <fieldset>
                    <legend>Optional Information</legend>
                    <label for="tags">Tags:</label>
                    <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($review["tags"]); ?>">
                    <label for="image_url">Image URL:</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($review["image_url"]); ?>">
                </fieldset>
                
                <button type="submit">Update Review</button>
            </form>
        </main>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
    </body>
</html>