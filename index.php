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
  <link rel="stylesheet" href="./css/indexx.css">
  <title>Document</title>
  <style>
    #model-container {
      width: 600PX;
      height: 60vh;
      position: relative;
      min-height: 300px;
    }

    canvas {
      width: 100%;
      height: 100%;
      display: block;
    }

    /* General layout improvements */
    .header {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      margin-top: 70px;
      padding: 10px;

    }

    @media (max-width: 850px) {
      .header {
        flex-wrap: wrap;
      }
    }

    .header-content {
      width: 100%;
      max-width: 650px;
      margin: 20px 20px;
    }

    .header-image video {
      width: 100%;
      border-radius: 10px;
      max-width: 600px;
    }

    .products-title {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 100px;
    }

    .products-header {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      flex-direction: row;
      margin-top: 60px;
      gap: 20px;
      padding: 20px;
    }

    .products-content {
      width: 100%;
      max-width: 400px;
      padding: 20px;
      box-shadow: 1px 1px 1px 1px gray;
    }

    .products-content img {
      width: 100%;
      max-width: 500px;
      border-radius: 10px;
      height: auto;
    }

    /* Media Queries */
    @media (max-width: 768px) {
      #model-container {
        height: 50vh;
      }

      .header {
        margin-top: 50px;
      }

      .header-content {
        max-width: 100%;
      }

      .products-content {
        max-width: 100%;
      }
    }

    @media (max-width: 480px) {
      #model-container {
        height: 40vh;
      }
    }

    .logos img {
      width: 100%;
      max-width: 100px;
    }

    .image-container {
      position: relative;
      width: 100%;
      display: inline-block;
    }

    .image-container img {
      width: 100%;
      /* Adjust based on your needs */
      display: block;
    }

    .hover-image {
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
    }

    .image-container:hover .hover-image {
      opacity: 1;
    }

    .customize {
      padding: 10px;
      border-radius: 10px;
      background-color: black;
      color: white;
      width: 100%;
    }

    .products-text {
      font-size: clamp(0.9375rem, 0.1563rem + 2.5vw, 1.5625rem);
    }

    .products-para {
      font-size: clamp(0.625rem, 0rem + 2vw, 1.125rem);
    }

    .search-sidebar {
      position: fixed;
      top: 0;
      right: -600px;
      /* Hidden initially */
      width: 600px;
      height: 100%;
      background: #fff;
      box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
      transition: right 0.3s ease-in-out;
      padding: 20px;
      z-index: 1000;
      overflow-y: auto;
      border-left: 4px solid #000;
    }

    .search-sidebar.active {
      right: 0;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 26px;
      cursor: pointer;
      border: none;
      background: none;
      font-weight: bold;
    }

    /* Overlay */
    #searchOverlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    #searchOverlay.active {
      display: block;
    }

    /* Search Input */
    #searchInput {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: 2px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      transition: 0.3s;
    }

    #searchInput:focus {
      border-color: #000;
      outline: none;
    }

    /* Search Results */
    #searchResults {
      margin-top: 20px;
      text-align: left;
    }

    .search-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px;
      border-bottom: 1px solid #ddd;
      transition: background 0.3s;
      border-radius: 8px;
    }

    .search-item:hover {
      background: #f9f9f9;
    }

    .search-item img {
      width: 80px;
      height: 80px;
      border-radius: 8px;
      object-fit: cover;
    }

    .search-item p {
      margin: 0;
      font-size: 18px;
      font-weight: 500;
    }

    .search-item .price {
      color: #ff4d00;
      font-weight: bold;
    }

    .search-item a {
      color: #000;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .search-item a:hover {
      color: #ff4d00;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
      .search-sidebar {
        width: 100%;
        right: -100%;
      }

      .search-sidebar.active {
        right: 0;
      }
    }
  </style>
</head>

<body>
  <!-- Add the background pattern container -->
  <div class="absolute inset-0 -z-10 overflow-hidden">
    <svg class="absolute top-0 left-[max(50%,25rem)] h-[64rem] w-[128rem] -translate-x-1/2 stroke-gray-200 [mask-image:radial-gradient(64rem_64rem_at_top,white,transparent)]" aria-hidden="true">
      <defs>
        <pattern id="e813992c-7d03-4cc4-a2bd-151760b470a0" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
          <path d="M100 200V.5M.5 .5H200" fill="none" />
        </pattern>
      </defs>
      <svg x="50%" y="-1" class="overflow-visible fill-gray-50">
        <path d="M-100.5 0h201v201h-201Z M699.5 0h201v201h-201Z M499.5 400h201v201h-201Z M-300.5 600h201v201h-201Z" stroke-width="0" />
      </svg>
      <rect width="100%" height="100%" stroke-width="0" fill="url(#e813992c-7d03-4cc4-a2bd-151760b470a0)" />
    </svg>
  </div>
  
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
            <a href="#" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
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
            <a href="about-us.php" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">Abou tUs
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-black transform origin-left scale-x-0 transition-transform duration-300 group-hover:scale-x-100"></span>
                </span>
            </a>
        </li>
        <li>
            <a href="contact.php" class="block py-2 px-3 text-gray-800 hover:bg-gray-100 md:hover:bg-transparent md:border-0 relative group md:p-0 transition-colors duration-300">
                <span class="relative">Contacts
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
  <section>
    <div id="searchSidebar" class="search-sidebar">
      <div class="search-sidebar-content">
        <span class="close-btn" onclick="closeSearchSidebar()">&times;</span>
        <h2>Search Products</h2>
        <input type="text" id="searchInput" placeholder="Search for products..." onkeyup="filterProducts()">
        <div id="searchResults"></div>
      </div>
    </div>
    <div id="searchOverlay" onclick="closeSearchSidebar()"></div>
  </section>

  <section class="">

    <div class="header">
      <div class="header-content">

        <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-800 md:text-5xl lg:text-6xl">
          Custom Shoes Made Just for You</h1>
        <p class="my-4 text-lg text-gray-500"> At ShoeVibes, we believe that footwear is more than just a necessity it’s
          a statement, an extension of your personality, and a way to stand out. </p>
      </div>
      <div class="header-image">
        <video width="600" autoplay loop muted>
          <source src="image/header.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </section>

  <!-------------- SHOP BY CATEGORIES  --------------------->
  <section>
    <?php include_once("video.php"); ?>
  </section>

  <section>
    <div class="products-title">
      <h1 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-800 md:text-5xl lg:text-4xl">
        Collection</h1>
    </div>

    <div class="products-header">
      <div class="products-content">
        <div class="image-container">
          <img class="default-image" src="./image/32.png" alt="">
          <img class="hover-image" src="./image/27.png" alt="">
          <h1 class="products-text mt-8">Customizable Lightweight Mesh Athletic</h1>
          <p class="products-para mt-3 mb-3">₱5,900.00</p>
          <a href="./shoes-preview/shoes1.php"> <button class="customize">Customize</button> </a>
        </div>
      </div>

      <div class="products-content">
        <a href="./shoes-preview/shoes2.php">
          <div class="image-container">
            <img class="default-image" src="./image/1.png" alt="">
            <img class="hover-image" src="./image/2.png" alt="">
            <h1 class="products-text mt-8">Customizable Air-Force Zeros Low Top</h1>
            <p class="products-para mt-3 mb-3">₱4,500.00</p>
            <a href="./shoes-preview/shoes2.php"> <button class="customize">Customize</button> </a>
          </div>
        </a>
      </div>

      <div class="products-content">
        <div class="image-container">
          <img class="default-image" src="./image/7.png" alt="">
          <img class="hover-image" src="./image/8.png" alt="">
          <h1 class="products-text mt-8">Customizable Premium Synthetic Leather Shoes</h1>
          <p class="products-para mt-3 mb-3">₱4,200.00</p>
          <a href="./shoes-preview/shoes3.php"> <button class="customize">Customize</button> </a>
        </div>
      </div>

      <div class="products-content">
        <div class="image-container">
          <img class="default-image" src="./image/15.png" alt="">
          <img class="hover-image" src="./image/16.png" alt="">
          <h1 class="products-text mt-8">Customizable High-Top Synthetic Leather Sneakers </h1>
          <p class="products-para mt-3 mb-3">₱4,200.00</p>
          <a href="./shoes-preview/shoes4.php"> <button class="customize">Customize</button> </a>
        </div>
      </div>

      <div class="products-content">
        <div class="image-container">
          <img class="default-image" src="./image/20.png" alt="">
          <img class="hover-image" src="./image/21.png" alt="">
          <h1 class="products-text mt-8">Customizable Lightweight Breathable Running Sneakers</h1>
          <p class="products-para mt-3 mb-3">₱4,200.00</p>
          <a href="./shoes-preview/shoes5.php"> <button class="customize">Customize</button> </a>
        </div>
      </div>

      <div class="products-content">
        <div class="image-container">
          <img class="default-image" src="./image/33.png" alt="">
          <img class="hover-image" src="./image/34.png" alt="">
          <h1 class="products-text mt-8">Customizable Eco Vegan Leather Boots</h1>
          <p class="products-para mt-3 mb-3">₱6,100.00</p>
          <a href="./shoes-preview/shoes6.php"> <button class="customize">Customize</button> </a>
        </div>
      </div>
    </div>

  </section>


  <section>
    <?php include_once "footer.php"; ?>

  </section>


  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.js"></script>
  <script src="https://unpkg.com/three@0.127.0/build/three.js"></script>
  <script src="https://unpkg.com/three@0.127.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/controls/OrbitControls.js"></script>
  <script src="./js/index.js"></script>

  <script>
    const scene = new THREE.Scene();

    // Create Camera with an aspect ratio based on container dimensions
    const container = document.getElementById('model-container');
    const aspect = container.clientWidth / container.clientHeight;
    const camera = new THREE.PerspectiveCamera(50, aspect, 0.1, 1000);
    camera.position.set(0, 2, 5);  // Adjust camera position if needed

    // Create Renderer and set its size to the container's dimensions
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);

    // Add Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 1);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
    directionalLight.position.set(5, 5, 5);
    scene.add(directionalLight);

    // Load GLB Model and adjust its scale for a larger appearance
    const loader = new THREE.GLTFLoader();
    loader.load(
      './image/shoez.glb',  // Ensure your model path is correct
      function (gltf) {
        const model = gltf.scene;
        // Adjust the model scale so that it fills the container as desired
        model.scale.set(17, 17, 17);  // Tweak this value as needed
        scene.add(model);
      },
      function (xhr) {
        console.log(`Loading: ${Math.round((xhr.loaded / xhr.total) * 100)}%`);
      },
      function (error) {
        console.error("Error loading the model:", error);
      }
    );

    // Add Orbit Controls for interaction
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.screenSpacePanning = false;
    controls.minDistance = 0;
    controls.maxDistance = 500;

    // Animation loop
    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
    }
    animate();

    // Handle container resize
    function resizeCanvas() {
      const width = container.clientWidth;
      const height = container.clientHeight;

      renderer.setSize(width, height);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
    }

    window.addEventListener('resize', resizeCanvas);

    // Call resizeCanvas once at the beginning
    function openSearchSidebar() {
      document.getElementById("searchSidebar").classList.add("active");
      document.getElementById("searchOverlay").classList.add("active");
    }

    function closeSearchSidebar() {
      document.getElementById("searchSidebar").classList.remove("active");
      document.getElementById("searchOverlay").classList.remove("active");
    }

    function filterProducts() {
      let query = document.getElementById("searchInput").value.toLowerCase();
      let products = document.querySelectorAll(".products-content");
      let resultsContainer = document.getElementById("searchResults");

      resultsContainer.innerHTML = ""; // Clear previous results

      products.forEach(product => {
        let productName = product.querySelector(".products-text").textContent.toLowerCase();
        if (productName.includes(query)) {
          let imgSrc = product.querySelector(".default-image").src;
          let price = product.querySelector(".products-para").textContent;
          let link = product.querySelector("a").href;

          resultsContainer.innerHTML += `
                    <div class="search-item">
                        <img src="${imgSrc}" alt="${productName}">
                        <div>
                            <p><strong>${productName}</strong></p>
                            <p class="price">${price}</p>
                            <a href="${link}">View Product</a>
                        </div>
                    </div>
                `;
        }
      });

      // If no products match
      if (resultsContainer.innerHTML === "") {
        resultsContainer.innerHTML = "<p>No products found</p>";
      }
    }
  </script>

</body>

</html>