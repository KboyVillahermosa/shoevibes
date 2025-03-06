<?php
session_start(); // Start the session at the very top

include '../database.php';

// Initialize messages
$order_message = "";
$thank_you_message = "";
$customization_error = "";
$customization_success = "";

if (isset($_POST['imageData']) && isset($_POST['customizationData'])) {
    $imageData = $_POST['imageData'];
    $customizationData = $_POST['customizationData'];

    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = str_replace(' ', '+', $imageData);
    $decodedImage = base64_decode($imageData);

    $folderPath = "customizations/";
    if (!is_dir($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    $timestamp = time();
    $imageFileName = $folderPath . "custom_shoe_" . $timestamp . ".png";
    $jsonFileName = $folderPath . "custom_shoe_" . $timestamp . ".json";

    $imageSaved = file_put_contents($imageFileName, $decodedImage);
    $jsonSaved = file_put_contents($jsonFileName, $customizationData);

    if ($imageSaved && $jsonSaved) {
        $customization_success = "Customization saved successfully!<br>";
        $customization_success .= "<img src='$imageFileName' alt='Customized Shoe' style='max-width:300px;'/><br>";
        // Construct the correct relative path to the JSON file
        $jsonPath = 'shoes-preview/' . $folderPath . basename($jsonFileName);
        $customization_success .= "<a href='../view_customization.php?json=" . urlencode($jsonPath) . "' target='_blank'>View 3D Customization</a>";

        // Store data in session:
        $_SESSION['customized_image'] = $imageFileName; // image path
        $_SESSION['customized_json'] = $jsonPath; // json path
        $_SESSION['customization_success'] = $customization_success; // all messages

    } else {
        $customization_error = "Error saving the customization.";
    }
}

// Retrieve data from session (if available)
if (isset($_SESSION['customization_success'])) {
    $customization_success = $_SESSION['customization_success'];

} else {
    $customization_success = "";
}

// Get product ID from URL, default to 1
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 1; //Sanitize input, ensure integer

// Handle add to cart functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];


    $sql = "SELECT product_name, price, image_url FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
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

        // Redirect back to the product page after adding to cart
        header('Location: shoes1.php?product_id=' . $product_id . '&cart_count=' . $cart_count);
        exit();
    } else {
        $order_message = "Error: Product not found.";
    }
}

// Handle item deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $index = $_POST['index'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1); // Remove the item
    }

    // Recalculate total items in cart
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

    header('Location: shoes1.php?product_id=' . $product_id . '&cart_count=' . $cart_count); // Redirect back to the product page
    exit;
}

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $productDetails = $result->fetch_assoc();
    } else {
        $productDetails = null;
        $order_message = "Product not found.";
    }
    $stmt->close();
} else {
    $order_message = "Database error: " . $conn->error;
    $productDetails = null;
}

// Check if customization data exists in localStorage
$customization_data_json = ""; // Initialize the variable
if (isset($_GET['customized']) && $_GET['customized'] == 'true') {
    // Retrieve the customization data from localStorage using JavaScript and store it in a PHP variable
    echo '<script>
            window.onload = function() {
                var customizationData = localStorage.getItem("shoeCustomization");
                if (customizationData) {
                    // Store the customization data in a hidden input field
                    document.getElementById("customizationData").value = customizationData;
                    // Display customization data for testing
                    document.getElementById("customizationDisplay").innerHTML = "<p><b>Customization Data (JSON):</b></p><pre>" . customizationData + "</pre>";
                } else {
                    console.log("No customization data found in localStorage.");
                    document.getElementById("customizationDisplay").innerHTML = "<p>No customization data available.</p>";
                }
            };
          </script>';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['imageData']) && !isset($_POST['customizationData']) && !isset($_POST['add_to_cart'])) {
    // Sanitize and validate input
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $size = isset($_POST['size']) ? filter_var($_POST['size'], FILTER_SANITIZE_STRING) : '';
    $firstName = isset($_POST['firstName']) ? filter_var($_POST['firstName'], FILTER_SANITIZE_STRING) : '';
    $lastName = isset($_POST['lastName']) ? filter_var($_POST['lastName'], FILTER_SANITIZE_STRING) : '';
    $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $street = isset($_POST['street']) ? filter_var($_POST['street'], FILTER_SANITIZE_STRING) : '';
    $barangay = isset($_POST['barangay']) ? filter_var($_POST['barangay'], FILTER_SANITIZE_STRING) : '';
    $city = isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_STRING) : '';
    $province = isset($_POST['province']) ? filter_var($_POST['province'], FILTER_SANITIZE_STRING) : '';
    $postalCode = isset($_POST['postalCode']) ? filter_var($_POST['postalCode'], FILTER_SANITIZE_STRING) : '';
    $customization_data = isset($_POST['customization_data']) ? $_POST['customization_data'] : '';

    // Validate required fields
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
                $sql_insert = "INSERT INTO orders (product_id, quantity, size, total_price, first_name, last_name, phone, email, street, barangay, city, province, postal_code, customization_data)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);

                if ($stmt_insert) {
                    $stmt_insert->bind_param("iisdssssssssss", $product_id, $quantity, $size, $total_price, $firstName, $lastName, $phone, $email, $street, $barangay, $city, $province, $postalCode, $customization_data);
                    if ($stmt_insert->execute()) {
                        $order_message = "Order submitted successfully!";
                        $thank_you_message = "Thank you for choosing ShoeVibes!";
                        echo '<script>setTimeout(function(){ document.getElementById("thankYouModal").classList.remove("hidden"); }, 500);</script>';
                    } else {
                        $order_message = "Error submitting order: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                } else {
                    $order_message = "Database error: " . $conn->error;
                }
            } else {
                $order_message = "Error: Product price not found!";
            }
            $stmt_price->close();
        } else {
            $order_message = "Database error: " . $conn->error;
        }
    }
}

if (isset($_POST['saveCustomization'])) {
    $customization_data = $_POST['customization_data'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET customization_data = ? WHERE id = ?");
    $stmt->bind_param("si", $customization_data, $user_id);

    if ($stmt->execute()) {
        $_SESSION['customization_data'] = $customization_data;
        echo json_encode(['status' => 'success', 'message' => 'Customization saved successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save customization']);
    }
    exit;
}





//review code in 
$reviewSubmitted = false;
$submissionError = "";

// Function to generate a unique token
function generate_token()
{
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
        if (
            isset($_POST['name']) && !empty($_POST['name']) &&
            isset($_POST['email']) && !empty($_POST['email']) &&
            isset($_POST['rating']) && !empty($_POST['rating']) &&
            isset($_POST['review'])
        ) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $rating = (int) $_POST['rating']; // Ensure rating is an integer
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
                $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, review_title, review, image) VALUES (?, ?, ?, ?, ?, ?)");
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

$sql = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

// Calculate star rating statistics
$sql_stats = "SELECT rating, COUNT(*) AS count FROM reviews GROUP BY rating";
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./ccs/shoes.css">
    <title>Product Page</title>

    <style>
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

        .size-button.selected {
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
            width: 100%;
            margin-bottom: 10px;
        }

        .add-to-cart-button:hover {
            background: black;
            color: white;
        }

        .thumbnail-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
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

        #orderSidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 550px;
            background: white;
            color: black;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 50;
            overflow-y: auto;
        }

        #orderSidebar.active {
            transform: translateX(0);
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

        .order {
            background: black;
            color: white;
            padding: 10px;
            border-radius: 10px;
        }

        /* Style for displaying the customization data */
        #customizationDisplay {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <section>
        <nav class="bg-white border-gray-200 shadow-sm">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="../index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
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
                            <a href="shoes1.php?show_cart=true&product_id=<?php echo $product_id; ?>"
                                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">
                                Cart
                                <span id="cart-count"
                                    class="bg-red-500 text-white rounded-full px-2 py-0.5 text-xs ml-1 <?php echo (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) ? '' : 'hidden'; ?>">
                                    <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    <!-- Shopping Cart Section -->
    <?php if (isset($_GET['show_cart']) && $_GET['show_cart'] == 'true'): ?>
        <div class="container mx-auto mt-8">
            <h1 class="text-2xl font-bold mb-4">Your Shopping Cart</h1>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <div class="mb-4 pb-4 border-b flex justify-between items-center">
                            <div>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product Image"
                                    class="h-16 w-16 object-cover mr-4">
                                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h2>
                                <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                <p>Price: ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                            <form method="post" action="shoes1.php?product_id=<?php echo $product_id; ?>&show_cart=true">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <button type="submit" name="remove_item"
                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Remove
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>

                    <div class="mt-4">
                        <h3 class="text-xl font-bold">Total: ₱<?php
                        $total = array_sum(array_map(function ($item) {
                            return $item['price'] * $item['quantity'];
                        }, $_SESSION['cart']));
                        echo number_format($total, 2);
                        ?></h3>
                    </div>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
            <a href="shoes1.php?product_id=<?php echo $product_id; ?>"
                class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Continue Shopping</a>
        </div>
    <?php else: ?>


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

                    <?php
                    if (!empty($customization_success)) {
                        echo $customization_success;
                    }
                    if (!empty($customization_error)) {
                        echo "<p class='text-red-500'>" . htmlspecialchars($customization_error) . "</p>";
                    }
                    ?>
                </div>

                <!-- Product details -->
                <div class="product-header">
                    <div class="product-header-content">
                        <div class="flex-1 bg-white p-6 rounded-lg shadow-lg">
                            <?php if ($productDetails): ?>
                                <a href="../boss.php">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-2 underline cursor-pointer">Customize
                                        shoes</h2>
                                </a>
                                <h2 class="text-xl font-semibold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($productDetails['product_name']); ?>
                                </h2>
                                <div class="flex items-center mb-4">
                                    <span class="text-yellow-500 text-lg">★★★★★</span>
                                    <span class="text-gray-600 ml-2">2 reviews</span>
                                </div>

                                <div class="text-lg font-bold text-gray-800 mb-4">
                                    ₱<span id="productPrice"><?php echo number_format($productDetails['price'], 2); ?></span>
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

                                <form method="post" action="shoes1.php?product_id=<?php echo $product_id; ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <input type="hidden" name="quantity" id="cartQuantity" value="1">
                                    <input type="hidden" name="size" id="cartSize" value="">
                                    <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                                </form>


                                <!-- Buttons -->
                                <div class="flex flex-col gap-4">
                                    <!-- <button class="add-to-cart-button"
                                    onclick="addToCart(<?php echo $product_id; ?>)">Add to Cart</button> -->
                                    <button onclick="openSidebar()" class="order-now-button">Order Now - Cash on
                                        Delivery</button>
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
                <p>Product: <span>
                        <?php echo htmlspecialchars($productDetails['product_name']); ?>
                    </span></p>
                <p>Price: ₱<span id="modalPrice">
                        <?php echo number_format($productDetails['price'], 2); ?>
                    </span></p>
                <input type="hidden" name="quantity" id="modal_quantity" value="1">
                <input type="hidden" name="size" id="modal_size" value="">
                <p>Quantity: <span id="modalQuantity">1</span></p>
                <p>Size: <span id="modalSize">Not selected</span></p>
                <p class="mb-8">Total: ₱<span id="modalTotal">0.00</span></p>

            <?php else: ?>
                <p class="text-red-500">Product details not found.</p>
            <?php endif; ?>

            <!-- Order Form -->
            <form id="orderForm" method="post" action="sidebar.php?product_id=<?php echo $product_id; ?>">

                <input type="hidden" name="quantity" id="quantity" value="1">
                <input type="hidden" name="size" id="size" value="">
                <!-- Hidden input for customization data -->
                <input type="hidden" name="customization_data" id="customizationData" value="">
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
                    class="order w-full bg-blue-500 text-white py-2 rounded-lg mb-4 hover:bg-blue-600 transition-colors duration-200">
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
                <p class="text-gray-700">
                    <?php echo htmlspecialchars($thank_you_message); ?>
                </p>
                <button onclick="closeThankYouModal()"
                    class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition-colors duration-200 mt-4">
                    Close
                </button>
            </div>
        </div>
    <?php endif; ?>



    <section>
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold mb-6 text-gray-800">Customer Reviews</h2>

            <?php if (!empty($submissionError)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?= htmlspecialchars($submissionError) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($reviewSubmitted): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Thank you!</strong>
                    <span class="block sm:inline">Your review has been submitted.</span>
                </div>
            <?php endif; ?>

            <!-- Display Average Rating -->
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Average Rating: <?= $average_rating ?> / 5
                    (<?= $total_reviews ?> reviews)</h3>
            </div>

            <form action="" method="POST" enctype="multipart/form-data"
                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <!-- Add the hidden token field -->
                <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($_SESSION['form_token']); ?>">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="name" type="text" name="name" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="email" type="email" name="email" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rating:</label>
                    <div class="flex items-center">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="hidden" required>
                            <label for="star<?= $i ?>"
                                class="text-3xl cursor-pointer text-gray-400 hover:text-black focus:text-black">
                                <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 1c1.939 0 3.683 1.476 4.489 3.955l6.572.955-4.756 4.645 1.123 6.545z" />
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="review_title">Review Title:</label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="review_title" type="text" name="review_title">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="review">Review:</label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="review" name="review" rows="4" required></textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Upload Image
                        (optional):</label>
                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48" aria-hidden="true">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4V12a4 4 0 014-4h16m-16 0h20v4m0 0v4m0 0v4"
                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
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
                    <button
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit">
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
                                    <svg class="w-4 h-4 fill-current text-yellow-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 1c1.939 0 3.683 1.476 4.489 3.955l6.572.955-4.756 4.645 1.123 6.545z" />
                                    </svg>
                                <?php endfor; ?>
                                <span class="text-gray-600 ml-2">(<?= $row['rating'] ?>/5)</span>
                            </div>
                            <h4 class="font-bold text-lg mb-2 text-gray-800"><?= htmlspecialchars($row['review_title']) ?>
                            </h4>
                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($row['review'])) ?></p>
                        </div>
                        <?php if ($row['image']): ?>
                            <img src="<?= htmlspecialchars($row['image']) ?>" alt="Review Image"
                                class="w-full h-48 object-cover">
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    </section>
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
            let price = parseFloat(document.getElementById('productPrice').innerText.replace(/,/g, ''));

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

            // Instead of using fetch to call a separate PHP file, directly submit the form
            // with the action set to shoes1.php and the name "add_to_cart" on the submit button.
            // This is necessary because we combined add_to_cart.php into this single file.

            // Update the hidden fields in the form to reflect current quantity and size
            document.querySelector('input[name="quantity"]').value = quantity;
            document.querySelector('input[name="size"]').value = size;

            // Since the form is submitted directly, you don't need to handle the response in JavaScript
            // The PHP code will handle adding the item to the cart and redirecting back to the page
            // Add any additional client-side validation before form submission here
        }

        function updateCartCount(count) {
            const cartCountSpan = document.getElementById('cart-count');
            cartCountSpan.innerText = count;
            cartCountSpan.classList.remove('hidden'); // Make the cart count visible
        }

        // Retrieve customization data from localStorage on page load
        window.onload = function () {
            var customizationData = localStorage.getItem("shoeCustomization");
            if (customizationData) {
                document.getElementById("customizationData").value = customizationData;
                // Display for testing
                document.getElementById("customizationDisplay").innerHTML = "<p><b>Customization Data:</b></p><pre>" + customizationData + "</pre>";

            } else {
                console.log("No customization data found in localStorage.");
            }
        };

        function saveCustomizationData() {
            var customizationData = document.getElementById("customizationData").value;

            fetch('shoes1.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'saveCustomization=1&customization_data=' + encodeURIComponent(customizationData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        localStorage.setItem("shoeCustomization", customizationData);
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }

        // Load customization data on page load
        window.onload = function () {
            var customizationData = <?php echo json_encode($_SESSION['customization_data'] ?? '{}'); ?>;
            document.getElementById("customizationData").value = customizationData;
            localStorage.setItem("shoeCustomization", customizationData);
        };



        //review code js
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