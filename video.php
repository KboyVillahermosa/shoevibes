<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
  </head>
  <style>
    .video-header {
      justify-content: center;
      display: flex;
      flex-direction: row;
      margin-top: 100px;
      padding: 10px;
    }
    #video-header{
      margin-top: 10px;
    }

    .video-content video{
      width: 100%;
      max-width: 700px;
      border-radius: 20px;
      height: auto;

    }
    @media screen and (max-width: 1000px) {
      .video-header {
      flex-wrap: wrap;
    }
   .video-text{
    max-width: 1000px;
   }
 
    }
    .video-text{
        width: 100%;
        max-width: 500px;
        margin: 10px 40px;
    }
    .video-text h1{
        font-size: clamp(1.25rem, -1.0938rem + 7.5vw, 3.125rem);
    }
    .video-text p{
        font-size: clamp(0.9375rem, 0.5469rem + 1.25vw, 1.25rem);
    }
   
  </style>
  <body>
    <div class="video-header">
      <div class="video-content">
        <video width="600" autoplay loop muted>
          <source src="./image/shoes2.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
      <div class="video-text">
        <h1>Step 2-a: Add Your Designs: Any Photos, Logos.</h1>
        <p>
          You can add your own pictures, symbols, or words. First, upload them.
          Then, move them around to put them on the parts of the shoe that you
          can change. This way, you can make your shoes look just how you want.
        </p>
      </div>
    </div>
    <div class="video-header" id="video-header">
        <div class="video-text">
          <h1>Step 2-a: Add Your Designs: Any Photos, Logos.</h1>
          <p>
            You can add your own pictures, symbols, or words. First, upload them.
            Then, move them around to put them on the parts of the shoe that you
            can change. This way, you can make your shoes look just how you want.
          </p>
        </div>
        <div class="video-content">
            <video width="600" autoplay loop muted>
              <source src="./image/shoes2.mp4" type="video/mp4" />
              Your browser does not support the video tag.
            </video>
          </div>
      </div>
  </body>
</html>
