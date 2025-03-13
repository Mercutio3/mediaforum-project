<?php
require "config.php";

if(isset($_GET["token"])) {
    $token = $_GET["token"];

    try {
        //SQL query to get user with matching verification token
        $stmt = $conn->prepare("SELECT * FROM users WHERE verification_token = :token");
        $stmt->execute(["token" => $token]);
        $user = $stmt->fetch();

        if($user){
            //Verify user and remove their token
            $stmt = $conn->prepare("UPDATE users SET verified = TRUE, verification_token = NULL WHERE id = :id");
            $stmt->execute(["id" => $user["id"]]);

            //Success
            $message = "Email succesfully verified.";
            $description = "Email succesfully verified! Redirecting to login page in <span id='countdown'>3</span> seconds.";
            $class = "success";
        } else {
            $message = "Error.";
            $description = "Invalid verification token.";
            $class = "error";
        }
    } catch (PDOException $e){
        $message = "Error.";
        $description = "Could not verify email. Please try again.";
        $class = "error";
    }
} else {
    $message = "Error.";
    $description = "Please provide a verification token.";
    $class = "error";
}
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Media Review Forum - Register</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/global.css">
        <link rel="stylesheet" type="text/css" href="../css/verify-email.css">
    </head>
    <body>
        <header>
            <h1>Media Review Forum - Register</h1>
            <nav>
                <ul>
                    <li><a href="../index.html">Home</a></li>
                    <li><a href="../browse.html">Browse</a></li>
                    <li><a href="../submit.php">Submit</a></li>
                    <li><a href="../notifications.php">Notifications</a></li>
                    <li><a href="../profile.php">Profile</a></li>
                    <li><a href="../account.php">Account</a></li>
                    <li><a href="../about.html">About/Contact</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>
        <div class="verify-div">
        <div class="message-box">
            <h1 class="<?php echo $class; ?>"><?php echo $message; ?></h1>
            <p><?php echo $description; ?></p>
            <?php if ($class === "success"): ?>
                <p> If you are not redirected, <a href="../login.html">click here</a>.</p>
                <script>
                    let countdown = 3;
                    const countdownElement = document.getElementById("countdown");
                    const interval = setInterval(() => {
                        countdown--;
                        countdownElement.textContent = countdown;
                        if(countdown <= 0){
                            clearInterval(interval);
                            window.location.href = "../login.html";
                        }
                    }, 1000);
                </script>
            <?php endif; ?>
        </div>
        </div>
        <footer>
            <p>&copy 2025 Santiago Ham</p>
        </footer>
    </body>
</html>