<?php
session_start();
include 'database.php';

$message = '';
$alertClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $messageText = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    if (empty($name) || empty($email) || empty($subject) || empty($messageText)) {
        $message = "Please fill in all fields.";
        $alertClass = "text-red-800 bg-red-50";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $messageText);

        if ($stmt->execute()) {
            $message = "Message sent successfully!";
            $alertClass = "text-green-800 bg-green-50";
        } else {
            $message = "Error sending message.";
            $alertClass = "text-red-800 bg-red-50";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ShoeVibes</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
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
    <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
        <li>
            <a href="index.php" class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-black md:p-0 transition-colors duration-200">Home</a>
        </li>
        <li>
            <a href="about_us.php" class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-black md:p-0 transition-colors duration-200">About Us</a>
        </li>
        <li>
            <a href="#" class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-black md:p-0 transition-colors duration-200">Contact</a>
        </li>
        <li>
            <a href="profile.php" class="block py-2 px-3 text-black hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-black md:p-0 transition-colors duration-200">Profile</a>
        </li>
    </ul>
</div>

      </div>
    </nav>
  </section>

<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">Contact Us</h1>
        
        <?php if ($message): ?>
        <div class="p-4 mb-6 rounded-lg <?php echo $alertClass; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-8">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="mb-6">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Your Name</label>
            <input type="text" id="name" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-black focus:border-black block w-full p-2.5" required>
        </div>
        
        <div class="mb-6">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Your Email</label>
            <input type="email" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-black focus:border-black block w-full p-2.5" required>
        </div>
        
        <div class="mb-6">
            <label for="subject" class="block mb-2 text-sm font-medium text-gray-900">Subject</label>
            <input type="text" id="subject" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-black focus:border-black block w-full p-2.5" required>
        </div>
        
        <div class="mb-6">
            <label for="message" class="block mb-2 text-sm font-medium text-gray-900">Your Message</label>
            <textarea id="message" name="message" rows="6" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-black focus:border-black block w-full p-2.5" required></textarea>
        </div>
        
        <button type="submit" class="text-white bg-black hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center transition-colors duration-200">Send Message</button>
    </form>
</div>


        <!-- Contact Information -->
        <div class="mt-12 bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Other Ways to Reach Us</h2>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-semibold text-lg mb-2">Address</h3>
                    <p class="text-gray-600">123 Shoe Street<br>Fashion District<br>Cebu City, Philippines</p>
                </div>
                
                <div>
                    <h3 class="font-semibold text-lg mb-2">Contact Info</h3>
                    <p class="text-gray-600">Email: info@shoevibes.com<br>Phone: +63 123 456 7890</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js"></script>
</body>
</html>