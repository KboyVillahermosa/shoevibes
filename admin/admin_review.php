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

// Fetch all reviews
$sql = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

// Calculate total reviews and average rating
$sql_stats = "SELECT rating, COUNT(*) AS count FROM reviews GROUP BY rating";
$result_stats = $conn->query($sql_stats);
$rating_counts = [];
$total_reviews = 0;
$total_rating = 0;

while ($row = $result_stats->fetch_assoc()) {
    $rating_counts[$row['rating']] = intval($row['count']);
    $total_reviews += intval($row['count']);
    $total_rating += intval($row['rating']) * intval($row['count']);
}
$average_rating = ($total_reviews > 0) ? round($total_rating / $total_reviews, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Review Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-3xl font-bold mb-4">Admin Review Panel</h2>
        <div class="mb-4 p-4 bg-blue-100 rounded">
            <h3 class="text-xl font-semibold">Total Reviews: <?= $total_reviews ?></h3>
            <h3 class="text-xl font-semibold">Average Rating: <?= $average_rating ?> / 5</h3>
        </div>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">Shoe Name</th>
                    <th class="py-2 px-4 border">Reviewer</th>
                    <th class="py-2 px-4 border">Rating</th>
                    <th class="py-2 px-4 border">Review</th>
                    <th class="py-2 px-4 border">Image</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border">
                        <td class="py-2 px-4 border">Shoe Name Placeholder</td>
                        <td class="py-2 px-4 border"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="py-2 px-4 border text-center"><?= $row['rating'] ?> / 5</td>
                        <td class="py-2 px-4 border"> <?= nl2br(htmlspecialchars($row['review'])) ?> </td>
                        <td class="py-2 px-4 border text-center">
                            <?php if ($row['image']): ?>
                                <img src="<?= htmlspecialchars($row['image']) ?>" alt="Review Image" class="w-20 h-20 object-cover">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-4 border text-center">
                            <form action="delete_review.php" method="POST">
                                <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
