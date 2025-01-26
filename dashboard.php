<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, credits FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch leads count
$stmt = $conn->prepare("SELECT COUNT(*) as total_leads FROM leads");
$stmt->execute();
$leads_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_leads'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RevenueSure</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">RevenueSure</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:underline">Dashboard</a>
                <a href="search_leads.php" class="hover:underline">Search Leads</a>
                <a href="manage_credits.php" class="hover:underline">Manage Credits</a>
                <a href="logout.php" class="hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Credits Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Your Credits</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $user['credits']; ?></p>
                <p class="text-gray-600 mt-2">Credits available for accessing leads.</p>
                <a href="manage_credits.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Manage Credits</a>
            </div>

            <!-- Leads Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Total Leads</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $leads_count; ?></p>
                <p class="text-gray-600 mt-2">Leads available in the platform.</p>
                <a href="search_leads.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search Leads</a>
            </div>

            <!-- Recent Activity Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Recent Activity</h3>
                <p class="text-gray-600">No recent activity.</p>
            </div>
        </div>
    </div>
</body>
</html>