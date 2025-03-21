<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email);
$stmt->fetch();
$stmt->close();

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $hashed_password)) {
        $_SESSION['error'] = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match!";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed_password, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Password changed successfully!";
        } else {
            $_SESSION['error'] = "Failed to update password!";
        }
        $stmt->close();
    }
}

// Handle Account Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        session_destroy();
        header("Location: register.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete account!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Inter', sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 20px;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1F2937;
        }

        p {
            font-size: 16px;
            color: #4B5563;
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .input-field:focus {
            border-color: #6366F1;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        .btn-primary {
            background: #1F2937;
            color: white;
        }

        .btn-primary:hover {
            background: #111827;
        }

        .btn-danger {
            background: #DC2626;
            color: white;
        }

        .btn-danger:hover {
            background: #B91C1C;
        }

        .success-msg, .error-msg {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .success-msg {
            background: #D1FAE5;
            color: #065F46;
        }

        .error-msg {
            background: #FEE2E2;
            color: #B91C1C;
        }

        .profile-info {
            font-size: 18px;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .profile-info span {
            font-weight: 600;
            color: #000;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
                margin: 50px auto;
            }
        }
        .profile-header{
            justify-content: start;
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            gap: 20px;
            margin-bottom: 50px;
        }
        .profile-content{
            width: 100%;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
        .heade-one{
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<section>
    <nav class="bg-white border-gray-200">
      <div class="logos max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
          <img src="./image/logo4.png" class="h-24" alt="Flowbite Logo" />
        </a>
        <button data-collapse-toggle="navbar-default" type="button"
          class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-black rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
          aria-controls="navbar-default" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1h15M1 7h15M1 13h15" />
          </svg>
        </button>
        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
          <ul
            class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
            <li><a href="index.php"
                class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Home</a>
            </li>
            <li>
              <a href="#"
                class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">About
                Us</a></li>
            <li>
              <a href="#"
                class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Contact</a>
            </li>
            <li>
              <a href="profile.php"
                class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Profile</a>
            </li>
            <li>
              <a href="logout.php"
                class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Logout</a>
            </li>
          </ul>
        </div>

      </div>
    </nav>
  </section>

<div class="container">
 
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-msg">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-msg">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
     <div class="profile-header">
        <div class="profile-content">
        <h2 class="heade-one">Profile Information</h2>
        <p class="profile-info"><span>First Name:</span> <?php echo $first_name; ?></p>
        <p class="profile-info"><span>Last Name:</span> <?php echo $last_name; ?></p>
        <p class="profile-info"><span>Email:</span> <?php echo $email; ?></p>
        </div>
     </div>
   
  
     <div class="profile-header">
     <div class="profile-content">
    <h2 class="mb-4">Change Password</h2>
    <form method="POST">
        <input type="password" name="current_password" placeholder="Current Password" required class="input-field">
        <input type="password" name="new_password" placeholder="New Password" required class="input-field">
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required class="input-field">
        <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
    </form>
    </div>
    </div>

    <div class="profile-header">
    <div class="profile-content">
    <h2 class="text-red-600 mt-6">Delete Account</h2>
    <form method="POST">
        <button type="submit" name="delete_account" class="btn btn-danger" onclick="return confirm('Are you sure? This action cannot be undone.')">
            Delete Account
        </button>
    </form>
    </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.js"></script>
</body>
</html>
