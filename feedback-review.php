<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submitted</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md max-w-md w-full">
        <?php if (isset($_SESSION['submission_success']) && $_SESSION['submission_success']): ?>
            <h1 class="text-2xl font-bold text-green-600 mb-4">Thank You!</h1>
            <p class="text-gray-700 mb-4">Your review has been successfully submitted. We appreciate your feedback!</p>
            <?php unset($_SESSION['submission_success']); // Clear the session variable ?>
        <?php else: ?>
            <h1 class="text-2xl font-bold text-red-600 mb-4">Oops!</h1>
            <p class="text-gray-700 mb-4">It seems you've reached this page in error. Please submit a review first.</p>
        <?php endif; ?>
        <a href="review.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Back to Reviews</a>
    </div>
</body>
</html>
