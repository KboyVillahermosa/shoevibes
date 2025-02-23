<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Saved Customization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        img {
            max-width: 90%;
            height: auto;
            border: 1px solid #ccc;
            padding: 10px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #4CAF50;
            font-size: 18px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .btn-container {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>Your Saved Customization</h2>
    <?php
    if (isset($_SESSION['save_error'])) {
        echo '<p class="error">' . $_SESSION['save_error'] . '</p>';
        unset($_SESSION['save_error']); // Clear the error message
    } elseif (isset($_SESSION['saved_image'])) {
        $image_path = $_SESSION['saved_image'];
        echo '<img src="' . $image_path . '" alt="Saved Customization">';
        unset($_SESSION['saved_image']); // Clear the session variable
    } else {
        echo '<p>No image data received.</p>';
    }
    ?>
    <div class="btn-container">
        <a href="boss.php" class="btn">Back to Customizer</a>
        <a href="boss.php" class="btn">Edit Again</a>
    </div>
</body>
</html>
