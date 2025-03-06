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
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet">

</head>
<style>
     
        .admin-review{
            margin-top: 150px;
        }
       
        
</style>
<body class="">
     <?php include_once"admin_nav.php"; ?>
    <section class="admin-review p-4 sm:ml-64">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <h2 class="text-3xl font-bold mb-4">Admin Review Panel</h2>
            <div class="mb-4 p-4 bg-blue-100 rounded">
                <h3 class="text-xl font-semibold">Total Reviews: <?= $total_reviews ?></h3>
                <h3 class="text-xl font-semibold">Average Rating: <?= $average_rating ?> / 5</h3>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Shoe Name</th>
                        <th class="px-6 py-3">Reviewer</th>
                        <th class="px-6 py-3">Rating</th>
                        <th class="px-6 py-3">Review</th>
                        <th class="px-6 py-3">Image</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">Shoe Name Placeholder</td>
                            <td class="px-6 py-4"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="px-6 py-4 text-center"><?= $row['rating'] ?> / 5</td>
                            <td class="px-6 py-4"> <?= nl2br(htmlspecialchars($row['review'])) ?> </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($row['image']): ?>
                                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Review Image"
                                        class="w-20 h-20 object-cover">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <form action="delete_review.php" method="POST">
                                    <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                                    <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>

</html>