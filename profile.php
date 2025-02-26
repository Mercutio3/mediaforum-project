<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.html");
    exit();
}

require "php/config.php";

$profileUserId = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : $_SESSION["user_id"];

try {
    $stmt = $conn->prepare("SELECT username, bio, profile_picture FROM users WHERE id = :id");
    $stmt->execute(["id" => $profileUserId]);
    $user = $stmt->fetch();

    if(!$user){
        die("User not found!");
    }

    $username = $user["username"];
    $bio = $user["bio"];
    $profilePicture = $user["profile_picture"] ? $user["profile_picture"] : "images/default-pp.png";
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

try {
    $stmt = $conn->prepare("SELECT id, title, media_type, rating, summary, created_at FROM reviews WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(["user_id" => $profileUserId]);
    $reviews = $stmt->fetchAll();
} catch (PDOException $e){
    die("Error fetching reivews: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Media Review Forum - Profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/global.css">
        <link rel="stylesheet" type="text/css" href="css/profile.css">
    </head>
    <body>
        <header>
            <h1>Media Review Forum - Profile</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="browse.html">Browse</a></li>
                    <li><a href="search.html">Search</a></li>
                    <li><a href="submit.php">Submit</a></li>
                    <li><a href="notifications.html">Notifications</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="about.html">About/Contact</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <main>
            <section id="profile-info">
                <div class="profile-picture">
                    <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
                </div>
                <div class="profile-details">
                    <h2><?php echo htmlspecialchars($username); ?></h2>
                    <p class="bio"><?php  echo htmlspecialchars($bio); ?></p>
                    <?php if ($profileUserId === $_SESSION["user_id"]): ?>
                        <a href="account.php" class="edit-profile-button">Edit Profile</a>
                    <?php endif; ?>
                </div>
            </section>
            <section id="profile-stats">
                <h2>User Stats</h2>
                <div class="user-stats">
                    <div class="user-stat">
                        <h3>Likes Received</h3>
                        <p>666</p>
                    </div>
                    <div class="user-stat">
                        <h3>Likes Given</h3>
                        <p>555</p>
                    </div>
                    <div class="user-stat">
                        <h3>Reviews Posted</h3>
                        <p><?php echo count($reviews); ?></p>
                    </div>
                </div>
            </section>

            <section id="profile-reviews">
                <h2>Reviews</h2>
                <div class="profile-reviews-grid">
                    <?php foreach ($reviews as $review): ?>
                        <article class="profile-review-card">
                            <h3><a href="review.html?id=<?php echo $review["id"]; ?>"><?php echo htmlspecialchars($review["title"]); ?></a></h3>
                            <p class="media-type"><?php echo htmlspecialchars($review["media_type"]); ?></p>
                            <p class="rating"><?php echo $review["rating"]; ?>/5</p>
                            <p class="summary"><?php echo htmlspecialchars($review["summary"]); ?></p>
                            <footer>
                                <span>Published on <?php echo date("F j, Y", strtotime($review["created_at"])); ?></span>
                                <?php if ($profileUserId === $_SESSION["user_id"]): ?>
                                    <a href="editReview.html?id=<?php echo $review["id"]; ?>">Edit</a>
                                    <a href="deleteReview.html?id=<?php echo $review["id"]; ?>">Delete</a>
                                <?php endif; ?>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <section id="profile-activity">
                <h2>User Activity</h2>
                <ul class="user-activity">
                    <li>Posted a review... <a href="review.html">Title</a></li>
                    <li>Posted a comment... <a href="review.html">Title</a></li>
                    <li>Gave a like... <a href="review.html">Title</a></li>
                </ul>
            </section>
        </main>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
        <script src=""></script>
    </body>
</html>