<?php
include '../database.php'; // Include the database connection

$orderPlaced = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $size = isset($_POST['size']) ? $conn->real_escape_string($_POST['size']) : '';
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $street = $conn->real_escape_string($_POST['street']);
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $city = $conn->real_escape_string($_POST['city']);
    $province = $conn->real_escape_string($_POST['province']);
    $postalCode = $conn->real_escape_string($_POST['postalCode']);

    // Handle design image
    $customization_image = '';
    if (isset($_POST['screenshot'])) {
        $image_data = $_POST['screenshot'];
        $image_data = str_replace('data:image/png;base64,', '', $image_data);
        $image_data = str_replace(' ', '+', $image_data);
        $decoded_image = base64_decode($image_data);

        // Generate unique filename
        $filename = 'custom_shoe_' . time() . '.png';
        $filepath = "customizations/" . $filename;

        if (file_put_contents($filepath, $decoded_image)) {
            $customization_image = $filepath;
        }
    }

    $total_price = calculateTotalPrice($conn, $product_id, $quantity, $size);

    $sql = "INSERT INTO orders (
        product_id, quantity, size, total_price,
        first_name, last_name, phone, email,
        street, barangay, city, province,
        postal_code, customization_data, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param(
            "iisdssssssssss",
            $product_id, $quantity, $size, $total_price,
            $firstName, $lastName, $phone, $email,
            $street, $barangay, $city, $province,
            $postalCode, $customization_image
        );

        if ($stmt->execute()) {
            $orderPlaced = true;
        }
        $stmt->close();
    }
    $conn->close();
}

function calculateTotalPrice($conn, $product_id, $quantity, $size) {
    $sql = "SELECT price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $base_price = $row['price'] ?? 0;
    return $base_price * $quantity;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoevibes Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
        }
        p {
            color: #555;
            font-size: 16px;
        }
        .btn-back{
            background: black;
            color: white;
            padding: 10px;
            border-radius: 10px;
            font-size: 20px;
              }
    </style>
</head>
<body>
    <div class="container">
        <img src="../image/logo4.png" alt="Shoevibes Logo" class="logo">
        <?php if ($orderPlaced): ?>
            <h2>Thank you for choosing Shoevibes!</h2>
            <p>Your order has been successfully placed. We appreciate your business!</p>
            <p>You will receive a confirmation email shortly with your order details.</p>
        <?php else: ?>
            <h2>Error</h2>
            <p>There was an error processing your order. Please try again.</p>
        <?php endif; ?>
        <a href="../index.php"><button class="btn-back">Back</button></a>
    </div>
</body>
</html>
