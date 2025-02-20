<?php
session_start();
include '../database.php'; // Ensure this file connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];

    // Fetch product details from database
    $sql = "SELECT product_name, price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        if (isset($product['product_name'])) {
            $item = [
                'product_id' => $product_id,
                'name' => $product['product_name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'size' => $size
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
        } else {
            echo "Error: Product name not found in database.";
            exit;
        }
    } else {
        echo "Error: Product not found.";
        exit;
    }

    header('Location: add_to_cart.php');
    exit;
}

// Handle item deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $index = $_POST['index'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1); // Remove the item
    }
    header('Location: add_to_cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Shopping Cart</title>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-white shadow-sm p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="shoes1.php" class="text-xl font-bold">ShoeVibes</a>
            <span class="bg-blue-500 text-white px-4 py-2 rounded">
                Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)
            </span>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4">Your Shopping Cart</h1>
        
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="mb-4 pb-4 border-b flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h2>
                            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            <p>Price: ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                        <form method="post" action="">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="remove_item" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Remove
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <div class="mt-4">
                    <h3 class="text-xl font-bold">Total: ₱<?php
                        $total = array_sum(array_map(function($item) {
                            return $item['price'] * $item['quantity'];
                        }, $_SESSION['cart']));
                        echo number_format($total, 2);
                    ?></h3>
                </div>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>

        <a href="../shoes-preview/shoes1.php" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Continue Shopping</a>
    </div>
</body>
</html>
