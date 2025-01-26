
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RevenueSure</title>
    <meta name="description" content="A platform to manage leads and businesses efficiently.">
    <meta name="keywords" content="leads, businesses, management, platform">
    <meta name="author" content="Your Name">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">RevenueSure</a>
            <div class="flex space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="hover:underline">Dashboard</a>
                    <a href="search_leads.php" class="hover:underline">Search Leads</a>
                    <a href="manage_credits.php" class="hover:underline">Manage Credits</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="hover:underline">Admin Dashboard</a>
                        <a href="manage_categories.php" class="hover:underline">Manage Categories</a>
                    <?php endif; ?>
                    <a href="logout.php" class="hover:underline">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="hover:underline">Login</a>
                    <a href="register.php" class="hover:underline">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-10 px-4">