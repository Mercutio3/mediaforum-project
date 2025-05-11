<?php
//Make the page only accessible to logged-in users.
session_start();
if(!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}
?>

<!-- This is the notifications page. Notifications for when your reviews are liked or
 commented on appear here. You can mark them as read or filter them by type. -->
<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>MedRev - Notifications</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/global.css">
        <link rel="stylesheet" type="text/css" href="css/notifications.css">
    </head>
    <body>
        <header>
            <h1>MedRev - Notifications</h1>
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
            <h2>Notifications</h2>
            <section id="notification-filters">
                <button class="button-filter active" data-filter="all">All</button>
                <button class="button-filter" data-filter="like">Likes</button>
                <button class="button-filter" data-filter="comment">Comments</button>
            </section>
            
            <section id="notifications">
                <p id="no-notifications" class="hidden">You don't have notifications.</p>
            </section>
        </main>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
        <script src="javascript/notifications.js"></script>
    </body>
</html>