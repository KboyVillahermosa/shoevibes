<?php
session_start(); // Start the session at the very beginning

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoevibe_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$reviewSubmitted = false;
$submissionError = "";

// Function to generate a unique token
function generate_token() {
    return bin2hex(random_bytes(32)); // Generates a 64-character hexadecimal string
}

// Create a token if one doesn't exist in the session
if (!isset($_SESSION['form_token'])) {
    $_SESSION['form_token'] = generate_token();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate the token
    if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
        $submissionError = "Invalid submission. Please try again."; // Changed error message for clarity
    } else {
        // Token is valid, process the form
        if (isset($_POST['name']) && !empty($_POST['name']) &&
            isset($_POST['email']) && !empty($_POST['email']) &&
            isset($_POST['rating']) && !empty($_POST['rating']) &&
            isset($_POST['review']))
        {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $rating = (int)$_POST['rating']; // Ensure rating is an integer
            $review_title = isset($_POST['review_title']) ? $_POST['review_title'] : ''; // Optional field
            $review = $_POST['review'];

            $imagePath = NULL;
            if (!empty($_FILES['image']['name'])) {
                $targetDir = "uploads/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                $imagePath = $targetDir . uniqid() . "." . $imageFileType;
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                    $imagePath = NULL;
                }
            }

            // Improved error handling during database interaction
            try {
                $stmt = $conn->prepare("INSERT INTO shoe3_reviews (name, email, rating, review_title, review, image) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("ssisss", $name, $email, $rating, $review_title, $review, $imagePath);
                if ($stmt->execute()) {
                    $reviewSubmitted = true;

                    // Invalidate the token after successful submission
                    unset($_SESSION['form_token']);
                    $_SESSION['form_token'] = generate_token(); // Generate a new token for the next submission
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                $stmt->close();
            } catch (Exception $e) {
                $submissionError = "Error: " . $e->getMessage();
            }
        } else {
            $submissionError = "Please fill in all required fields.";
        }
    }
}

$sql = "SELECT * FROM shoe3_reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

// Calculate star rating statistics
$sql_stats = "SELECT rating, COUNT(*) AS count FROM shoe3_reviews GROUP BY rating";
$result_stats = $conn->query($sql_stats);
$rating_counts = [];
$total_reviews = 0;
$total_rating = 0; // Add this line
while ($row = $result_stats->fetch_assoc()) {
    $rating_counts[$row['rating']] = intval($row['count']);
    $total_reviews += intval($row['count']);
    $total_rating += intval($row['rating']) * intval($row['count']); // And this line
}

// Calculate average rating
$average_rating = ($total_reviews > 0) ? round($total_rating / $total_reviews, 1) : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.5.4/dist/flowbite.min.js"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Customer Reviews</h2>

    <?php if (!empty($submissionError)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($submissionError) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($reviewSubmitted): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Thank you!</strong>
            <span class="block sm:inline">Your review has been submitted.</span>
        </div>
    <?php endif; ?>

     <!-- Display Average Rating -->
     <div class="mb-4">
        <h3 class="text-xl font-semibold text-gray-700">Average Rating: <?= $average_rating ?> / 5 (<?= $total_reviews ?> reviews)</h3>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <!-- Add the hidden token field -->
        <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($_SESSION['form_token']); ?>">

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Rating:</label>
            <div class="flex items-center">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="hidden" required>
                    <label for="star<?= $i ?>" class="text-3xl cursor-pointer text-gray-400 hover:text-black focus:text-black">
                        <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 1c1.939 0 3.683 1.476 4.489 3.955l6.572.955-4.756 4.645 1.123 6.545z"/></svg>
                    </label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="review_title">Review Title:</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="review_title" type="text" name="review_title">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="review">Review:</label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="review" name="review" rows="4" required></textarea>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Upload Image (optional):</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4V12a4 4 0 014-4h16m-16 0h20v4m0 0v4m0 0v4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Upload a file</span>
                            <input id="image" name="image" type="file" class="sr-only">
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Submit Review
            </button>
        </div>
    </form>

    <hr class="my-8 border-gray-200">

    <h3 class="text-2xl font-bold mb-4 text-gray-800">Reviews:</h3>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span><?= htmlspecialchars($row['name']) ?></span>
                        <span class="mx-2">•</span>
                        <span><?= date("M d, Y", strtotime($row['created_at'])) ?></span>
                    </div>
                    <div class="flex items-center my-2">
                        <?php for ($i = 0; $i < $row['rating']; $i++): ?>
                            <svg class="w-4 h-4 fill-current text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 1c1.939 0 3.683 1.476 4.489 3.955l6.572.955-4.756 4.645 1.123 6.545z"/></svg>
                        <?php endfor; ?>
                        <span class="text-gray-600 ml-2">(<?= $row['rating'] ?>/5)</span>
                    </div>
                    <h4 class="font-bold text-lg mb-2 text-gray-800"><?= htmlspecialchars($row['review_title']) ?></h4>
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($row['review'])) ?></p>
                </div>
                <?php if ($row['image']): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Review Image" class="w-full h-48 object-cover">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const stars = document.querySelectorAll('input[name="rating"]');

            stars.forEach((star, index) => {
                star.addEventListener('change', () => {
                    stars.forEach((s, i) => {
                        const label = s.nextElementSibling;
                        if (i <= index) {
                            label.classList.add('text-yellow-500');
                        } else {
                            label.classList.remove('text-yellow-500');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
