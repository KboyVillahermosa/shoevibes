<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>3D Shoe Customizer</title>
  <meta name="description" content="3D shoe customizer" />
  <meta name="keywords" content="3d, shoe, customizer, three.js" />
  <meta name="author" content="Your Name" />
  <link rel="shortcut icon" href="favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Raleway:500,700&display=swap" rel="stylesheet">
  <style>
    /* Loader Styles */
    .loading {
      position: fixed;
      z-index: 50;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background: #f1f1f1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .loader {
      perspective: 120px;
      width: 100px;
      height: 100px;
      position: relative;
    }

    .loader::before {
      content: "";
      position: absolute;
      left: 25px;
      top: 25px;
      width: 50px;
      height: 50px;
      background-color: #ff0000;
      animation: flip 1s infinite;
    }

    @keyframes flip {
      0% {
        transform: rotate(0);
      }

      50% {
        transform: rotateY(180deg);
      }

      100% {
        transform: rotateY(180deg) rotateX(180deg);
      }
    }

    /* Canvas covers full viewport */
    canvas {
      display: block;
      width: 100%;
      height: 100vh;
    }

    /* Options and swatches styling */
    .options {
      position: absolute;
      top: 20px;
      left: 20px;
      z-index: 100;
      display: flex;
      gap: 10px;
    }

    .option {
      cursor: pointer;
      border: 2px solid transparent;
      padding: 5px;
      background: rgba(255, 255, 255, 0.8);
    }

    .option.--is-active {
      border-color: #4CAF50;
    }

    .option img {
      max-width: 50px;
      display: block;
    }

    .tray {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      background: rgba(255, 255, 255, 0.8);
      padding: 10px;
      z-index: 100;
      overflow-x: auto;
      white-space: nowrap;
    }

    .tray__slide {
      display: flex;
      gap: 10px;
    }

    .tray__swatch {
      width: 40px;
      height: 40px;
      border: 2px solid #ddd;
      cursor: pointer;
      flex-shrink: 0;
      background-size: cover;
    }

    /* Save button styling */
    #saveCustomization {
      position: absolute;
      top: 20px;
      right: 20px;
      z-index: 110;
      padding: 10px 20px;
      background-color: white;
      color: black;
      border: 1px solid black;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
    }

    /* Info message styling */
    .info {
      position: absolute;
      top: 80px;
      left: 20px;
      z-index: 100;
      background: rgba(255, 255, 255, 0.8);
      padding: 10px;
      border-radius: 4px;
    }

    .back-button {
      position: absolute;
      top: 20px;
      right: 230px;
      z-index: 120;

    }

    .back-btn {
      background: black;
      padding: 10px;
      padding: 10px 20px;
      background-color: black;
      color: white;
      border: none;
      font-size: 16px;
      cursor: pointer;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div class="loading" id="js-loader">
    <div class="loader"></div>
  </div>

  <h2 style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); z-index:110;">
    MAKE THE SHOE IN YOUR OWN STYLE
  </h2>

  <!-- Options to select shoe parts -->
  <div class="options">
    <div class="option --is-active" data-option="Front">
      <img src="./image/front.bmp" alt="Front" />
    </div>
    <div class="option" data-option="Back">
      <img src="./image/back.bmp" alt="Back" />
    </div>
    <div class="option" data-option="Side">
      <img src="./image/side.bmp" alt="Side" />
    </div>
    <div class="option" data-option="Cube004">
      <img src="./image/main.bmp" alt="Main" />
    </div>
    <div class="option" data-option="back">
      <img src="./image/back-top.bmp" alt="Back Top" />
    </div>
  </div>

  <!-- Three.js Canvas -->
  <canvas id="c"></canvas>

  <!-- Tray for swatches -->
  <div class="tray" id="js-tray">
    <div class="tray__slide" id="js-tray-slide"></div>
  </div>

  <!-- Info message -->
  <div class="info">
    <p>
      <strong>Grab</strong> to rotate the shoe, <strong>Scroll</strong> to zoom,
      and <strong>Drag</strong> swatches to view more.
    </p>
  </div>

  <!-- Save Customization Form -->
  <form id="saveForm" method="POST" action="./shoes-preview/shoes2.php">
    <input type="hidden" id="imageData" name="imageData" value="">
    <input type="hidden" id="customizationData" name="customizationData" value="">
    <button type="submit" id="saveCustomization">Save Customization</button>
  </form>
  <div class="back-button">
    <a href="./shoes-preview/shoes2.php"><button class="back-btn">Back</button></a> 
  </div>

  <!-- Three.js Libraries -->
  <script src="https://unpkg.com/three@0.127.0/build/three.js"></script>
  <script src="https://unpkg.com/three@0.127.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/controls/OrbitControls.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const LOADER = document.getElementById('js-loader');
      const TRAY = document.getElementById('js-tray-slide');
      const canvas = document.getElementById('c');

      let theModel;
      const MODEL_PATH = "shoe.glb";
      let activeOption = 'Front';
      let loaded = false;
      let initRotate = 0;
      const BACKGROUND_COLOR = 0xf1f1f1;

      // Object to store customization settings
      let customization = { parts: {} };

      // Define color and texture options
      const colors = [
        { texture: './image/anim.jpg', size: [2, 2, 2], shininess: 60 },
        { texture: './image/fabric_.jpg', size: [4, 4, 4], shininess: 0 },
        { texture: './image/pattern_.jpg', size: [8, 8, 8], shininess: 10 },
        { texture: './image/denim_.jpg', size: [3, 3, 3], shininess: 0 },
        { texture: './image/quilt_.jpg', size: [6, 6, 6], shininess: 0 },
        { texture: './image/vio.jpg', size: [6, 6, 6], shininess: 0 },
        { texture: './image/shape1.jpg', size: [6, 6, 6], shininess: 0 },
        { texture: './image/shape2.jpg', size: [6, 6, 6], shininess: 0 },
        { color: '131417' },
        { color: '374047' },
        { color: '5f6e78' },
        { color: '7f8a93' },
        { color: '97a1a7' },
        { color: 'acb4b9' },
        { color: 'DF9998' },
        { color: '7C6862' },
        { color: 'A3AB84' },
        { color: 'D6CCB1' },
        { color: 'F8D5C4' },
        { color: 'A3AE99' },
        { color: 'EFF2F2' },
        { color: 'B0C5C1' },
        { color: '8B8C8C' },
        { color: '565F59' },
        { color: 'CB304A' },
        { color: 'FED7C8' },
        { color: 'C7BDBD' },
        { color: '3DCBBE' },
        { color: '264B4F' },
        { color: '389389' },
        { color: '85BEAE' },
        { color: 'F2DABA' },
        { color: 'F2A97F' },
        { color: 'D85F52' },
        { color: 'D92E37' },
        { color: 'FC9736' },
        { color: 'F7BD69' },
        { color: 'A4D09C' },
        { color: '4C8A67' },
        { color: '25608A' },
        { color: '75C8C6' },
        { color: 'F5E4B7' },
        { color: 'E69041' },
        { color: 'E56013' },
        { color: '11101D' },
        { color: '630609' },
        { color: 'C9240E' },
        { color: 'EC4B17' },
        { color: '281A1C' },
        { color: '4F556F' },
        { color: '64739B' },
        { color: 'CDBAC7' },
        { color: '946F43' },
        { color: '66533C' },
        { color: '173A2F' },
        { color: '153944' },
        { color: '27548D' },
        { color: '438AAC' }
      ];

      // Set up Three.js scene
      const scene = new THREE.Scene();
      scene.background = new THREE.Color(BACKGROUND_COLOR);
      scene.fog = new THREE.Fog(BACKGROUND_COLOR, 20, 100);

      // Renderer with preserveDrawingBuffer enabled (to capture canvas)
      const renderer = new THREE.WebGLRenderer({
        canvas,
        antialias: true,
        preserveDrawingBuffer: true
      });
      renderer.shadowMap.enabled = true;
      renderer.setPixelRatio(window.devicePixelRatio);
      renderer.setSize(window.innerWidth, window.innerHeight);

      // Camera setup
      const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 1000);
      camera.position.set(0, 0, 5);

      // Initial material for model parts
      const INITIAL_MTL = new THREE.MeshPhongMaterial({ color: BACKGROUND_COLOR, shininess: 10 });
      const INITIAL_MAP = [
        { childID: "Front", mtl: INITIAL_MTL },
        { childID: "Back", mtl: INITIAL_MTL },
        { childID: "Side", mtl: INITIAL_MTL },
        { childID: "Cube004", mtl: INITIAL_MTL },
        { childID: "back", mtl: INITIAL_MTL }
      ];

      // Load the GLTF model
      const loader = new THREE.GLTFLoader();
      loader.load(MODEL_PATH, function (gltf) {
        theModel = gltf.scene;
        theModel.traverse(o => {
          if (o.isMesh) {
            o.castShadow = true;
            o.receiveShadow = true;
          }
        });
        theModel.scale.set(1.6, 1.6, 1.6);
        theModel.rotation.y = Math.PI;
        theModel.position.y = 0;

        INITIAL_MAP.forEach(object => {
          initColor(theModel, object.childID, object.mtl);
          // Save initial state
          customization.parts[object.childID] = { type: 'initial' };
        });
        scene.add(theModel);
        LOADER.style.display = 'none';
      }, undefined, function (error) {
        console.error(error);
      });

      function initColor(parent, type, mtl) {
        parent.traverse(o => {
          if (o.isMesh && o.name.includes(type)) {
            o.material = mtl;
            o.nameID = type;
          }
        });
      }

      // Add lights
      const hemiLight = new THREE.HemisphereLight(0xffffff, 0xffffff, 0.61);
      hemiLight.position.set(0, 50, 0);
      scene.add(hemiLight);

      const dirLight = new THREE.DirectionalLight(0xffffff, 0.54);
      dirLight.position.set(-8, 12, 8);
      dirLight.castShadow = true;
      dirLight.shadow.mapSize = new THREE.Vector2(1024, 1024);
      scene.add(dirLight);

      // Add a floor
      const floorGeometry = new THREE.PlaneGeometry(5000, 5000, 1, 1);
      const floorMaterial = new THREE.MeshPhongMaterial({ color: 0xeeeeee, shininess: 0 });
      const floor = new THREE.Mesh(floorGeometry, floorMaterial);
      floor.rotation.x = -Math.PI / 2;
      floor.receiveShadow = true;
      floor.position.y = -1;
      scene.add(floor);

      // Orbit controls for interactivity
      const controls = new THREE.OrbitControls(camera, renderer.domElement);
      controls.maxPolarAngle = Math.PI / 2;
      controls.minPolarAngle = Math.PI / 3;
      controls.enableDamping = true;
      controls.enablePan = false;
      controls.dampingFactor = 0.1;
      controls.autoRotate = true;
      controls.autoRotateSpeed = 0.2;

      // Render loop
      function animate() {
        controls.update();
        renderer.render(scene, camera);
        requestAnimationFrame(animate);
        if (resizeRendererToDisplaySize(renderer)) {
          camera.aspect = canvas.clientWidth / canvas.clientHeight;
          camera.updateProjectionMatrix();
        }
        if (theModel && !loaded) {
          initialRotation();
        }
      }
      animate();

      function resizeRendererToDisplaySize(renderer) {
        const canvas = renderer.domElement;
        const width = window.innerWidth;
        const height = window.innerHeight;
        const needResize = canvas.width !== width || canvas.height !== height;
        if (needResize) {
          renderer.setSize(width, height, false);
        }
        return needResize;
      }

      function initialRotation() {
        initRotate++;
        if (initRotate <= 120) {
          theModel.rotation.y += Math.PI / 60;
        } else {
          loaded = true;
        }
      }

      // Build swatches in the tray
      function buildColors(colors) {
        colors.forEach((color, i) => {
          const swatch = document.createElement('div');
          swatch.classList.add('tray__swatch');
          if (color.texture) {
            swatch.style.backgroundImage = "url(" + color.texture + ")";
          } else {
            swatch.style.background = "#" + color.color;
          }
          swatch.setAttribute('data-key', i);
          TRAY.appendChild(swatch);
        });
      }
      buildColors(colors);

      // Option selection for shoe parts
      const options = document.querySelectorAll(".option");
      options.forEach(option => {
        option.addEventListener('click', function () {
          options.forEach(opt => opt.classList.remove('--is-active'));
          this.classList.add('--is-active');
          activeOption = this.getAttribute('data-option');
        });
      });

      // Swatch selection: update material and save customization data
      TRAY.querySelectorAll(".tray__swatch").forEach(swatch => {
        swatch.addEventListener('click', function () {
          const color = colors[parseInt(this.dataset.key)];
          let new_mtl;
          if (color.texture) {
            const txt = new THREE.TextureLoader().load(color.texture);
            txt.repeat.set(color.size[0], color.size[1], color.size[2]);
            txt.wrapS = THREE.RepeatWrapping;
            txt.wrapT = THREE.RepeatWrapping;
            new_mtl = new THREE.MeshPhongMaterial({ map: txt, shininess: color.shininess || 10 });
            customization.parts[activeOption] = { type: 'texture', value: color.texture, size: color.size, shininess: color.shininess || 10 };
          } else {
            new_mtl = new THREE.MeshPhongMaterial({ color: parseInt('0x' + color.color), shininess: color.shininess || 10 });
            customization.parts[activeOption] = { type: 'color', value: color.color, shininess: color.shininess || 10 };
          }
          setMaterial(theModel, activeOption, new_mtl);
        });
      });

      function setMaterial(parent, type, mtl) {
        parent.traverse(o => {
          if (o.isMesh && o.nameID === type) {
            o.material = mtl;
          }
        });
      }

      // Save customization: capture image and JSON data
      const saveForm = document.getElementById('saveForm');
      if (saveForm) {
        saveForm.addEventListener('submit', function (e) {
          e.preventDefault();
          if (!theModel) {
            alert("Model not fully loaded. Please wait and try again.");
            return;
          }
          async function captureAndSave() {
            controls.update();
            renderer.render(scene, camera);
            await new Promise(resolve => requestAnimationFrame(resolve));
            await new Promise(resolve => setTimeout(resolve, 100));
            const dataURL = canvas.toDataURL('image/png');
            document.getElementById('imageData').value = dataURL;
            document.getElementById('customizationData').value = JSON.stringify(customization);
            e.target.submit();
          }
          captureAndSave();
        });
      }
    });
  </script>
</body>

</html>