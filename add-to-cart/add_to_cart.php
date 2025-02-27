<?php

include '../database.php'; // Ensure database connection is included

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']); // Ensure it's an integer
    $size = $_POST['size'];

    // Fetch product details from the database
    $sql = "SELECT product_name, price, image_url FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        $item = [
            'product_id' => $product_id,
            'name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'size' => $size,
            'image_url' => $product['image_url']
        ];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if item exists in cart (same product and size)
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
        echo "Error: Product not found.";
        exit;
    }

    header('Location: add_to_cart.php');
    exit;
}

// Handle item deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $index = intval($_POST['index']);
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
    <link rel="stylesheet" href="../css/shoes.css"> <!-- Ensure this path is correct -->
    <title>Shopping Cart</title>
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-white shadow-sm p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="../index.php" class="text-xl font-bold">
                <img src="../image/logo4.png" class="h-16" alt="Logo">
            </a>
            <span class="bg-blue-500 text-white px-4 py-2 rounded">
                Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)
            </span>
        </div>
    </nav>

    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4">Your Shopping Cart</h1>
        
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="mb-4 pb-4 border-b flex justify-between items-center">
                        <div class="flex items-center">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product Image" class="h-16 w-16 object-cover mr-4">
                            <div>
                                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($item['name']); ?></h2>
                                <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                <p>Quantity: <?php echo intval($item['quantity']); ?></p>
                                <p>Price: ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
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
                        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart']));
                        echo number_format($total, 2);
                    ?></h3>
                </div>
            </div>
        <?php else: ?>
            <p class="text-gray-500">Your cart is empty.</p>
        <?php endif; ?>

        <a href="../shoes-preview/shoes1.php" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Continue Shopping</a>
    </div>

    <script>
        window.onload = function () {
            let inputField = document.getElementById("some-input-id");  
            if (inputField) {  
                inputField.value = "some value";  
            }  
        };
    </script>
</body>
</html>
