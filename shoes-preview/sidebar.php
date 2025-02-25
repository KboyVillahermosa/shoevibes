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

    $total_price = calculateTotalPrice($conn, $product_id, $quantity, $size);

    $sql = "INSERT INTO orders (product_id, quantity, size, total_price, first_name, last_name, phone, email, street, barangay, city, province, postal_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iisssssssssss", $product_id, $quantity, $size, $total_price, $firstName, $lastName, $phone, $email, $street, $barangay, $city, $province, $postalCode);

        
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
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($orderPlaced): ?>
            <h2>Thank you for choosing Shoevibes</h2>
            <p>Your order has been successfully placed. We appreciate your business!</p>
            <p>You will receive a confirmation email shortly with your order details.</p>
        <?php else: ?>
            <h2>Error</h2>
            <p>There was an error processing your order. Please try again.</p>
        <?php endif; ?>
    </div>
</body>
</html>
