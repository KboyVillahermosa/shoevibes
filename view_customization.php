<?php
// Make sure a JSON file path is provided
if(!isset($_GET['json']) || !file_exists($_GET['json'])) {
    die("Invalid customization file.");
}
$customizationData = file_get_contents($_GET['json']);
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View 3D Customization</title>
  <style>
    body, html { margin: 0; padding: 0; overflow: hidden; }
    canvas { display: block; }
    /* Simple back button styling */
    .back-btn {
      position: absolute;
      top: 20px;
      left: 20px;
      z-index: 110;
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
  <!-- Back button to boss.php -->
  <a href="./shoes-preview/shoes1.php" class="back-btn">Back</a>
  <canvas id="c"></canvas>
  <!-- Three.js Libraries -->
  <script src="https://unpkg.com/three@0.127.0/build/three.js"></script>
  <script src="https://unpkg.com/three@0.127.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/controls/OrbitControls.js"></script>
  <script>
    // Retrieve customization data from PHP
    const customization = <?php echo $customizationData; ?>;
    const MODEL_PATH = "shoe.glb"; // Ensure your GLTF model is accessible

    const canvas = document.getElementById('c');
    const renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true,
      preserveDrawingBuffer: true
    });
    renderer.shadowMap.enabled = true;
    renderer.setSize(window.innerWidth, window.innerHeight);

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf1f1f1);
    scene.fog = new THREE.Fog(0xf1f1f1, 20, 100);

    const camera = new THREE.PerspectiveCamera(50, window.innerWidth/window.innerHeight, 0.1, 1000);
    camera.position.set(0, 0, 5);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.1;
    controls.enablePan = false;
    controls.autoRotate = true;
    controls.autoRotateSpeed = 0.2;

    // Add lights
    const hemiLight = new THREE.HemisphereLight(0xffffff, 0xffffff, 0.61);
    hemiLight.position.set(0,50,0);
    scene.add(hemiLight);
    const dirLight = new THREE.DirectionalLight(0xffffff, 0.54);
    dirLight.position.set(-8,12,8);
    dirLight.castShadow = true;
    scene.add(dirLight);

    // Floor
    const floor = new THREE.Mesh(
      new THREE.PlaneGeometry(5000,5000,1,1),
      new THREE.MeshPhongMaterial({ color: 0xeeeeee, shininess: 0 })
    );
    floor.rotation.x = -Math.PI/2;
    floor.position.y = -1;
    floor.receiveShadow = true;
    scene.add(floor);

    // Create a default white material for parts without customization
    const defaultWhiteMaterial = new THREE.MeshPhongMaterial({ color: 0xffffff, shininess: 10 });

    // Load the GLTF model and apply customization settings
    let theModel;
    const loader = new THREE.GLTFLoader();
    loader.load(MODEL_PATH, function(gltf) {
      theModel = gltf.scene;
      theModel.scale.set(1.6, 1.6, 1.6);
      theModel.rotation.y = Math.PI;
      theModel.position.y = 0;

      // First, apply a default white material to all relevant parts
      theModel.traverse(o => {
        if(o.isMesh) {
          // Here we assume the model parts are identified by their name containing one of your keys.
          // You might customize this condition to suit your model.
          if(o.name.match(/Front|Back|Side|Cube004|back/)) {
            o.material = defaultWhiteMaterial;
          }
        }
      });

      // Then, apply customization for each part saved in the JSON if available
      for(let part in customization.parts) {
        const setting = customization.parts[part];
        theModel.traverse(o => {
          if(o.isMesh && o.name.includes(part)) {
            if(setting.type === 'texture') {
              const txt = new THREE.TextureLoader().load(setting.value);
              txt.repeat.set(setting.size[0], setting.size[1], setting.size[2]);
              txt.wrapS = THREE.RepeatWrapping;
              txt.wrapT = THREE.RepeatWrapping;
              o.material = new THREE.MeshPhongMaterial({ map: txt, shininess: setting.shininess });
            } else if(setting.type === 'color') {
              o.material = new THREE.MeshPhongMaterial({ color: parseInt('0x' + setting.value), shininess: setting.shininess });
            }
          }
        });
      }
      scene.add(theModel);
    });

    function animate() {
      controls.update();
      renderer.render(scene, camera);
      requestAnimationFrame(animate);
    }
    animate();

    window.addEventListener('resize', function() {
      renderer.setSize(window.innerWidth, window.innerHeight);
      camera.aspect = window.innerWidth/window.innerHeight;
      camera.updateProjectionMatrix();
    });
  </script>
</body>
</html>
