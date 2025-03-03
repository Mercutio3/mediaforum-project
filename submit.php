<?php
session_start();
//Make the page only accessible to logged-in users.
if(!isset($_SESSION["user_id"])){
    header("Location: login.html");
    exit();
}
?>

<!-- This is the submission page. Users enter their review details and submit. -->
<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Media Review Forum - Submit</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/global.css">
        <link rel="stylesheet" href="css/submit.css">
    </head>
    <body>
        <header>
            <h1>Media Review Forum - Submit</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="browse.html">Browse</a></li>
                    <li><a href="search.html">Search</a></li>
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
            <h2>Submit Review</h2>
            <form id="submission-form" action="/submit-review" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>Media Details</legend>
                    <label for="media-type">Type of Media:</label>
                    <select id="media-type" name="media_type" required>
                        <option value="">Select type...</option>
                        <option value="movie">Movie</option>
                        <option value="tvshow">TV Show</option>
                        <option value="book">Book</option>
                    </select>

                    <label for="media-title">Title:</label>
                    <input type="text" id="media-title" name="media_title" placeholder="Enter title..." required>

                    <label for="media-title">Creator:</label>
                    <input type="text" id="media-creator" name="media_creator" placeholder="Enter creator..." required>

                    <label for="media-title">Year:</label>
                    <input type="number" id="media-year" name="media_year" min="0" max="2025" placeholder="Enter year..." required>
                </fieldset>
                
                <fieldset>
                    <legend>Review Content</legend>
                    <label for="review-title">Review Title:</label>
                    <input type="text" id="review-title" name="title" placeholder="Enter title..." required>
                    <label for="rating">Rating:</label>
                    <select id="rating" name="rating" required>
                        <option value="">Select Rating:</option>
                        <option value="1">*----</option>
                        <option value="2">**---</option>
                        <option value="3">***--</option>
                        <option value="4">****-</option>
                        <option value="5">*****</option>
                    </select>
                    <label for="review-content">Review:</label>
                    <textarea id="review-content" name="summary" rows="10" placeholder="Write review..." required></textarea></textarea>
                </fieldset>

                <fieldset>
                    <legend>Optional Information</legend>
                    <label for="tags">Tags:</label>
                    <input type="text" id="tags" name="tags" placeholder="Add tags...">
                    <label for="media-image">Upload image:</label>
                    <input type="file" id="media-image" name="media-image" accept="image/*">
                </fieldset>

                <button type="submit">Submit</button>
            </form>

            <section id="preview">
                <h3>Review Preview</h3>
                <div id="review-preview"></div>
            </section>
        </main>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
        <script src="javascript/submit.js"></script>
    </body>
</html>