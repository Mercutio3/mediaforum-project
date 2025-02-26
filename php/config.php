<?php
$host = "localhost";
$port = "8889";
$dbname = "media_review_forum";
$username = "root";
$password = "root";
$socket = "/Applications/MAMP/tmp/mysql/mysql.sock";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;unix_socket=$socket";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Couldn't connect to database: " . $e->getMessage());
}
?>