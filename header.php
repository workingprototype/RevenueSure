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
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
     <style>
        :root {
            --primary-color: #6366f1; /* Indigo */
            --secondary-color: #34d399; /* Teal */
            --background-light: #f9fafb;
            --background-dark: #1f2937;
            --error-color: #ef4444;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-light);
            color: #1f2937;
        }

        .submenu {
            display: none;
            padding-left: 20px;
            transition: all 0.3s ease;
        }
       .menu-item:hover .submenu {
            display: block;
            animation: fadeIn 0.3s ease-in-out; /* Apply the fade-in animation on hover */
        }
        .menu-item a {
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        .menu-item a:hover {
           background-color: #434f5c;
           color: white;
        }
         .active {
           background-color: #434f5c;
        }
          aside {
            min-width: 256px;
             transition: all 0.3s ease-in-out;
        }
         button {
              transition: background-color 0.3s ease, color 0.3s ease, transform 0.1s ease;
              }
              button:active {
              transform: scale(0.98);
              }
         input:focus, select:focus, textarea:focus {
               border-color: var(--primary-color);
               box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
            }
           .bg-white {
             transition: all 0.3s ease-in-out;
             box-shadow: 0 2px 5px rgba(0,0,0,0.1);
             }
         input,select, textarea {
          transition: all 0.3s ease-in-out;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 8px;

           }
             .fade-in-up {
            animation: fadeInUp 0.3s ease-in-out; /* Apply the fade-in-up animation on load */
            }
            .fade-in {
                animation: fadeIn 0.3s ease-in-out; /* Apply the fade-in animation on load */
            }
           @keyframes fadeIn {
            from {
                opacity: 0;
             }
             to {
                opacity: 1;
            }
          }
        @keyframes fadeInUp {
          from {
            opacity: 0;
             transform: translateY(20px);
           }
          to {
             opacity: 1;
             transform: translateY(0);
            }
        }

        /* Typography */
        h1 {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        h3 {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        p {
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: darken(var(--primary-color), 10%);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: darken(var(--secondary-color), 10%);
        }

        /* Shadows and Elevation */
        .shadow-sm {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .shadow-md {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        /* Spacing */
        .spacing-xs {
            margin: 8px;
        }

        .spacing-sm {
            margin: 16px;
        }

        .spacing-md {
            margin: 24px;
        }

        .spacing-lg {
            margin: 32px;
        }

        /* Accessibility */
        a:focus, button:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
         #profileDropdown {
            transition: all 0.3s ease-in-out;
           }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <div class="flex h-screen fade-in-up">
        <!-- Left Sidebar -->
        <aside class="bg-gray-800 text-white w-64 p-4">
            <a href="index.php" class="text-2xl font-bold block mb-6">RevenueSure</a>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User Menu -->
                    <?php
                    // Function to check if the current page path is or contains the given path
                     function isActive($path) {
                         $current_page = basename($_SERVER['PHP_SELF']);
                         return strpos($current_page, $path) === 0;
                    }

                     function isParentActive($path) {
                         $current_page = basename($_SERVER['PHP_SELF']);
                       return strpos($current_page, $path) !== false;
                      }
                    ?>
                    <a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
                    <a href="your_leads.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('your_leads.php') ? 'active' : ''; ?>"><i class="fas fa-user-circle mr-2"></i>Your Leads</a>
                    <a href="search_leads.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('search_leads.php') ? 'active' : ''; ?>"><i class="fas fa-search mr-2"></i>Search Leads</a>
                   <a href="view_tasks.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('view_tasks.php') ? 'active' : ''; ?>"><i class="fas fa-tasks mr-2"></i>View Tasks</a>
                    <a href="manage_credits.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('manage_credits.php') ? 'active' : ''; ?>"><i class="fas fa-credit-card mr-2"></i>Manage Credits</a>

                    <!-- Admin Menu -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <h6 class="text-gray-500 uppercase mt-4 mb-2 px-4">Admin</h6>
                         <div class="menu-item <?php if (isParentActive('add_lead.php') || isParentActive('leads_list.php') || isParentActive('import_leads.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-user-tie mr-2"></i>Manage Leads</a>
                            <div class="submenu">
                                  <a href="add_lead.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('add_lead.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Lead</a>
                                  <a href="leads_list.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('leads_list.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Leads</a>
                                   <a href="import_leads.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('import_leads.php') ? 'active' : ''; ?>"><i class="fas fa-file-import mr-2"></i>Import Leads</a>
                            </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('add_customer.php') || isParentActive('manage_customers.php') || isParentActive('view_customer.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-user-check mr-2"></i>Manage Customers</a>
                               <div class="submenu">
                                  <a href="add_customer.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('add_customer.php') ? 'active' : ''; ?>"> <i class="fas fa-plus mr-2"></i> Add Customer</a>
                                     <a href="manage_customers.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('manage_customers.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Customers</a>
                                       <a href="view_customer.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isParentActive('view_customer.php') ? 'active' : ''; ?>"><i class="fas fa-eye mr-2"></i>Customer Profile</a>
                                </div>
                        </div>
                         <div class="menu-item <?php if (isParentActive('add_employee.php') || isParentActive('manage_employees.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-users mr-2"></i>Manage Employees</a>
                             <div class="submenu">
                                <a href="add_employee.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('add_employee.php') ? 'active' : ''; ?>"><i class="fas fa-user-plus mr-2"></i>Add Employee</a>
                                <a href="manage_employees.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('manage_employees.php') ? 'active' : ''; ?>"><i class="fas fa-users-cog mr-2"></i>View Employees</a>
                                   </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('manage_categories.php') || isParentActive('add_category.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-list mr-2"></i>Manage Categories</a>
                             <div class="submenu">
                                 <a href="manage_categories.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('manage_categories.php') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>View Categories</a>
                                   <a href="add_category.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('add_category.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Category</a>
                               </div>
                        </div>
                           <div class="menu-item <?php if (isParentActive('manage_invoices.php') || isParentActive('add_invoice.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-700 rounded flex items-center"><i class="fas fa-file-invoice-dollar mr-2"></i>Manage Invoices</a>
                            <div class="submenu">
                                <a href="manage_invoices.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('manage_invoices.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Invoices</a>
                                <a href="add_invoice.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('add_invoice.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Invoice</a>
                            </div>
                        </div>
                         <a href="reporting_dashboard.php" class="block py-2 px-4 hover:bg-gray-700 rounded <?php echo isActive('reporting_dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-chart-bar mr-2"></i>Reporting</a>
                    <?php endif; ?>
                     <a href="logout.php" class="block py-2 px-4 hover:bg-gray-700 rounded mt-4"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                <?php else: ?>
                    <a href="login.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
                    <a href="register.php" class="block py-2 px-4 hover:bg-gray-700 rounded"><i class="fas fa-user-plus mr-2"></i>Register</a>
                 <?php endif; ?>
             </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 p-4 fade-in">
            <!-- Top Navigation (for Notifications) -->
            <nav class="bg-blue-600 p-4 text-white mb-6 rounded-md">
                <div class="container mx-auto flex justify-end items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative mr-4">
                            <button id="profileButton" class="hover:underline relative flex items-center">
                                <?php
                                $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
                                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                $stmt->execute();
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                $profile_picture = $user['profile_picture'];

                                if ($profile_picture) : ?>
                                    <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="rounded-full w-8 h-8 object-cover">
                                <?php else : ?>
                                    <i class="fas fa-user-circle fa-lg"></i>
                                <?php endif; ?>
                            </button>
                             <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg">
                                <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                                <a href="logout.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
                              </div>
                         </div>
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
                   <?php endif; ?>
                </div>
            </nav>
            <script>
                document.getElementById('notificationButton').addEventListener('click', function() {
                    document.getElementById('notificationDropdown').classList.toggle('hidden');
                });
                 document.getElementById('profileButton').addEventListener('click', function() {
                    document.getElementById('profileDropdown').classList.toggle('hidden');
                 });
            </script>
            <!-- Main Content Area -->
             <div class="container mx-auto px-4">