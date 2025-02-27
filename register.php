<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $customization_data = '{}'; // Initialize with empty JSON object

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, customization_data) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $customization_data);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful!";
    } else {
        $_SESSION['error'] = "Registration failed!";
    }

    $stmt->close();
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFFFFF; /* White background */
           height: 90vh; /* Ensure the body takes up at least the full viewport height */
            overflow-x: hidden; /* Prevent horizontal scrolling */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center horizontally */
            justify-content: center; /* Center vertically */
        }

        .register-container {
            width: 360px; /* Adjust width as needed */
            padding: 2rem;
            border-radius: 0.5rem;
            position: relative; /* For absolute positioning of circles */
            z-index: 1; /* Ensure register form is above circles */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */

        }

        .input-field {
            border: 1px solid #D1D5DB; /* Thin grey border */
            border-radius: 0.375rem; /* Rounded corners */
            padding: 0.75rem;
            color: #4B5563; /* Dark grey text */
        }

        .input-field:focus {
            outline: none;
            border-color: #9CA3AF; /* Slightly darker grey on focus */
            box-shadow: none;
        }

        .circle {
            width: 200px; /* Adjust size as needed */
            height: 200px;
            background-color: black;
            border-radius: 50%;
            position: absolute;
        }

        .circle-left {
            top: -50px; /* Adjust position as needed */
            left: -100px; /* Adjust position as needed */
        }

        .circle-right {
            top: -50px; /* Adjust position as needed */
            right: -100px; /* Adjust position as needed */
        }

        .circle-inner {
            width: 80px; /* Adjust size as needed */
            height: 80px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .image-logo{
            width: 100%;
            max-width: 240px;
        }
    </style>
</head>
<body>
  

    <div class="register-container">
        <div class="text-center mb-6">
            <img src="./image/logo4.png" alt="Shoe Vibes Logo" class="image-logo">
            <h2 class="text-xl font-semibold text-gray-900 mt-3">Create Account</h2>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <button onclick="window.location.href='login.php'"
                class="w-full bg-gray-900 text-white py-2 rounded-md hover:bg-gray-700 transition duration-200">
                Go to Login
            </button>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <input type="text" name="first_name" placeholder="First Name" required
                    class="input-field w-full">
            </div>
             <div>
                <input type="text" name="last_name" placeholder="Last Name" required
                    class="input-field w-full">
            </div>
            <div>
                <input type="email" name="email" placeholder="Email" required
                    class="input-field w-full">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required
                    class="input-field w-full">
            </div>
            <div>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required
                    class="input-field w-full">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-gray-900 text-white py-2 rounded-md hover:bg-gray-700 transition duration-200">
                    Register
                </button>
            </div>
        </form>

        <p class="mt-4 text-sm text-gray-600 text-center">
            Already have an account?
            <a href="login.php" class="text-gray-900 font-semibold hover:underline">Login</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.js"></script>
</body>
</html>
