<?php
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product details from request
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$size = isset($_POST['size']) ? $_POST['size'] : '';

// Fetch product details from the database
include '../database.php';
$sql = "SELECT product_id, product_name, price, image_url FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    // Prepare cart item
    $cart_item = [
        'product_id' => $product['product_id'],
        'product_name' => $product['product_name'],
        'price' => $product['price'],
        'image_url' => $product['image_url'],
        'quantity' => $quantity,
        'size' => $size
    ];

    // Add item to session cart
    $_SESSION['cart'][] = $cart_item;

    // Send response
    echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}

$conn->close();
?>
