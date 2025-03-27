<?php
session_start();
include 'database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ShoeVibes</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <div class="relative bg-black h-96">
        <div class="absolute inset-0">
            <img src="image/sv.png" alt="Shoe Design" class="w-full h-full object-cover opacity-50">
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Our Story</h1>
            <p class="mt-6 text-xl text-gray-300 max-w-3xl">Transforming ordinary shoes into extraordinary expressions of personal style.</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Mission Section -->
        <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Our Mission</h2>
            <p class="text-lg text-gray-600 mb-6">
                At ShoeVibes, we believe that every pair of shoes tells a story. Our mission is to empower individuals to express their unique style through customized footwear that reflects their personality and creativity.
            </p>
        </div>

        <!-- Values Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Innovation</h3>
                <p class="text-gray-600">Pushing the boundaries of shoe customization with cutting-edge design tools and techniques.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Quality</h3>
                <p class="text-gray-600">Using premium materials and expert craftsmanship to ensure durability and comfort.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Sustainability</h3>
                <p class="text-gray-600">Committed to eco-friendly practices and responsible manufacturing processes.</p>
            </div>
        </div>

        <!-- Story Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">The ShoeVibes Journey</h2>
                <p class="text-lg text-gray-600 mb-4">
                    Founded in 2024, ShoeVibes began with a simple idea: give people the power to design their perfect pair of shoes. What started as a small workshop has grown into a vibrant community of shoe enthusiasts and creative designers.
                </p>
                <p class="text-lg text-gray-600">
                    Today, we offer a wide range of customization options, from color selection to pattern design, enabling our customers to create truly unique footwear that stands out from the crowd.
                </p>
            </div>
            <div class="relative h-96">
                <img src="image/abt.jpg" alt="Our Workshop" class="rounded-lg shadow-xl w-full h-full object-cover">
            </div>
        </div>

        <!-- Team Section -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Meet Our Team</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-12">
                Our passionate team of designers, craftspeople, and customer service experts work together to bring your shoe visions to life.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js"></script>
</body>
</html>