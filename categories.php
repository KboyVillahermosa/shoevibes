

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <title>Document</title>
</head>
<body>
    <style>
        .categories-title{
    justify-content: center;
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    margin-top: 50px;
}

.categories-header{
    justify-content: center;
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
    margin-top: 20px;
    gap: 20px;
}

.categories-content{
    width: 100%;
    max-width: 500px;
}

.categories-content img{
    width: 100%;
    max-width: 500px;
}
    </style>
         <section>
      <div class="categories-title">
      <h1 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-800 md:text-5xl lg:text-4xl">Choose your categories</h1>
      </div>
      <div class="categories-header">
        <div class="categories-content">
          <img src="./image/men.webp" alt="">
        </div>
        <div class="categories-content">
          <img src="./image/women.webp" alt="">
        </div>
        <div class="categories-content">
          <img src="./image/kids.webp" alt="">
        </div>
      </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>