<?php
session_start();
include 'database.php'; // Adjust the path if needed

// Function to get cart total
function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-4">Your Shopping Cart</h1>

        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="w-full h-32 object-cover mb-2">
                        <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h2>
                        <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p>Total: ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?> Php</p>
                        <form method="post" action="shoes1.php?product_id=<?php echo $item['product_id']; ?>&show_cart=true">
                            <input type="hidden" name="remove_item" value="<?php echo $index; ?>">
                            <button type="submit" class="bg-black text-white rounded-md py-2 px-4 mt-4 hover:bg-gray-800">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <h2 class="text-xl font-bold">Cart Total: ₱<?php echo number_format(getCartTotal(), 2); ?> Php</h2>
                <a href="index.php" class="bg-black text-white rounded-md py-2 px-4 hover:bg-gray-800">Continue Shopping</a>
            </div>
        <?php else: ?>
            <p class="text-gray-600">Your cart is empty.</p>
            <a href="index.php" class="inline-block mt-4 bg-black text-white rounded-md py-2 px-4 hover:bg-gray-800">Continue Shopping</a>
        <?php endif; ?>
    </div>
</body>

</html>
