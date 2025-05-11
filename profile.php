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
    $stmt = $conn->prepare("SELECT username, bio, profile_picture, public_profile FROM users WHERE id = :id");
    $stmt->execute(["id" => $profileUserId]);
    $user = $stmt->fetch();

    if(!$user){
        $errorMessage = "User not found!";
    } else {
        //Check if profile is public before displaying
        //If profile is private but belongs to you, it's still dispalyed.
        if(!$user["public_profile"] && $profileUserId !== $_SESSION["user_id"]) {
            $errorMessage = "This profile is not public.";
        } else {
            $username = $user["username"];
            $bio = $user["bio"];
            //If a user doesn't have a profile picture, display the default
            $profilePicture = $user["profile_picture"] ? $user["profile_picture"] : "images/default-pp.png";

            //Get total likes received
            $stmt = $conn->prepare("
                SELECT COUNT(likes.id) AS total_likes_received
                FROM likes
                JOIN reviews ON likes.review_id = reviews.id
                WHERE reviews.user_id = :user_id
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $likesReceivedTotal = $stmt->fetchColumn();

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

            //Get total likes given
            $stmt = $conn->prepare("
                SELECT COUNT(likes.id) AS total_likes_given
                FROM likes
                WHERE user_id = :user_id
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $likesGivenTotal = $stmt->fetchColumn();

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

            //Get total reviews posted
            $stmt = $conn->prepare("
                SELECT COUNT(id) AS total_reviews_posted
                FROM reviews
                WHERE user_id = :user_id
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $reviewsPostedTotal = $stmt->fetchColumn();

            //Get reviews posted for past 7 days
            $stmt = $conn->prepare("
                SELECT DATE(created_at) AS date, COUNT(id) AS reviews_posted
                FROM reviews
                WHERE user_id = :user_id AND created_at >= NOW() - INTERVAL 7 DAY
                GROUP BY DATE(created_at)
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $reviewsPosted = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //Get total comments posted
            $stmt = $conn->prepare("
                SELECT COUNT(id) AS total_comments_posted
                FROM comments
                WHERE user_id = :user_id
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $commentsPostedTotal = $stmt->fetchColumn();

            //Get comments posted for past 7 days
            $stmt = $conn->prepare("
                SELECT DATE(created_at) AS date, COUNT(id) AS comments_posted
                FROM comments
                WHERE user_id = :user_id AND created_at >= NOW() - INTERVAL 7 DAY
                GROUP BY DATE(created_at)
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $commentsPosted = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //Get total comments received
            $stmt = $conn->prepare("
                SELECT COUNT(comments.id) AS total_comments_received
                FROM comments
                JOIN reviews ON comments.review_id = reviews.id
                WHERE reviews.user_id = :user_id
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $commentsReceivedTotal = $stmt->fetchColumn();

            //Get comments received for past 7 days
            $stmt = $conn->prepare("
                SELECT DATE(comments.created_at) AS date, COUNT(comments.id) AS comments_received
                FROM comments
                JOIN reviews ON comments.review_id = reviews.id
                WHERE reviews.user_id = :user_id AND comments.created_at >= NOW() - INTERVAL 7 DAY
                GROUP BY DATE(comments.created_at)
            ");
            $stmt->execute(["user_id" => $profileUserId]);
            $commentsReceived = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    $errorMessage = "Error fetching user data: " . $e->getMessage();
}

try {
    //Get all reviews (and their details) posted by a user
    $stmt = $conn->prepare("SELECT id, title, media_type, rating, summary, created_at FROM reviews WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute(["user_id" => $profileUserId]);
    $reviews = $stmt->fetchAll();
} catch (PDOException $e){
    die("Error fetching reviews: " . $e->getMessage());
}
try {
    //Get all comments posted by a user and their respective reviews
    $stmt = $conn->prepare("
        SELECT comments.*, reviews.title AS review_title, reviews.id AS review_id
        FROM comments
        JOIN reviews ON comments.review_id = reviews.id
        WHERE comments.user_id = :user_id
        ORDER BY comments.created_at DESC
    ");
    $stmt->execute(["user_id" => $profileUserId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e){
    die("Error fetching comments: " . $e->getMessage());
}
?>

<!-- This is the profile page. It displays user info, statistics, and reviews posted. -->
<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>MedRev - Profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/global.css">
        <link rel="stylesheet" type="text/css" href="css/profile.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body>
        <header>
            <h1>MedRev - Profile</h1>
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
            <?php if (isset($errorMessage)): ?>
                <section id="profile-error">
                    <div class="error-box">
                        <h2>Oops!</h2>
                        <p><?php echo htmlspecialchars($errorMessage); ?></p>
                        <p><a href="index.html">Return Home</a></p>
                    </div>
                </section>
            <?php else: ?>
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
                        <p><?php echo $likesReceivedTotal; ?></p>
                    </div>
                    <div class="user-stat">
                        <h3>Likes Given</h3>
                        <p><?php echo $likesGivenTotal; ?></p>
                    </div>
                    <div class="user-stat">
                        <h3>Reviews Posted</h3>
                        <p><?php echo $reviewsPostedTotal; ?></p>
                    </div>
                    <div class="user-stat">
                        <h3>Comments Posted</h3>
                        <p><?php echo $commentsPostedTotal; ?></p>
                    </div>
                    <div class="user-stat">
                        <h3>Comments Received</h3>
                        <p><?php echo $commentsReceivedTotal; ?></p>
                    </div>
                </div>
                <br>
                <h2>User Activity (Last 7 Days)</h2>
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
                    <div class="stat-chart-div">
                        <canvas id="commentsPostedChart"></canvas>
                    </div>
                    <div class="stat-chart-div">
                        <canvas id="commentsReceivedChart"></canvas>
                    </div>
                </div>
            </section>

            <section id="profile-reviews">
                <h2>Reviews</h2>
                <div class="profile-reviews-grid">
                    <?php if (empty($reviews)): ?>
                        <p>This user has not posted any reviews.</p>
                    <?php else: ?>
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
                    <?php endif; ?>
                </div>
            </section>

            <section id="profile-comments">
                <h2>Comments</h2>
                <div class="profile-comments-grid">
                    <?php if (empty($comments)): ?>
                        <p>This user has not posted any comments.</p>
                    <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <article class="profile-comment-card">
                            <p><?php echo htmlspecialchars($comment["content"]); ?></p>
                            <footer>
                                <span>Posted on <a href="review.html?id=<?php echo $comment["review_id"]; ?>"><?php echo htmlspecialchars($comment["review_title"]); ?></a></span>
                                <span><?php echo date("F j, Y", strtotime($comment["created_at"])); ?></span>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; ?>
        </main>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
        <script>
            const userStats = {
                likesGiven: <?php echo json_encode($likesGiven); ?>,
                likesReceived: <?php echo json_encode($likesReceived); ?>,
                reviewsPosted: <?php echo json_encode($reviewsPosted); ?>,
                commentsPosted: <?php echo json_encode($commentsPosted); ?>,
                commentsReceived: <?php echo json_encode($commentsReceived); ?>
            };
        </script>
        <script src="javascript/profile.js"></script>
    </body>
</html>