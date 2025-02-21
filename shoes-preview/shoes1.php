<?php
include '../database.php'; // Database connection

// Initialize messages
$order_message = "";
$thank_you_message = "";

// Get product ID from URL, default to 1
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 1; //Sanitize input, ensure integer

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) { // Check if prepare was successful
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $productDetails = $result->fetch_assoc();
    } else {
        $productDetails = null;
        $order_message = "Product not found."; // set error message
    }

    $stmt->close(); // Close statement
} else {
    $order_message = "Database error: " . $conn->error;
    $productDetails = null;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;  // Default to 1, ensure integer
    $size = isset($_POST['size']) ? filter_var($_POST['size'], FILTER_SANITIZE_STRING) : '';
    $firstName = isset($_POST['firstName']) ? filter_var($_POST['firstName'], FILTER_SANITIZE_STRING) : '';
    $lastName = isset($_POST['lastName']) ? filter_var($_POST['lastName'], FILTER_SANITIZE_STRING) : '';
    $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : ''; //Consider using a REGEX for validation.
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $street = isset($_POST['street']) ? filter_var($_POST['street'], FILTER_SANITIZE_STRING) : '';
    $barangay = isset($_POST['barangay']) ? filter_var($_POST['barangay'], FILTER_SANITIZE_STRING) : '';
    $city = isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_STRING) : '';
    $province = isset($_POST['province']) ? filter_var($_POST['province'], FILTER_SANITIZE_STRING) : '';
    $postalCode = isset($_POST['postalCode']) ? filter_var($_POST['postalCode'], FILTER_SANITIZE_STRING) : '';

    // Validate required fields (example)
    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email) || empty($street) || empty($barangay) || empty($city) || empty($province) || empty($postalCode)) {
        $order_message = "Error: Please fill in all required fields.";
    } elseif (empty($size)) {
        $order_message = "Error: Please select a size.";
    } else {
        // Fetch product price from the database
        $sql_price = "SELECT price FROM products WHERE product_id = ?";
        $stmt_price = $conn->prepare($sql_price);

        if ($stmt_price) {
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

                if ($stmt_insert) {
                    $stmt_insert->bind_param("iisdsssssssss", $product_id, $quantity, $size, $total_price, $firstName, $lastName, $phone, $email, $street, $barangay, $city, $province, $postalCode);

                    if ($stmt_insert->execute()) {
                        $order_message = "Order submitted successfully!";
                        $thank_you_message = "Thank you for choosing ShoeVibes!";
                        echo '<script>setTimeout(function(){ document.getElementById("thankYouModal").classList.remove("hidden"); }, 500);</script>';

                    } else {
                        $order_message = "Error submitting order: " . $stmt_insert->error;
                    }
                    $stmt_insert->close(); //close insert statement
                } else {
                    $order_message = "Database error: " . $conn->error;
                }
            } else {
                $order_message = "Error: Product price not found!";
            }
            $stmt_price->close(); //close price statement

        } else {
            $order_message = "Database error: " . $conn->error;
        }
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
    <link rel="stylesheet" href="./ccs/shoes.css"> <!-- Consider moving inline styles to this file -->
    <title>Product Page</title>

    <style>
        /* Moved inline styles here, consider moving to shoes.css for better organization */
        .size-button {
            background-color: white;
            color: black;
            border: 1px solid black;
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .size-button:hover {
            background-color: black;
            color: white;
            border-color: black;
        }

        .size-button.selected {  /* Style for selected size */
            background-color: black;
            color: white;
        }

        .quantity-button {
            background-color: white;
            color: black;
            border: 1px solid black;
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .quantity-button:hover {
            background-color: black;
            color: white;
            border-color: black;
        }

        .order-now-button {
            background: black;
            color: white;
            padding: 10px;
            border-radius: 10px;
        }

        .add-to-cart-button {
            border: 1px solid black;
            padding: 10px;
            border-radius: 10px;
        }

        .add-to-cart-button:hover {
            background: black;
            color: white;
        }

        .thumbnail-container {
            display: flex;
            /* Enable flex container */
            flex-wrap: wrap;
            /* Allow wrapping to next row */
            justify-content: center;
            /* Center items horizontally */
        }

        .shoes1 {
            width: 100%;
            max-width: 500px;
        }

        .thumbnail img {
            border: 1px solid gray;
        }

        .modal-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .modal-input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
        }

                /* Sidebar Styles */
        #orderSidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 550px; /* Adjust width as needed */
            background: white;
            color: black;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transform: translateX(-100%); /* Initially hidden */
            transition: transform 0.3s ease-in-out;
            z-index: 50;
            overflow-y: auto; /* Enable scrolling */
        }

        #orderSidebar.active {
            transform: translateX(0); /* Slide in when active */
        }

        #sidebarOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            display: none;
        }

        #sidebarOverlay.active {
            display: block;
        }
        .order{
            background: black;
            color: white;
            padding: 10px;
            border-radius: 10px;

        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <section>
        <nav class="bg-white border-gray-200 shadow-sm">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
                    <img src="../image/logo4.png" class="h-16" alt="ShoeVibes Logo" />
                </a>
                <button data-collapse-toggle="navbar-default" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-black rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    aria-controls="navbar-default" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
                <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                    <ul
                        class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
                        <li>
                            <a href="#"
                                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Home</a>
                        </li>
                        <li>
                            <a href="../"
                                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Shop</a>
                        </li>
                        <li>
                            <a href="../add-to-cart/add_to_cart.php"
                                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">
                                Cart
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
            <div class="thubnail flex flex-row md:flex-col gap-2">
                <img src="../image/s1.png" alt="Shoe 1"
                    class="shoes1 w-18 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
                <img src="../image/s2.png" alt="Shoe 2"
                    class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
                <img src="../image/s3.png" alt="Shoe 3"
                    class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
                <img src="../image/s4.png" alt="Shoe 4"
                    class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
                <img src="../image/s5.png" alt="Shoe 5"
                    class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
                <img src="../image/s6.png" alt="Shoe 6"
                    class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
                <img src="../image/s7.png" alt="Shoe 7"
                    class="w-16 h-16 cursor-pointer rounded-lg border-2 border-transparent hover:border-gray-600"
                    onclick="changeImage(this)">
            </div>

            <!-- Main product image -->
            <div class="flex-1">
                <?php if ($productDetails): ?>
                    <img id="mainImage" src="<?php echo htmlspecialchars($productDetails['image_url']); ?>"
                        alt="<?php echo htmlspecialchars($productDetails['product_name']); ?>"
                        class="w-full h-auto rounded-lg shadow-lg">
                <?php else: ?>
                    <p class="text-red-500">Product not found.</p>
                <?php endif; ?>
            </div>

            <!-- Product details -->
            <div class="product-header">
                <div class="product-header-content">
                    <div class="flex-1 bg-white p-6 rounded-lg shadow-lg">
                        <?php if ($productDetails): ?>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">
                                <?php echo htmlspecialchars($productDetails['product_name']); ?>
                            </h2>
                            <div class="flex items-center mb-4">
                                <span class="text-yellow-500 text-lg">★★★★★</span>
                                <span class="text-gray-600 ml-2">2 reviews</span>
                            </div>

                            <div class="text-lg font-bold text-gray-800 mb-4">
                                ₱<span
                                    id="productPrice"><?php echo number_format($productDetails['price'], 2); ?></span>
                                PHP
                            </div>

                            <!-- Shoe Size Selection -->
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700">Size:</p>
                                <div class="flex gap-2 mt-2 flex-wrap mb-5">
                                    <?php foreach (['35', '36', '37', '38', '39', '40', '41', '42'] as $sizeOption): ?>
                                        <button class="size-button"
                                            onclick="selectSize(this, '<?php echo $sizeOption; ?>')"><?php echo $sizeOption; ?></button>
                                    <?php endforeach; ?>
                                    <input type="hidden" id="size" name="size" value="">
                                </div>
                                <div>
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
                                <button class="add-to-cart-button"
                                    onclick="addToCart(<?php echo $product_id; ?>)">Add to Cart</button>
                                <button onclick="openSidebar()" class="order-now-button">Order Now - Cash on Delivery</button>
                            </div>
                        <?php else: ?>
                            <p class="text-red-500">Product details not found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sidebar -->
    <div id="orderSidebar">
        <h3 class="text-2xl font-semibold mb-4">Your Order</h3>
        <button onclick="closeSidebar()" class="absolute top-2 right-2 text-black hover:text-gray-300">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <?php if ($productDetails): ?>
            <p>Product: <span><?php echo htmlspecialchars($productDetails['product_name']); ?></span></p>
            <p>Price: ₱<span id="modalPrice"><?php echo number_format($productDetails['price'], 2); ?></span></p>
            <input type="hidden" name="quantity" id="modal_quantity" value="1">
            <input type="hidden" name="size" id="modal_size" value="">
            <p>Quantity: <span id="modalQuantity">1</span></p>
            <p>Size: <span id="modalSize">Not selected</span></p>
            <p>Total: ₱<span id="modalTotal">0.00</span></p>
            <?php if (!empty($order_message)): ?>
                <div class="mb-4 text-sm <?php echo strpos($order_message, 'Error') !== false ? 'text-red-500' : 'text-green-500'; ?>">
                    <?php echo $order_message; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-red-500">Product details not found.</p>
        <?php endif; ?>

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
            <button type="submit" class="order w-full bg-blue-500 text-white py-2 rounded-lg mb-4 hover:bg-blue-600 transition-colors duration-200">
                Submit Order
            </button>
        </form>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay"></div>

    <!-- Thank You Modal -->
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden" id="thankYouModal">
        <div class="modal-content bg-white p-8 rounded-lg w-96">
            <h3 class="text-2xl font-semibold mb-4">Thank You!</h3>
            <p class="text-gray-700"><?php echo htmlspecialchars($thank_you_message); ?></p>
            <button onclick="closeThankYouModal()" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 mt-4">
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

            updatePrice(quantity);
            updateModal();
        }

        function selectSize(element, size) {
            const selectedButton = document.querySelector('.size-button.selected');
            if (selectedButton) {
                selectedButton.classList.remove('selected');
            }

            element.classList.add('selected');
            selectedSize = size;
            document.getElementById('size').value = size;
            updateModal();
        }

       function openSidebar() {
            if (!selectedSize) {
                alert('Please select a size.');
                return;
            }
            document.getElementById('orderSidebar').classList.add('active');
            document.getElementById('sidebarOverlay').classList.add('active');
            updateModal();
        }

        function closeSidebar() {
            document.getElementById('orderSidebar').classList.remove('active');
            document.getElementById('sidebarOverlay').classList.remove('active');
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
