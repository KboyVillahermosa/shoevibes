<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoevibe_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['review_id'])) {
    $review_id = intval($_POST['review_id']);

    // Delete the review
    $sql = "DELETE FROM reviews WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Review deleted successfully!";
    } else {
        $_SESSION['message'] = "Failed to delete review.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the admin panel
    header("Location: admin_reviews.php");
    exit();
}
?>
