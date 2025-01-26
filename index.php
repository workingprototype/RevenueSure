<?php
session_start();
require 'db.php'; // Include the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RevenueSure</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Include Tailwind CSS -->
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">RevenueSure</a>
            <div class="flex space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="search_leads.php" class="hover:underline">Search Leads</a>
                    <a href="manage_credits.php" class="hover:underline">Manage Credits</a>
                    <a href="logout.php" class="hover:underline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="hover:underline">Login</a>
                    <a href="register.php" class="hover:underline">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container mx-auto mt-10 px-4">
        <div class="bg-white p-8 rounded-lg shadow-lg text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to RevenueSure</h1>
            <p class="text-gray-600 mb-6">Find and connect with businesses effortlessly. Access leads, manage credits, and grow your network.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="search_leads.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search Leads</a>
            <?php else: ?>
                <a href="register.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Get Started</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mx-auto mt-16 px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Search Leads</h3>
                <p class="text-gray-600">Easily search for leads by name, email, or category.</p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Manage Credits</h3>
                <p class="text-gray-600">Buy and manage credits to access premium features.</p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">User-Friendly</h3>
                <p class="text-gray-600">Simple and intuitive interface for all users.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 mt-16 py-6 text-white">
        <div class="container mx-auto text-center">
            <p>&copy; 2023 RevenueSure. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>