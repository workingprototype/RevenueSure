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
     <style>
        .submenu {
            display: none;
            padding-left: 20px;
        }
        .menu-item:hover .submenu {
          display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <div class="flex h-screen">
        <!-- Left Sidebar -->
        <aside class="bg-gray-800 text-white w-64 p-4">
            <a href="index.php" class="text-2xl font-bold block mb-6">RevenueSure</a>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User Menu -->
                     <a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
                    <a href="search_leads.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-search mr-2"></i>Search Leads</a>
                   <a href="view_tasks.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-tasks mr-2"></i>View Tasks</a>
                    <a href="manage_credits.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-credit-card mr-2"></i>Manage Credits</a>


                    <!-- Admin Menu -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <h6 class="text-gray-500 uppercase mt-4 mb-2 px-4">Admin</h6>
                         <div class="menu-item">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-user-tie mr-2"></i>Manage Leads</a>
                            <div class="submenu">
                                  <a href="add_lead.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-plus mr-2"></i>Add Lead</a>
                                   <a href="import_leads.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-file-import mr-2"></i>Import Leads</a>


                            </div>
                        </div>
                          <div class="menu-item">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-user-check mr-2"></i>Manage Customers</a>
                               <div class="submenu">
                                   
                                </div>
                        </div>
                         <div class="menu-item">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-users mr-2"></i>Manage Employees</a>
                             <div class="submenu">
                                <a href="add_employee.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-user-plus mr-2"></i>Add Employee</a>
                                <a href="manage_employees.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-users-cog mr-2"></i>View Employees</a>
                                   </div>
                        </div>

                         <div class="menu-item">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-list mr-2"></i>Manage Categories</a>
                             <div class="submenu">
                                 <a href="manage_categories.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-list-alt mr-2"></i>View Categories</a>
                                   <a href="add_category.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-plus mr-2"></i>Add Category</a>
                               </div>
                        </div>
                         <a href="reporting_dashboard.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-chart-bar mr-2"></i>Reporting</a>
                    <?php endif; ?>
                     <a href="logout.php" class="block py-2 px-4 hover:bg-gray-700 rounded mt-4"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                <?php else: ?>
                    <a href="login.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
                    <a href="register.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-user-plus mr-2"></i>Register</a>
                 <?php endif; ?>
             </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 p-4">
            <!-- Top Navigation (for Notifications) -->
            <nav class="bg-blue-600 p-4 text-white mb-6 rounded-md">
                <div class="container mx-auto flex justify-end items-center">
                <div class="relative">
                        <button id="notificationButton" class="hover:underline relative">
                            <!-- Bell Icon -->
                            <i class="fas fa-bell"></i>
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = :user_id AND is_read = 0");
                            $stmt->bindParam(':user_id', $_SESSION['user_id']);
                            $stmt->execute();
                            $unread = $stmt->fetch(PDO::FETCH_ASSOC)['unread'];
                            if ($unread > 0) : ?>
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
                            <?php if ($notifications) : ?>
                                <?php foreach ($notifications as $notification) : ?>
                                    <a href="view_notification.php?id=<?php echo $notification['id']; ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                        <?php if (!$notification['is_read']) : ?>
                                            <span class="text-xs text-blue-600">New</span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <p class="px-4 py-2 text-gray-600">No notifications.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
            <script>
            document.getElementById('notificationButton').addEventListener('click', function() {
                document.getElementById('notificationDropdown').classList.toggle('hidden');
            });
            </script>
            <!-- Main Content Area -->
             <div class="container mx-auto px-4">