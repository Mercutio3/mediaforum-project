<?php
//Make the page only accessible to logged-in users.
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location: login.html");
    exit();
}
$username = $_SESSION["username"];
$email = $_SESSION["email"];
?>

<!-- This is the account page. A logged-in user's accont page is only visible
 to them. Here they can change their profile info, password, privacy, and
 delete their account. -->
<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Media Review Forum - Account</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/global.css">
        <link rel="stylesheet" type="text/css" href="css/account.css">
    </head>
    <body>
        <header>
            <h1>Media Review Forum - Account</h1>
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
            <h2>Account Settings</h2>
            <section id="account-info">
                <p>Username: <?php echo htmlspecialchars($username); ?></p>
                <p>Email: <?php echo htmlspecialchars($email); ?></p>
            </section>

            <section id="edit-account">
                <h3>Change Profile Info</h3>
                <form id="edit-account-form" enctype="multipart/form-data">
                    <label for="bio">Bio:</label>
                    <textarea id="bio" name="bio" rows="5" placeholder="User bio..."></textarea>
                    <label for="profile-picture">Profile Picture:</label>
                    <input type="file" id="profile-picture" name="profile-picture" accept="image/*">
                    <button type="submit">Save</button>
                </form>
            </section>

            <section id="account-password">
                <h3>Change Password</h3>
                <form id="account-password-form">
                    <label for="current-password">Current Password:</label>
                    <input type="password" id="current-password" name="current-password" required>
                    <label for="new-password">New Password:</label>
                    <input type="password" id="new-password" name="new-password" required>
                    <label for="repeat-new-password">Repeat New Password:</label>
                    <input type="password" id="repeat-new-password" name="repeat-new-password" required>
                    <button type="submit">Change Password</button>
                </form>
            </section>

            <section id="account-privacy">
                <h3>Privacy Settings</h3>
                <form id="account-privacy-form" action="/privacy-settings" method="POST">
                    <label>
                        <input type="checkbox" name="account-public" checked>
                        Public Profile
                    </label>
                    <label>
                        <input type="checkbox" name="account-public">
                        Email Notifications
                    </label>
                    <button type="submit">Save</button>
                </form>
            </section>

            <section id="delete-account">
                <h3>Delete Account</h3>
                <button id="delete-account-button">Delete</button>
            </section>
        </main>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
        <script src="../javascript/account.js"></script>
    </body>
</html>