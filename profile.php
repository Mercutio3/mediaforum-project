<?php
//Make the page only accessible to logged-in users.
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: login.html");
    exit();
}

require "php/config.php";

$profileUserId = isset($_GET["user_id"]) ? intval($_GET["user_id"]) : $_SESSION["user_id"];

try {
    //Get user details from users table
    $stmt = $conn->prepare("SELECT username, bio, profile_picture FROM users WHERE id = :id");
    $stmt->execute(["id" => $profileUserId]);
    $user = $stmt->fetch();

    if(!$user){
        die("User not found!");
    }

    $username = $user["username"];
    $bio = $user["bio"];

    //If a user doesn't have a profile picture, display the default
    $profilePicture = $user["profile_picture"] ? $user["profile_picture"] : "images/default-pp.png";

    //Get likes received for past 7 days
    $stmt = $conn->prepare("
        SELECT DATE(likes.created_at) AS date, COUNT(likes.id) AS likes_received
        FROM likes
        JOIN reviews ON likes.review_id = reviews.id
        WHERE reviews.user_id = :user_id AND likes.created_at >= NOW() - INTERVAL 7 DAY
        GROUP BY DATE(likes.created_at)
    ");
    $stmt->execute(["user_id" => $profileUserId]);
    $likesReceived = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $likesReceivedCount = array_sum(array_column($likesReceived, "likes_received"));

    //Get likes given for past 7 days
    $stmt = $conn->prepare("
        SELECT DATE(created_at) AS date, COUNT(id) AS likes_given
        FROM likes
        WHERE user_id = :user_id AND created_at >= NOW() - INTERVAL 7 DAY
        GROUP BY DATE(created_at)
    ");
    $stmt->execute(["user_id" => $profileUserId]);
    $likesGiven = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $likesGivenCount = array_sum(array_column($likesGiven, "likes_given"));

    //Get reviews posted for past 7 days
    $stmt = $conn->prepare("
        SELECT DATE(created_at) AS date, COUNT(id) AS reviews_posted
        FROM reviews
        WHERE user_id = :user_id AND created_at >= NOW() - INTERVAL 7 DAY
        GROUP BY DATE(created_at)
    ");
    $stmt->execute(["user_id" => $profileUserId]);
    $reviewsPosted = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

try {
    //Get all reviews (and their details) posted by a user
    $stmt = $conn->prepare("SELECT id, title, media_type, rating, summary, created_at FROM reviews WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(["user_id" => $profileUserId]);
    $reviews = $stmt->fetchAll();
} catch (PDOException $e){
    die("Error fetching reivews: " . $e->getMessage());
}
?>

<!-- This is the profile page. It displays user info, statistics, and reviews posted. -->
<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Media Review Forum - Profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/global.css">
        <link rel="stylesheet" type="text/css" href="css/profile.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
        <header>
            <h1>Media Review Forum - Profile</h1>
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
                        <p><?php echo $likesReceivedCount; ?></p>
                    </div>
                    <div class="user-stat">
                        <h3>Likes Given</h3>
                        <p><?php echo $likesGivenCount; ?></p>
                    </div>
                    <div class="user-stat">
                        <h3>Reviews Posted</h3>
                        <p><?php echo count($reviews); ?></p>
                    </div>
                </div>
                <br>
                <h2>Activity (Last 7 Days)</h2>
                <div class="stats-charts">
                    <div class="stat-chart-div">
                        <canvas id="likesReceivedChart"></canvas>
                    </div>
                    <div class="stat-chart-div">
                        <canvas id="likesGivenChart"></canvas>
                    </div>
                    <div class="stat-chart-div">
                        <canvas id="reviewsPostedChart"></canvas>
                    </div>
                </div>
            </section>

            <section id="profile-reviews">
                <h2>Reviews</h2>
                <div class="profile-reviews-grid">
                    <?php foreach ($reviews as $review): ?>
                        <article class="profile-review-card" data-id="<?php echo $review["id"]; ?>">
                            <h3><a href="review.html?id=<?php echo $review["id"]; ?>"><?php echo htmlspecialchars($review["title"]); ?></a></h3>
                            <p class="media-type"><?php echo htmlspecialchars($review["media_type"]); ?></p>
                            <p class="rating"><?php echo $review["rating"]; ?>/5</p>
                            <p class="summary"><?php echo htmlspecialchars($review["summary"]); ?></p>
                            <footer>
                                <span>Published on <?php echo date("F j, Y", strtotime($review["created_at"])); ?></span>
                                <?php if ($profileUserId === $_SESSION["user_id"]): ?>
                                    <a href="edit-review.php?id=<?php echo $review["id"]; ?>">Edit</a>
                                    <button class="delete-review">Delete</button>
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
        <script>
            const userStats = {
                likesGiven: <?php echo json_encode($likesGiven); ?>,
                likesReceived: <?php echo json_encode($likesReceived); ?>,
                reviewsPosted: <?php echo json_encode($reviewsPosted); ?>
            };
        </script>
        <script src="javascript/profile.js"></script>
    </body>
</html>