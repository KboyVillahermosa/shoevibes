<?php
session_start();
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

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
</head>
<body class="bg-white flex items-center justify-center min-h-screen">

    <div class="bg-[#F8F8FF] p-8 rounded-lg shadow-lg w-96 text-center border border-gray-200">
        <h2 class="text-xl font-bold text-black mb-6">Register</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <button onclick="window.location.href='login.php'"
                class="w-full bg-black text-[#F8F8FF] py-2 rounded-lg hover:bg-gray-900 transition duration-200">
                Go to Login
            </button>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <input type="text" name="first_name" placeholder="First Name" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black bg-white text-black">

            <input type="text" name="last_name" placeholder="Last Name" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black bg-white text-black">

            <input type="email" name="email" placeholder="Email" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black bg-white text-black">

            <input type="password" name="password" placeholder="Password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black bg-white text-black">

            <input type="password" name="confirm_password" placeholder="Confirm Password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black bg-white text-black">

            <button type="submit"
                class="w-full bg-black text-[#F8F8FF] py-2 rounded-lg hover:bg-gray-900 transition duration-200">
                Register
            </button>
        </form>

        <p class="mt-4 text-sm text-gray-600">
            Already have an account? 
            <a href="login.php" class="text-black font-semibold hover:underline">Login</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.js"></script>
</body>
</html>
