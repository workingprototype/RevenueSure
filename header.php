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
      <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007aff; /* Apple Blue */
            --secondary-color: #4cd964; /* Apple Green */
            --background-light: #f9fafb;
            --background-dark: #1f2937;
            --error-color: #ff3b30;
             --nav-item-height: 48px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: var(--background-light);
            color: #1f2937;
        }
         aside {
            min-width: 280px;
             transition: all 0.3s ease-in-out;
             border-right: 1px solid rgba(0,0,0,0.1);
             background: #e2e8f0
         }
         aside nav {
             display: flex;
             flex-direction: column;
             justify-content: flex-start;
          }
          
          .menu-item a {
           display: flex;
                align-items: center;
            padding: 12px 20px;
              margin: 0 10px;
            transition: color 0.4s ease, background-color 0.4s ease, border-left-color 0.4s ease; /* Slowed down transition */
             border-left: 4px solid transparent;
            color: #4a5568;
             font-size: 0.9rem;
           }
          .menu-item .submenu a:hover {
              background-color: rgba(0,0,0,0.03);
             color: #000;
           }
         .menu-item .submenu a {
              padding: 12px 30px;
             font-size: 0.85rem;
            transition: color 0.2s ease, background-color 0.2s ease;
            }

         .menu-item.active a {
          color: #000;
             background-color: rgba(0,0,0,0.04);
           border-left-color: var(--primary-color);
          }
      .submenu {
            padding-left: 10px;
            transition: all 0.4s ease; /* Slowed down transition */
             margin-top: 5px;
               border-left: 2px solid #94a3b8;
             overflow: hidden;
               max-height: 0;
         }

        .menu-item:hover .submenu {
            max-height: 1000px;
          animation: fadeIn 0.6s ease-in-out; /* Slowed down fade-in animation */
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
             .top-nav-button {
               transition: all 0.3s ease-in-out;
            }
              .top-nav-button:hover {
                    background: rgba(255,255,255,0.1);
             }
                .top-nav-dropdown {
               z-index: 50;
               border: 1px solid rgba(0,0,0,0.07);
                 border-radius: 12px;
                  box-shadow: 0 10px 15px rgba(0,0,0,0.1);
               }
              .top-nav-dropdown a{
                padding: 10px;
                 display: block;
               transition: all 0.2s ease;
               }
                .top-nav-dropdown a:hover{
                   background: #f2f2f2;
                 }
                 .paper-doc {
            font-family: 'Georgia', serif;
            max-width: 800px;
            margin: 20px auto;
             padding: 40px 60px;
            background-color: #fdfdfd;
              border: 1px solid #e2e2e2;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
             border-radius: 15px;
           line-height: 1.7;
              font-size: 16px;
        }
         .paper-doc h1, .paper-doc h2, .paper-doc h3, .paper-doc h4, .paper-doc h5, .paper-doc h6 {
             font-family: 'Roboto Slab', serif;
            margin-bottom: 15px;
              line-height: 1.4;
            color: #333;
              font-weight: 700;

        }
          .paper-doc h1{
               font-size: 2.5rem;

           }
              .paper-doc h2 {
                font-size: 2rem;
                   border-bottom: 2px solid #f2f2f2;
                padding-bottom: 6px;
              }
             .paper-doc h3{
                 font-size: 1.75rem
             }
               .paper-doc h4{
                font-size: 1.5rem;
                  
              }
               .paper-doc h5{
                font-size: 1.25rem;
               }
                .paper-doc h6{
                   font-size: 1.1rem;
             }
        .paper-doc a {
             color: #0056b3;
              text-decoration: none;
                 border-bottom: 1px solid transparent;
             transition: border-bottom 0.3s ease;
          }
      .paper-doc a:hover {
             border-bottom: 1px solid #0056b3;
       }
        .paper-doc p{
            margin-bottom: 15px;
        }
         .paper-doc ol,.paper-doc ul {
            padding-left: 25px;
            margin-bottom: 15px;

        }
        .paper-doc ul li {
            list-style-type: disc;
              margin-bottom: 10px;
        }
        .paper-doc ol li {
             list-style-type: decimal;
              margin-bottom: 10px;
         }
          .paper-doc blockquote {
             margin: 20px 0;
               padding: 15px 20px;
                border-left: 4px solid #c0c0c0;
               font-style: italic;
                color: #555;
                background-color: #fafafa;
          }
          .paper-doc .rating-bookmark-container {
               display: flex;
                justify-content: space-between;
               align-items: center;
               margin-top: 30px;
              border-top: 1px solid #f2f2f2;
                  padding-top: 15px;
          }
          .paper-doc .rating-bookmark-container button {
                transition: all 0.3s ease;
           }
             .paper-doc .rating-bookmark-container button:hover {
                   color: #0056b3;
              }
                 .paper-doc .rating-bookmark-container textarea {
                       margin-top: 5px;
                    }
    </style>
</head>
<body class="bg-gray-100">
   <!-- Navigation Bar -->
    <div class="flex h-screen fade-in-up">
        <!-- Left Sidebar -->
        <aside class="bg-gray-100 text-gray-700 w-64 p-4">
            <a href="index.php" class="text-2xl font-bold block mb-6 pl-4 text-gray-800">RevenueSure</a>
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
                    <a href="dashboard.php" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
                    <a href="your_leads.php" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('your_leads.php') ? 'active' : ''; ?>"><i class="fas fa-user-circle mr-2"></i>Your Leads</a>
                    <a href="search_leads.php" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('search_leads.php') ? 'active' : ''; ?>"><i class="fas fa-search mr-2"></i>Search Leads</a>
                   <a href="view_tasks.php" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('view_tasks.php') ? 'active' : ''; ?>"><i class="fas fa-tasks mr-2"></i>View Tasks</a>
                    <a href="manage_credits.php" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('manage_credits.php') ? 'active' : ''; ?>"><i class="fas fa-credit-card mr-2"></i>Manage Credits</a>

                    <!-- Admin Menu -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <h6 class="text-gray-500 uppercase mt-4 mb-2 px-4">Admin</h6>
                         <div class="menu-item <?php if (isParentActive('add_lead.php') || isParentActive('leads_list.php') || isParentActive('import_leads.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-user-tie mr-2"></i>Manage Leads</a>
                            <div class="submenu">
                                  <a href="add_lead.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_lead.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Lead</a>
                                   <a href="import_leads.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('import_leads.php') ? 'active' : ''; ?>"><i class="fas fa-file-import mr-2"></i>Import Leads</a>
                            </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('add_customer.php') || isParentActive('manage_customers.php') || isParentActive('view_customer.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-user-check mr-2"></i>Manage Customers</a>
                               <div class="submenu">
                                  <a href="add_customer.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_customer.php') ? 'active' : ''; ?>"> <i class="fas fa-plus mr-2"></i> Add Customer</a>
                                     <a href="manage_customers.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_customers.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Customers</a>
                                       <a href="view_customer.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('view_customer.php') ? 'active' : ''; ?>"><i class="fas fa-eye mr-2"></i>Customer Profile</a>
                                </div>
                        </div>
                         <div class="menu-item <?php if (isParentActive('add_employee.php') || isParentActive('manage_employees.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-users mr-2"></i>Manage Employees</a>
                             <div class="submenu">
                                <a href="add_employee.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_employee.php') ? 'active' : ''; ?>"><i class="fas fa-user-plus mr-2"></i>Add Employee</a>
                                <a href="manage_employees.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_employees.php') ? 'active' : ''; ?>"><i class="fas fa-users-cog mr-2"></i>View Employees</a>
                                   </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('manage_categories.php') || isParentActive('add_category.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-list mr-2"></i>Manage Categories</a>
                             <div class="submenu">
                                 <a href="manage_categories.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_categories.php') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>View Categories</a>
                                   <a href="add_category.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_category.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Category</a>
                               </div>
                        </div>
                           <div class="menu-item <?php if (isParentActive('manage_invoices.php') || isParentActive('add_invoice.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-file-invoice-dollar mr-2"></i>Manage Invoices</a>
                            <div class="submenu">
                                <a href="manage_invoices.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_invoices.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Invoices</a>
                                <a href="add_invoice.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_invoice.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Invoice</a>
                            </div>
                        </div>
                         <div class="menu-item <?php if (isParentActive('manage_projects.php') || isParentActive('add_project.php') || isParentActive('view_project.php') || isParentActive('manage_project_categories.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-tasks mr-2"></i>Manage Projects</a>
                            <div class="submenu">
                                <a href="manage_projects.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_projects.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Projects</a>
                                 <a href="add_project.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_project.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Project</a>
                                    <a href="manage_project_categories.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_project_categories.php') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>Project Categories</a>
                            </div>
                         </div>
                       <div class="menu-item <?php if (isParentActive('manage_tickets.php') || isParentActive('add_ticket.php') || isParentActive('view_ticket.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-ticket-alt mr-2"></i>Manage Tickets</a>
                            <div class="submenu">
                                <a href="manage_tickets.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_tickets.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Tickets</a>
                                    <a href="add_ticket.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_ticket.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Ticket</a>
                                </div>
                        </div>
                     <div class="menu-item <?php if (isParentActive('manage_teams.php') || isParentActive('add_team_member.php') || isParentActive('manage_roles.php') || isParentActive('manage_departments.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-users-cog mr-2"></i>Manage Team</a>
                            <div class="submenu">
                                  <a href="manage_teams.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_teams.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Team</a>
                                    <a href="add_team_member.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_team_member.php') ? 'active' : ''; ?>"><i class="fas fa-user-plus mr-2"></i>Add Team Member</a>
                                     <a href="manage_roles.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_roles.php') ? 'active' : ''; ?>"><i class="fas fa-user-tag mr-2"></i>Manage Roles</a>
                                        <a href="manage_departments.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_departments.php') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>Manage Departments</a>
                               </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('manage_knowledge_base.php') || isParentActive('add_knowledge_base_article.php') || isParentActive('view_knowledge_base_article.php')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-book mr-2"></i>Knowledge Base ( KB ) </a>
                            <div class="submenu">
                                 <a href="manage_knowledge_base.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_knowledge_base.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i> View Articles</a>
                                    <a href="add_knowledge_base_article.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_knowledge_base_article.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i> Add Article</a>
                                      <a href="manage_knowledge_base_categories.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_knowledge_base_categories.php') ? 'active' : ''; ?>"> <i class="fas fa-list-alt mr-2"></i>Manage KB Categories</a>
                                      <a href="manage_knowledge_base_requests.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_knowledge_base_requests.php') ? 'active' : ''; ?>"> <i class="fas fa-list-alt mr-2"></i>Knowledge Base Requests</a>
                                    </div>
                        </div>
                       <div class="menu-item <?php if (isParentActive('manage_expenses.php') || isParentActive('add_expense.php')) echo 'active'; ?>">
                         <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-money-bill-wave mr-2"></i>Manage Expenses</a>
                        <div class="submenu">
                                <a href="manage_expenses.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('manage_expenses.php') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Expenses</a>
                                  <a href="add_expense.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('add_expense.php') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Record Expense</a>
                            </div>
                      </div>
                         <a href="reporting_dashboard.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('reporting_dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-chart-bar mr-2"></i>Reporting</a>
                        <a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('settings.php') ? 'active' : ''; ?>"><i class="fas fa-cog mr-2"></i>Settings</a>
                    <?php endif; ?>
                     <a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg mt-4"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                <?php else: ?>
                   <a href="login.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
                     <a href="register.php" class="block py-2 px-4 hover:bg-gray-200 rounded-lg"><i class="fas fa-user-plus mr-2"></i>Register</a>
                 <?php endif; ?>
             </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 p-4 fade-in">
            <!-- Top Navigation (for Notifications) -->
            <nav class="bg-blue-600 p-4 text-white mb-6 rounded-md">
                <div class="container mx-auto flex justify-end items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                       <div class="relative top-nav-button mr-4 hover:bg-white/10 p-1.5 rounded-full transition-colors" >
                            <button id="profileButton" class="relative flex items-center">
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
                             <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg top-nav-dropdown">
                                <a href="profile.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                                <a href="logout.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
                              </div>
                         </div>
                         <div class="relative top-nav-button hover:bg-white/10 p-1.5 rounded-full transition-colors">
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
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border rounded-xl shadow-lg top-nav-dropdown">
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