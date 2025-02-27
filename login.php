<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, customization_data FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password, $customization_data);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['customization_data'] = $customization_data;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FFFFFF; /* White background */
            overflow: hidden; /* Prevent scrollbars caused by circles */
        }

        .login-container {
            width: 360px; /* Adjust width as needed */
            padding: 2rem;
            border-radius: 0.5rem;
            position: relative; /* For absolute positioning of circles */
            z-index: 1; /* Ensure login form is above circles */
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
            width: 300px; /* Adjust size as needed */
            height: 300px;
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
            width: 150px; /* Adjust size as needed */
            height: 150px;
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
<body class="flex items-center justify-center min-h-screen">

   

    <div class="login-container">
        <div class="text-center mb-6">
            <img src="./image/logo4.png" alt="Shoe Vibes Logo" class="image-logo">
            <h2 class="text-xl font-semibold text-gray-900 mt-3">Login your Account</h2>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <input type="email" name="email" placeholder="Email" required
                    class="input-field w-full">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required
                    class="input-field w-full">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-gray-900 text-white py-2 rounded-md hover:bg-gray-700 transition duration-200">
                    Login
                </button>
            </div>
        </form>

        <p class="mt-4 text-sm text-gray-600 text-center">
            You don't have an Account?
            <a href="register.php" class="text-gray-900 font-semibold hover:underline">Create</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.js"></script>
</body>
</html>
