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
  <style>
    /* Fixed container for the canvas */
    #model-container {
      width: 600PX;
      /* Use full width of the parent */
      height: 60vh;
      /* Adjust the height as a percentage of the viewport height */
      position: relative;
      min-height: 300px;
      /* prevent the container from collapsing too much */
    }

    /* Ensure canvas fills the container */
    canvas {
      width: 100%;
      height: 100%;
      display: block;
    }

    /* General layout improvements */
    .header {
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      margin-top: 70px;
      padding: 10px;
    }

    .header-content {
      width: 100%;
      max-width: 700px;
    }

    .header-image img {
      width: 100%;
      height: auto;
      border-radius: 80px;
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
    }

    .products-content img {
      width: 100%;
      max-width: 400px;
      border-radius: 10px;
      height: auto;

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
  </style>
</head>

<body>
  <section>
    <nav class="bg-white border-gray-200">
      <div class="logos max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
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
            <li>
              <a href="#"
                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Home</a>
            </li>
            <li>
              <a href="#"
                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">About</a>
            </li>
            <li>
              <a href="#"
                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Services</a>
            </li>
            <li>
              <a href="#"
                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Pricing</a>
            </li>
            <li>
              <a href="#"
                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Contact</a>
            </li>

            <li>
              <a href="logout.php"
                class="block py-2 px-3 text-black rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
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
        <div id="model-container"></div>


      </div>
    </div>
  </section>
  
  <section>
    <div class="products-title">
      <h1 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-800 md:text-5xl lg:text-4xl">
        Collection</h1>
    </div>

    <div class="products-header">
      <div class="products-content">
        <a href="./shoes-preview/shoes1.php">
          <img src="./image/nike1.png" alt=""></a>
      </div>
      <div class="products-content">
        <img src="./image/nike1.png" alt="">
      </div>
      <div class="products-content">
        <img src="./image/nikev1.png" alt="">
      </div>
      <div class="products-content">
        <img src="./image/s1.png" alt="">
      </div>
      <div class="products-content">
        <img src="./image/s1.png" alt="">
      </div>
      <div class="products-content">
        <img src="./image/s1.png" alt="">
      </div>

    </div>
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
      renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2)) // Improve sharpness on high-res devices
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
    }

    window.addEventListener('resize', resizeCanvas);

    // Call resizeCanvas once at the beginning
    resizeCanvas();
  </script>

</body>

</html>