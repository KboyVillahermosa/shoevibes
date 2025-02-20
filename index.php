
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/indexs.css">
    <title>Document</title>
</head>
<body>
<section>
  <nav class="bg-white border-gray-200">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
        <img src="./image/logo4.png" class="h-16" alt="Flowbite Logo" />
      </a>
      <button data-collapse-toggle="navbar-default" type="button" 
        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-black rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
        aria-controls="navbar-default" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
      </button>
      <div class="hidden w-full md:block md:w-auto" id="navbar-default">
        <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
          <li>
            <a href="#" class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Home</a>
          </li>
          <li>
            <a href="#" class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">About</a>
          </li>
          <li>
            <a href="#" class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Services</a>
          </li>
          <li>
            <a href="#" class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Pricing</a>
          </li>
          <li>
            <a href="#" class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Contact</a>
          </li>
          
          <li>
            <a href="logout.php" class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</section>


    <section class="">
        <div class="header">
            <div class="header-content">
            <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-800 md:text-5xl lg:text-6xl">We invest in the world’s potential</h1>
            <p class="my-4 text-lg text-gray-500">Start developing with an open-source library of over 450+ UI components, sections, and pages built with the utility classes from Tailwind CSS and designed in Figma.</p>
            </div>
            <div class="header-image">
                <img src="./image/shoe.jpg" alt="">
            </div>
        </div>
    </section>
   <section>
       <div class="products-title">
       <h1 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-800 md:text-5xl lg:text-4xl">Products</h1>
       </div>

       <div class="products-header">
        <div class="products-content">
           <a href="./shoes-preview/shoes1.php"><img src="./image/shoe.jpg" alt=""></a>
        </div>
        <div class="products-content">
           <img src="./image/shoe.jpg" alt="">
        </div>
        <div class="products-content">
           <img src="./image/shoe.jpg" alt="">
        </div>
        <div class="products-content">
           <img src="./image/shoe.jpg" alt="">
        </div>
        <div class="products-content">
           <img src="./image/shoe.jpg" alt="">
        </div>
        <div class="products-content">
           <img src="./image/shoe.jpg" alt="">
        </div>
        
       </div>
   </section>


   <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.js"></script>
   <script src="./js/index.js"></script>
</body>
</html>