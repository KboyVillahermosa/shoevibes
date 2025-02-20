
<?php
$host = "localhost";
$user = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (leave blank)
$dbname = "shoevibe_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

