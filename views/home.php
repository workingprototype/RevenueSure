<?php
$appName = $_ENV['APP_NAME'] ?? 'RevenueSure';  //Fallback if not set
?>

    <!-- Hero Section -->
    <div class="container mx-auto mt-10 px-4 fade-in">
        <div class="bg-white p-8 rounded-2xl shadow-xl text-center border-l-4 border-blue-500 transition hover:shadow-2xl">
            <h1 class="text-5xl font-bold text-gray-900 mb-4">Welcome to <?php echo htmlspecialchars($appName); ?></h1>
            <p class="text-gray-700 mb-6">Find and connect with businesses effortlessly. Access leads, manage credits, and grow your network.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>leads/search" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Search Leads</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>auth/register" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Get Started</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mx-auto mt-16 px-4 fade-in">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-6 rounded-2xl shadow-md text-center  transition hover:shadow-2xl border-l-4 border-purple-500">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Search Leads</h3>
                <p class="text-gray-600">Easily search for leads by name, email, or category.</p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-white p-6 rounded-2xl shadow-md text-center transition hover:shadow-2xl border-l-4 border-yellow-500">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Manage Credits</h3>
                <p class="text-gray-600">Buy and manage credits to access premium features.</p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-white p-6 rounded-2xl shadow-md text-center transition hover:shadow-2xl border-l-4 border-green-500">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">User-Friendly</h3>
                <p class="text-gray-600">Simple and intuitive interface for all users.</p>
            </div>
        </div>