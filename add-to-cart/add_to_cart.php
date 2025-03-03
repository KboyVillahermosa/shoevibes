<?php
session_start(); // Make sure session_start() is at the very top
include '../database.php';

$response = array('status' => 'error', 'message' => 'Invalid request'); // Default response

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $size = isset($_POST['size']) ? filter_var($_POST['size'], FILTER_SANITIZE_STRING) : '';

    if ($product_id > 0 && !empty($size) && $quantity > 0) {
        // Fetch product details from the database
        $sql = "SELECT product_name, price, image_url FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();

                $item = [
                    'product_id' => $product_id,
                    'product_name' => $product['product_name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'size' => $size,
                    'image_url' => $product['image_url']
                ];

                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // Check if item already exists in cart (same product and size)
                $found = false;
                foreach ($_SESSION['cart'] as &$cart_item) {
                    if ($cart_item['product_id'] == $product_id && $cart_item['size'] == $size) {
                        $cart_item['quantity'] += $quantity; // Update quantity
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $_SESSION['cart'][] = $item;
                }

                 // Calculate total items in cart
                 $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

                $response = array('status' => 'success', 'message' => 'Product added to cart', 'cart_count' => $cart_count);

            } else {
                $response = array('status' => 'error', 'message' => 'Product not found');
            }
            $stmt->close();

        } else {
            $response = array('status' => 'error', 'message' => 'Database error: ' . $conn->error);
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Invalid product ID, quantity, or size');
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $index = isset($_POST['index']) ? intval($_POST['index']) : -1;

    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
           // Calculate total items in cart
           $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
        $response = array('status' => 'success', 'message' => 'Item removed from cart',  'cart_count' => $cart_count);
    } else {
        $response = array('status' => 'error', 'message' => 'Invalid item index');
    }

}

header('Content-Type: application/json'); // Send JSON response
echo json_encode($response);
exit;
?>
