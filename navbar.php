<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet">
    <title>Document</title>
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
        <!-- Replace the existing navbar section with this updated version -->
        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
    <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
        <li>
            <a href="index.php" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">Home
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-black transform origin-left scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                </span>
            </a>
        </li>
        <li>
            <button onclick="openSearchSidebar()" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">Search
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-black transform origin-left scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                </span>
            </button>
        </li>
        <li>
            <a href="#" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">About Us
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-black transform origin-left scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                </span>
            </a>
        </li>
        <li>
            <a href="contact.php" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">Contact
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-black transform origin-left scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                </span>
            </a>
        </li>
        <li>
            <a href="profile.php" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">Profile
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-black transform origin-left scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                </span>
            </a>
        </li>
    </ul>
</div>
      </div>
    </nav>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js"></script>
</body>
</html>