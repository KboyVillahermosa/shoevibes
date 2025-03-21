<?php
header('Content-Type: application/json'); // Ensure JSON output
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../database.php'; // Ensure this path is correct

if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode(["error" => "No query provided"]);
    exit();
}

$query = "%" . $_GET['query'] . "%";

// Check if the database connection is successful
if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Prepare the SQL query
$sql = "SELECT product_id, product_name, price, image_url FROM products WHERE product_name LIKE ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Database query failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $query);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// If no products found, return an empty array
echo json_encode($products);
exit();
?>
