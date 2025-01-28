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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <!-- Include FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">RevenueSure</a>
            <div class="flex space-x-4">
                <div class="relative">
                    <button id="notificationButton" class="hover:underline relative">
                        <!-- Bell Icon -->
                        <i class="fas fa-bell"></i>
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = :user_id AND is_read = 0");
                        $stmt->bindParam(':user_id', $_SESSION['user_id']);
                        $stmt->execute();
                        $unread = $stmt->fetch(PDO::FETCH_ASSOC)['unread'];
                        if ($unread > 0): ?>
                            <!-- Notification Count -->
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full absolute -top-2 -right-2"><?php echo $unread; ?></span>
                        <?php endif; ?>
                    </button>
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5");
                        $stmt->bindParam(':user_id', $_SESSION['user_id']);
                        $stmt->execute();
                        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if ($notifications): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <a href="view_notification.php?id=<?php echo $notification['id']; ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                    <?php if (!$notification['is_read']): ?>
                                        <span class="text-xs text-blue-600">New</span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="px-4 py-2 text-gray-600">No notifications.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="hover:underline">Dashboard</a>
                    <a href="search_leads.php" class="hover:underline">Search</a>
                    <a href="add_lead.php" class="hover:underline">Leads</a>
                    <a href="manage_credits.php" class="hover:underline">Manage Credits</a>
                    <a href="view_tasks.php" class="hover:underline">View Tasks</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="reporting_dashboard.php" class="hover:underline">Reporting</a>
    <a href="add_employee.php" class="hover:underline">Add Employees</a>
<?php endif; ?>
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
    <script>
    document.getElementById('notificationButton').addEventListener('click', function() {
        document.getElementById('notificationDropdown').classList.toggle('hidden');
    });
    </script>
    <!-- Main Content -->
    <div class="container mx-auto mt-10 px-4">