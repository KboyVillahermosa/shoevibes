<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <title>Before/After Image Slider</title>
    <style>
      
      .slider-text{
        justify-content: center;
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        margin-top: 100px;
        padding: 20px;
      }
      .slider-content{
        width: 100%;
        max-width: 600px;
      }
      .slider-content h1{
        font-size: clamp(1.25rem, -1.0938rem + 7.5vw, 3.125rem);
      }
      .slider-container {
        position: relative;
        width: 700px;
        max-width: 100%;
        height: 400px;
        margin: 50px auto;
        overflow: hidden;
        border-radius: 10px;
      }

      .image-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
      }

      .before-image,
      .after-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      /* Masking Effect */
      .after-image {
        clip-path: inset(0 0 0 50%);
      }

      .slider {
        position: absolute;
        top: 0;
        left: 50%;
        width: 5px;
        height: 100%;
        background-color: white;
        cursor: ew-resize;
        transform: translateX(-50%);
      }

      .slider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 30px;
        height: 30px;
        background-color: white;
        border-radius: 50%;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        transform: translate(-50%, -50%);
      }
      /* Before & After Image Slider */
    
      /* Video Section */
      .video-header {
        justify-content: center;
        display: flex;
        flex-direction: row;
        margin-top: 100px;
        padding: 10px;
      }

      #video-header {
        margin-top: 150px;
      }

      .video-content video {
        width: 100%;
        max-width: 700px;
        border-radius: 20px;
        height: auto;
      }

      @media screen and (max-width: 1000px) {
        .video-header {
          flex-wrap: wrap;
        }

        .video-text {
          max-width: 1000px;
        }
      }

      .video-text {
        width: 100%;
        max-width: 500px;
        margin: 10px 40px;
      }
    
 
      .video-text h1 {
        font-size: clamp(2.5rem, 1.7188rem + 2.5vw, 3.125rem);
      }

      .video-text p {
        font-size: clamp(0.9375rem, 0.7031rem + 0.75vw, 1.125rem);
        margin-top: 10px;
      }
    </style>
  </head>
  <body>

    <!-- Before & After Image Slider -->
     <div class="slider-text">
        <div class="slider-content">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Learn how to design your own custom shoes now</h1>
        <p class="mt-8 text-gray-800">Watch our video tutorial on how to customize your shoes and transform plain ones into a unique expression of your imagination.</p>
        </div>
     </div>
      <div class="slider-container">
      <div class="image-wrapper">
        <img src="image/28.png" class="before-image" />
        <img src="image/40.png" class="after-image" id="afterImage" />
        <div class="slider" id="slider"></div>
      </div>
    </div>
    <!-- Video Sections -->
    <div class="video-header">
      <div class="video-content">
        <video width="600" autoplay loop muted>
          <source src="image/shoevibes2.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
      <div class="video-text">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Step 2-a: Add Your Designs: Any Photos, Logos.</h1>
        <p class="mt-8 text-gray-800">
          You can add your own pictures, symbols, or words. First, upload them.
          Then, move them around to put them on the parts of the shoe that you
          can change. This way, you can make your shoes look just how you want.
        </p>
      </div>
    </div>

    <div class="video-header" id="video-header">
      <div class="video-text">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Step 2-a: Add Your Designs: Any Photos, Logos.</h1>
        <p class="mt-8 text-gray-800">
          You can add your own pictures, symbols, or words. First, upload them.
          Then, move them around to put them on the parts of the shoe that you
          can change. This way, you can make your shoes look just how you want.
        </p>
      </div>
      <div class="video-content">
        <video width="600" autoplay loop muted>
          <source src="image/shoevibes.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
    </div>

    <!-- JavaScript -->
    <script>
      const slider = document.getElementById("slider");
      const afterImage = document.getElementById("afterImage");

      let isDragging = false;

      slider.addEventListener("mousedown", () => {
        isDragging = true;
        document.body.style.userSelect = "none";
      });

      document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        let containerRect = document.querySelector(".slider-container").getBoundingClientRect();
        let offsetX = e.clientX - containerRect.left;
        let percentage = (offsetX / containerRect.width) * 100;
        percentage = Math.max(0, Math.min(100, percentage));

        slider.style.left = `${percentage}%`;
        afterImage.style.clipPath = `inset(0 0 0 ${percentage}%)`;
      });

      document.addEventListener("mouseup", () => {
        isDragging = false;
        document.body.style.userSelect = "auto";
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
  </body>
</html>
