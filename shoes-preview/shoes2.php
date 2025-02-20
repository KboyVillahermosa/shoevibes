<?php
include '../database.php'; // Database connection

// Initialize order message
$order_message = "";
$thank_you_message = ""; // Initialize thank you message

// Get the product ID from the URL, default to 1 if not provided
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 2;

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $productDetails = $result->fetch_assoc();
} else {
    $productDetails = null;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postalCode = $_POST['postalCode'];

    // Fetch product price from the database
    $sql_price = "SELECT price FROM products WHERE product_id = ?";
    $stmt_price = $conn->prepare($sql_price);
    $stmt_price->bind_param("i", $product_id);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();

    if ($result_price->num_rows > 0) {
        $product = $result_price->fetch_assoc();
        $price = $product['price'];
        $total_price = $quantity * $price;

        // Insert order into the database
        $sql_insert = "INSERT INTO orders (product_id, quantity, size, total_price, first_name, last_name, phone, email, street, barangay, city, province, postal_code)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iisdsssssssss", $product_id, $quantity, $size, $total_price, $firstName, $lastName, $phone, $email, $street, $barangay, $city, $province, $postalCode);

        if ($stmt_insert->execute()) {
            $order_message = "Order submitted successfully!";
            $thank_you_message = "Thank you for choosing ShoeVibes!"; // Set thank you message
            echo '<script>setTimeout(function(){ document.getElementById("thankYouModal").classList.remove("hidden"); }, 500);</script>';

        } else {
            $order_message = "Error submitting order: " . $stmt_insert->error;
        }
    } else {
        $order_message = "Error: Product not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Product Page</title>
    <style>
        /* Custom styles for smooth scrolling and modal */
        .modal-content {
            max-height: 80vh;
            overflow-y: auto;
            padding: 2rem;
            transition: all 0.3 ease;
            width: 90%;
            max-width: 800px;
        }

        .modal-background {
            backdrop-filter: blur(5px);
        }

        .size-button {
            @apply px-4 py-1 border rounded-lg transition-colors duration-200;
        }

        .size-button.selected {
            @apply bg-black text-white;
        }

        .size-button:hover {
            @apply bg-black text-white;
        }

        .quantity-button {
            @apply bg-gray-200 px-4 py-2 rounded-lg text-lg hover:bg-gray-300 transition-colors duration-200;
        }

        .add-to-cart-button {
            @apply w-full border border-gray-800 text-gray-800 py-3 rounded-lg hover:bg-gray-800 hover:text-white transition-colors duration-200;
        }

        .order-now-button {
            @apply w-full bg-black text-white py-3 rounded-lg flex items-center justify-center gap-2 hover:bg-gray-900 transition-colors duration-200;
        }

        .modal-input {
            @apply w-full border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500;
        }

        .modal-label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
<section>
    <nav class="bg-white border-gray-200 shadow-sm">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="../image/logo4.png" class="h-16" alt="Flowbite Logo"/>
            </a>
            <button data-collapse-toggle="navbar-default" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-black rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
                    <li>
                        <a href="#"
                           class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Home</a>
                    </li>
                    <li>
                        <a href="../"
                           class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Add
                            to Cart</a>
                    </li>
                    <li>
                        <a href="../add-to-cart/add_to_cart.php"
                           class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">
                            Add to Cart
                            <span id="cart-count"
                                  class="bg-red-500 text-white rounded-full px-2 py-0.5 text-xs ml-1 hidden">0</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
</section>

<section class="py-6 px-4 max-w-5xl mx-auto">
    <h1 class="mb-8 text-3xl font-extrabold text-gray-800">Product Page</h1>

    <div class="flex flex-col md:flex-row gap-8 items-start">
        <!-- Thumbnail images -->
        <div class="flex flex-row md:flex-col gap-2">
            <img src="../image/s1.png" alt="Shoe 1"
                 class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                 onclick="changeImage(this)">
            <img src="../image/s2.png" alt="Shoe 2"
                 class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                 onclick="changeImage(this)">
            <img src="../image/s3.png" alt="Shoe 3"
                 class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                 onclick="changeImage(this)">
        </div>

        <!-- Main product image -->
        <div class="flex-1">
            <?php if ($productDetails): ?>
                <img id="mainImage"
                     src="<?php echo htmlspecialchars($productDetails['image_url']); ?>"
                     alt="<?php echo htmlspecialchars($productDetails['product_name']); ?>"
                     class="w-full h-auto rounded-lg shadow-lg">
            <?php else: ?>
                <p class="text-red-500">Product not found.</p>
            <?php endif; ?>
        </div>
        ...
        <!-- Product details -->
        <div class="flex-1 bg-white p-6 rounded-lg shadow-lg">
            <?php if ($productDetails): ?>
                <h2 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($productDetails['product_name']); ?></h2>
                <div class="flex items-center mb-4">
                    <span class="text-yellow-500 text-lg">★★★★★</span>
                    <span class="text-gray-600 ml-2">2 reviews</span>

                </div>

                <div class="text-lg font-bold text-gray-800 mb-4">
                    ₱<span id="productPrice"><?php echo number_format($productDetails['price'], 2); ?></span> PHP
                </div>

                <!-- Shoe Size Selection -->
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-700">Size:</p>
                    <div class="flex gap-2 mt-2 flex-wrap">
                        <?php foreach (['35', '36', '37', '38', '39', '40', '41', '42'] as $size): ?>
                            <button class="size-button"
                                    onclick="selectSize(this, '<?php echo $size; ?>')"><?php echo $size; ?></button>
                        <?php endforeach; ?>
                        <input type="hidden" id="size" name="size" value="">
                    </div>
                    <div class>
                        <p>Size: <span id="modalSizes">Not selected</span></p>
                        <p>Total: ₱<span id="modalTotals">0.00</span></p>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="flex items-center gap-4 mb-6">
                    <button class="quantity-button" onclick="updateQuantity(-1)">-</button>
                    <span id="quantityText" class="text-xl font-semibold">1</span>
                    <input type="hidden" name="quantity" id="quantity" value="1">
                    <button class="quantity-button" onclick="updateQuantity(1)">+</button>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-4">
                <button class="add-to-cart-button" onclick="addToCart(<?php echo $product_id; ?>)">
    Add to Cart
</button>

                    <button onclick="openModal()" class="order-now-button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"></path>
                        </svg>
                        Order Now - Cash on Delivery
                    </button>
                </div>
            <?php else: ?>
                <p class="text-red-500">Product details not found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden" id="orderModal">
    <div class="modal-content bg-white p-8 rounded-lg w-96">
        <h3 class="text-2xl font-semibold mb-4">Your Order</h3>
        <?php if ($productDetails): ?>
            <p>Product: <span><?php echo htmlspecialchars($productDetails['product_name']); ?></span></p>
            <p>Price: ₱<span id="modalPrice"><?php echo number_format($productDetails['price'], 2); ?></span></p>
            <input type="hidden" name="quantity" id="modal_quantity" value="1">
            <input type="hidden" name="size" id="modal_size" value="">
            <p>Quantity: <span id="modalQuantity">1</span></p>
            <p>Size: <span id="modalSize">Not selected</span></p>
            <p>Total: ₱<span id="modalTotal">0.00</span></p>
            <?php if (!empty($order_message)): ?>
                <div
                    class="mb-4 text-sm <?php echo strpos($order_message, 'Error') !== false ? 'text-red-500' : 'text-green-500'; ?>"><?php echo $order_message; ?></div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-red-500">Product details not found.</p>
        <?php endif; ?>
        ...
        <!-- Order Form -->
        <form id="orderForm" method="post" action="?product_id=<?php echo $product_id; ?>">
            <input type="hidden" name="quantity" id="quantity" value="1">
            <input type="hidden" name="size" id="size" value="">
            <div class="mb-4">
                <label class="modal-label">First Name</label>
                <input type="text" placeholder="First Name" name="firstName" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Last Name</label>
                <input type="text" placeholder="Last Name" name="lastName" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Phone</label>
                <input type="text" placeholder="Phone" name="phone" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Email</label>
                <input type="email" placeholder="Email" name="email" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Street</label>
                <input type="text" placeholder="Street" name="street" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Barangay</label>
                <input type="text" placeholder="Barangay" name="barangay" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">City</label>
                <input type="text" placeholder="City" name="city" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Province</label>
                <input type="text" placeholder="Province" name="province" class="modal-input" required>
            </div>
            <div class="mb-4">
                <label class="modal-label">Postal Code</label>
                <input type="text" placeholder="Postal Code" name="postalCode" class="modal-input" required>
            </div>

            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg mb-4 hover:bg-blue-600 transition-colors duration-200">
                Submit Order
            </button>
        </form>
        <button onclick="closeModal()"
                class="w-full bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
            Close
        </button>
    </div>
</div>

<!-- Thank You Modal -->
<div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden" id="thankYouModal">
    <div class="modal-content bg-white p-8 rounded-lg w-96">
        <h3 class="text-2xl font-semibold mb-4">Thank You!</h3>
        <p class="text-gray-700"><?php echo htmlspecialchars($thank_you_message); ?></p>
        <button onclick="closeThankYouModal()"
                class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 mt-4">
            Close
        </button>
    </div>
</div>

<script>
    let selectedSize = '';

    function changeImage(element) {
        document.getElementById('mainImage').src = element.src;
    }

    function updateQuantity(amount) {
        let quantity = parseInt(document.getElementById('quantity').value);
        quantity += amount;
        if (quantity < 1) quantity = 1;
        document.getElementById('quantity').value = quantity;
        document.getElementById('quantityText').innerText = quantity;

        // Update price based on quantity
        updatePrice(quantity);
        updateModal();
    }

    function selectSize(element, size) {
        // Remove 'selected' class from previously selected button
        const selectedButton = document.querySelector('.size-button.selected');
        if (selectedButton) {
            selectedButton.classList.remove('selected');
        }

        // Add 'selected' class to the clicked button
        element.classList.add('selected');
        selectedSize = size;
        document.getElementById('size').value = size;
        updateModal();
    }

    function openModal() {
        if (!selectedSize) {
            alert('Please select a size.');
            return;
        }
        document.getElementById('orderModal').classList.remove('hidden');
        updateModal();
    }

    function closeModal() {
        document.getElementById('orderModal').classList.add('hidden');
    }

    function closeThankYouModal() {
        document.getElementById('thankYouModal').classList.add('hidden');
        // Optionally, redirect to the home page or clear the form
        window.location.href = "/"; // Redirect to the home page
    }

    function updatePrice(quantity) {
        let productPrice = parseFloat(document.getElementById('productPrice').innerText.replace(/,/g, ''));
        let totalPrice = productPrice * quantity;

        document.getElementById('modalTotal').innerText = totalPrice.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateModal() {
        let quantity = parseInt(document.getElementById('quantity').value);
        let price = parseFloat(document.getElementById('productPrice').innerText.replace(/,/g, '')); // Get price from product details

        let total = quantity * price;

        document.getElementById('modalQuantity').innerText = quantity;
        document.getElementById('modalSizes').innerText = selectedSize || "Not selected";
        document.getElementById('modalTotals').innerText = total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('modalSize').innerText = selectedSize || "Not selected";
        document.getElementById('modalTotal').innerText = total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function addToCart(productId) {
        let quantity = parseInt(document.getElementById('quantity').value);
        let size = document.getElementById('size').value;

        if (!size) {
            alert('Please select a size.');
            return;
        }

        fetch('add_to_cart.php', { // Path to your add_to_cart.php file
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `product_id=${productId}&quantity=${quantity}&size=${size}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message); // Show success message
                    updateCartCount(data.cart_count); // Update the cart count in the navbar
                } else {
                    alert(data.message); // Show error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding to cart.');
            });
    }

    function updateCartCount(count) {
        const cartCountSpan = document.getElementById('cart-count');
        cartCountSpan.innerText = count;
        cartCountSpan.classList.remove('hidden'); // Make the cart count visible
    }




function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    const size = document.getElementById('size').value;

    fetch('../add-to-cart/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=${quantity}&size=${size}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            document.getElementById('cart-count').classList.remove('hidden');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}


</script>
</body>
</html>
