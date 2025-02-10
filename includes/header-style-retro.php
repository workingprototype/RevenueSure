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
        /* Retro-Inspired Styles */
        body {
            font-family: monospace;
            background-color: #000; /* Dark background */
            color: #0f0; /* Green text */
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Prevent horizontal scrollbar */
        }

        /* CRT Screen Effect */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1); /* Subtle scanlines */
            z-index: 1000;
            pointer-events: none;
             opacity: 0.7;
        }

        /* Top Navigation Bar */
        nav.bg-blue-600 {
            background-color: #222;
            border-bottom: 2px solid #0f0;
            padding: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,255,0,0.2);
            position: relative;
            z-index: 10;
        }

        nav a, nav button {
            color: #0f0;
            text-shadow: 0 0 5px #0f0;
            transition: color 0.3s ease;
        }

        nav a:hover, nav button:hover {
            color: #fff;
            text-shadow: 0 0 10px #fff;
        }

        .top-nav-dropdown {
            background-color: #333;
            border: 1px solid #0f0;
            box-shadow: 0 5px 10px rgba(0,255,0,0.3);
            border-radius: 0;
            padding: 0.5rem 0;
        }

        .top-nav-dropdown a {
            padding: 0.5rem 1rem;
            display: block;
            transition: background-color 0.2s ease;
        }

        .top-nav-dropdown a:hover {
            background-color: #444;
        }

        /* Left Sidebar */
        aside {
            background-color: #111;
            border-right: 2px solid #0f0;
            min-width: 200px;
            padding: 1rem;
             transition: all 0.3s ease-in-out;
        }

        aside a {
            color: #0f0;
            display: block;
            padding: 0.5rem 0.75rem;
            margin: 0.2rem 0;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
             text-shadow: 0 0 3px #0f0;
        }

        aside a:hover, aside a.active {
            background-color: #333;
            border-left-color: #0f0;
            color: #fff;
             text-shadow: 0 0 5px #fff;
        }

        aside .menu-item.active a {
            color: #fff;
            background-color: #333;
            border-left-color: #0f0;
        }

        /* Submenu */
        .submenu {
            padding-left: 1.5rem;
            margin-top: 0.3rem;
             border-left: 2px dotted #0f0;
             overflow: hidden;
             max-height: 0;
              transition: max-height 0.3s ease-out;
        }

        .menu-item:hover .submenu {
           max-height: 500px; /* Adjust as needed */
            transition: max-height 0.5s ease-in;

        }

        .submenu a {
            padding: 0.4rem 0.5rem;
             font-size: 0.9em;
        }

          /* Logo */
        aside a.text-2xl {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
             margin-bottom: 1rem;
             display: block;
             text-shadow: 0 0 8px #0f0;
        }

        /* Main Content Area */
        .flex-1 {
            padding: 1rem;
            color: #0f0;
            overflow: auto; /* Enable scrolling if content overflows */
        }

        /* Buttons */
        button {
            background-color: #333;
            color: #0f0;
            border: 1px solid #0f0;
            padding: 0.5rem 1rem;
            border-radius: 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #0f0;
            color: #000;
            box-shadow: 0 0 5px #0f0;
        }

        /* Inputs */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            background-color: #111;
            color: #0f0;
            border: 1px solid #0f0;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 0;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 5px #0f0;
            border-color: #0f0;
        }

         /* Paper Doc Styles */
        .paper-doc {
            font-family: monospace;
            max-width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #111;
            border: 1px solid #0f0;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
            border-radius: 0;
            line-height: 1.6;
            font-size: 1rem;
            color: #0f0;
        }

        .paper-doc h1, .paper-doc h2, .paper-doc h3, .paper-doc h4, .paper-doc h5, .paper-doc h6 {
            font-family: monospace;
            color: #0f0;
            text-shadow: 0 0 5px #0f0;
            margin-bottom: 1rem;
        }

        .paper-doc h1 {
            font-size: 2rem;
        }

        .paper-doc h2 {
            font-size: 1.75rem;
            border-bottom: 1px solid #0f0;
            padding-bottom: 0.5rem;
        }

        .paper-doc a {
            color: #0f0;
            text-decoration: none;
            border-bottom: 1px dotted #0f0;
            transition: border-bottom 0.3s ease;
        }

        .paper-doc a:hover {
            border-bottom: 1px solid #0f0;
        }

        .paper-doc p {
            margin-bottom: 1rem;
        }

        .paper-doc ul, .paper-doc ol {
            padding-left: 2rem;
            margin-bottom: 1rem;
        }

        .paper-doc ul li {
            list-style-type: square;
            margin-bottom: 0.5rem;
        }

        .paper-doc ol li {
            list-style-type: decimal;
            margin-bottom: 0.5rem;
        }

        .paper-doc blockquote {
            margin: 1rem 0;
            padding: 1rem;
            border-left: 4px solid #0f0;
            font-style: italic;
            background-color: #222;
        }

        .paper-doc .rating-bookmark-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            border-top: 1px solid #0f0;
            padding-top: 1rem;
        }

        .paper-doc .rating-bookmark-container button {
            background-color: transparent;
            border: 1px solid #0f0;
            color: #0f0;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .paper-doc .rating-bookmark-container button:hover {
            background-color: #0f0;
            color: #000;
        }

        .paper-doc .rating-bookmark-container textarea {
            background-color: #111;
            color: #0f0;
            border: 1px solid #0f0;
            padding: 0.5rem;
            margin-top: 0.5rem;
            width: 100%;
        }

        /* Utility Classes (Retro-fied) */
        .bg-gray-100 { background-color: #111; }
        .text-gray-700 { color: #0f0; }
        .text-gray-800 { color: #0f0; }
        .text-gray-500 { color: #777; }
        .hover\:bg-gray-200:hover { background-color: #333; }
        .rounded-lg { border-radius: 0; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1rem; }
         .mt-6 { margin-top: 1.5rem; }
        .mr-2 { margin-right: 0.5rem; }
        .pl-4 { padding-left: 1rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .uppercase { text-transform: uppercase; }
        .block { display: block; }
         .items-center { align-items: center; }
          .flex { display: flex; }
           .container {
               width: 100%;
               max-width: 1200px;
               margin: 0 auto;
             }
            .mx-auto { margin-left: auto; margin-right: auto; }
           .justify-end { justify-content: flex-end; }
              .relative { position: relative; }
            .absolute { position: absolute; }
            .right-0 { right: 0; }
            .mt-2 { margin-top: 0.5rem; }
             .w-48 { width: 12rem; }
            .bg-white { background-color: #222; }
               .shadow-lg { box-shadow: 0 5px 10px rgba(0,255,0,0.3); }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
             .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                .text-gray-800 { color: #0f0; }
                 .hover\:bg-gray-100:hover { background-color: #333; }
            .text-xs { font-size: 0.75rem; }
              .text-blue-600 { color: #0f0; }
               .bg-red-500 { background-color: red; }
               .text-white { color: #000; }
            .rounded-full { border-radius: 0; }
           .transition-colors { transition: background-color 0.3s ease; }
            .p-1\.5 { padding: 0.375rem; }
               .flex { display: flex; }
                .items-center { align-items: center; }
                .object-cover { object-fit: cover; }
                  .w-8 { width: 2rem; }
                .h-8 { height: 2rem; }
                 .mr-4 { margin-right: 1rem; }
                .p-4 { padding: 1rem; }
                .mb-6 { margin-bottom: 1.5rem; }
                  .top-nav-button:hover {
                      background: rgba(0,255,0,0.1);
                 }
                 .hidden { display: none; }
                 .fa-lg { font-size: 1.33333em; line-height: 0.75em; vertical-align: -15%; }
                 /* Animation Keyframes */
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
                .fade-in-up {
                    animation: fadeInUp 0.3s ease-in-out; /* Apply the fade-in-up animation on load */
                }
                .fade-in {
                     animation: fadeIn 0.3s ease-in-out; /* Apply the fade-in animation on load */
                }

        /* Add more retro-specific styles as needed */
    </style>
</head>
<body class="bg-gray-100">
   <!-- Navigation Bar -->
    <div class="flex h-screen fade-in-up">
        <!-- Left Sidebar -->
        <aside class="text-gray-700 w-64 p-4">
            <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold block mb-6 pl-4 text-gray-800">RevenueSure</a>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User Menu -->
                    <?php
                    // Function to check if the current page path is or contains the given path
                     function isActive($path) {
                         $current_page = $_GET['route'] ?? ''; // Use $_GET['route'] here
                         return strpos($current_page, $path) === 0;
                    }
                     function isParentActive($path) {
                         $current_page = $_GET['route'] ?? ''; // Use $_GET['route'] here
                       return strpos($current_page, $path) !== false;
                      }
                    ?>
                    <a href="<?php echo BASE_URL; ?>dashboard" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('dashboard') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>leads/yourleads" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('leads/yourleads') ? 'active' : ''; ?>"><i class="fas fa-user-circle mr-2"></i>Your Leads</a>
                    <a href="<?php echo BASE_URL; ?>leads/search" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('leads/search') ? 'active' : ''; ?>"><i class="fas fa-search mr-2"></i>Search Leads</a>
                   <a href="<?php echo BASE_URL; ?>tasks/viewtasks" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('tasks/view') ? 'active' : ''; ?>"><i class="fas fa-tasks mr-2"></i>View Tasks</a>
                    <a href="<?php echo BASE_URL; ?>credits/manage" class="menu-item block hover:bg-gray-200 rounded-lg px-4 py-3 <?php echo isActive('credits/manage') ? 'active' : ''; ?>"><i class="fas fa-credit-card mr-2"></i>Manage Credits</a>

                    <!-- Admin Menu -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <h6 class="text-gray-500 uppercase mt-4 mb-2 px-4">Admin</h6>
                         <div class="menu-item <?php if (isParentActive('leads/add') || isParentActive('leads/manage') || isParentActive('leads/import')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-user-tie mr-2"></i>Manage Leads</a>
                            <div class="submenu">
                                  <a href="<?php echo BASE_URL; ?>leads/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('leads/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Lead</a>
                                   <a href="<?php echo BASE_URL; ?>leads/import" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('leads/import') ? 'active' : ''; ?>"><i class="fas fa-file-import mr-2"></i>Import Leads</a>
                            </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('customers/add') || isParentActive('customers/manage') || isParentActive('customers/view')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-user-check mr-2"></i>Manage Customers</a>
                               <div class="submenu">
                                  <a href="<?php echo BASE_URL; ?>customers/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('customers/add') ? 'active' : ''; ?>"> <i class="fas fa-plus mr-2"></i> Add Customer</a>
                                     <a href="<?php echo BASE_URL; ?>customers/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('customers/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Customers</a>
                                     <a href="<?php echo BASE_URL; ?>customers/view" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('customers/view') ? 'active' : ''; ?>"><i class="fas fa-eye mr-2"></i>Customer Profile</a>
                                </div>
                        </div>
                         <div class="menu-item <?php if (isParentActive('employees/add') || isParentActive('employees/manage')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-users mr-2"></i>Manage Employees</a>
                             <div class="submenu">
                                <a href="<?php echo BASE_URL; ?>employees/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('employees/add') ? 'active' : ''; ?>"><i class="fas fa-user-plus mr-2"></i>Add Employee</a>
                                <a href="<?php echo BASE_URL; ?>employees/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('employees/manage') ? 'active' : ''; ?>"><i class="fas fa-users-cog mr-2"></i>View Employees</a>
                                   </div>
                        </div>
                          <div class="menu-item <?php if (isParentActive('categories/manage') || isParentActive('categories/add')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-list mr-2"></i>Manage Categories</a>
                             <div class="submenu">
                                 <a href="<?php echo BASE_URL; ?>categories/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('categories/manage') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>View Categories</a>
                                   <a href="<?php echo BASE_URL; ?>categories/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('categories/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Category</a>
                               </div>
                        </div>
                           <div class="menu-item <?php if (isParentActive('invoices/manage') || isParentActive('invoices/add') || isParentActive('invoices/view')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-file-invoice-dollar mr-2"></i>Manage Invoices</a>
                            <div class="submenu">
                                <a href="<?php echo BASE_URL; ?>invoices/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('invoices/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Invoices</a>
                                <a href="<?php echo BASE_URL; ?>invoices/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('invoices/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Invoice</a>
                                 <a href="<?php echo BASE_URL; ?>invoices/view" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('invoices/view') ? 'active' : ''; ?>"><i class="fas fa-eye mr-2"></i>Invoice Details</a>
                            </div>
                        </div>
                            <div class="menu-item <?php if (isParentActive('projects/manage') || isParentActive('projects/add') || isParentActive('projects/view') || isParentActive('projects/categories/manage')) echo 'active'; ?>">
                               <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-tasks mr-2"></i>Manage Projects</a>
                            <div class="submenu">
                                <a href="<?php echo BASE_URL; ?>projects/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('projects/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Projects</a>
                                    <a href="<?php echo BASE_URL; ?>discussions/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('discussions/manage') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Discussions</a>
                                 <a href="<?php echo BASE_URL; ?>projects/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('projects/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Project</a>
                                      <a href="<?php echo BASE_URL; ?>projects/categories/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('projects/categories/manage') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>Project Categories</a>
                            </div>
                        </div>
                       <div class="menu-item <?php if (isParentActive('support_tickets/manage') || isParentActive('support_tickets/add') || isParentActive('support_tickets/view')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-ticket-alt mr-2"></i>Manage Tickets</a>
                            <div class="submenu">
                                <a href="<?php echo BASE_URL; ?>support_tickets/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('support_tickets/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Tickets</a>
                                    <a href="<?php echo BASE_URL; ?>support_tickets/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('support_tickets/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Add Ticket</a>
                            </div>
                        </div>
                      <div class="menu-item <?php if (isParentActive('team/manage') || isParentActive('team/add') || isParentActive('team/edit')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-users-cog mr-2"></i>Manage Team</a>
                            <div class="submenu">
                                  <a href="<?php echo BASE_URL; ?>team/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('team/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Team</a>
                                  <a href="<?php echo BASE_URL; ?>team/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('team/add') ? 'active' : ''; ?>"><i class="fas fa-user-plus mr-2"></i>Add Team Member</a>
                                  <a href="<?php echo BASE_URL; ?>team/roles/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('team/roles/manage') ? 'active' : ''; ?>"><i class="fas fa-user-tag mr-2"></i>Manage Roles</a>
                                    <a href="<?php echo BASE_URL; ?>team/departments/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('team/departments/manage') ? 'active' : ''; ?>"><i class="fas fa-list-alt mr-2"></i>Manage Departments</a>
                               </div>
                        </div>
                         <div class="menu-item <?php if (isParentActive('knowledge_base/manage') || isParentActive('knowledge_base/add') || isParentActive('knowledge_base/view')) echo 'active'; ?>">
                            <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-book mr-2"></i>Knowledge Base ( KB ) </a>
                            <div class="submenu">
                                 <a href="<?php echo BASE_URL; ?>knowledge_base/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('knowledge_base/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i> View Articles</a>
                                    <a href="<?php echo BASE_URL; ?>knowledge_base/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('knowledge_base/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i> Add Article</a>
                                      <a href="<?php echo BASE_URL; ?>knowledge_base/categories/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('knowledge_base/categories/manage') ? 'active' : ''; ?>"> <i class="fas fa-list-alt mr-2"></i>Manage KB Categories</a>
                                    <a href="<?php echo BASE_URL; ?>knowledge_base/request/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isParentActive('knowledge_base/request/manage') ? 'active' : ''; ?>"> <i class="fas fa-list-alt mr-2"></i>Knowledge Base Requests</a>
                                </div>
                         </div>
                           <div class="menu-item <?php if (isParentActive('expenses/manage') || isParentActive('expenses/add')) echo 'active'; ?>">
                             <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-money-bill-wave mr-2"></i>Manage Expenses</a>
                            <div class="submenu">
                                <a href="<?php echo BASE_URL; ?>expenses/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('expenses/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Expenses</a>
                                   <a href="<?php echo BASE_URL; ?>expenses/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('expenses/add') ? 'active' : ''; ?>"><i class="fas fa-plus mr-2"></i>Record Expense</a>
                            </div>
                      </div>
                            <div class="menu-item <?php if (isParentActive('contracts/manage') || isParentActive('contracts/add')) echo 'active'; ?>">
                                   <a class="block py-2 px-4 hover:bg-gray-200 rounded-lg flex items-center"><i class="fas fa-file-contract mr-2"></i>Manage Contracts</a>
                                 <div class="submenu">
                                        <a href="<?php echo BASE_URL; ?>contracts/manage" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('contracts/manage') ? 'active' : ''; ?>"><i class="fas fa-list-ul mr-2"></i>View Contracts</a>
                                       <a href="<?php echo BASE_URL; ?>contracts/add" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('contracts/add') ? 'active' : ''; ?>"> <i class="fas fa-plus mr-2"></i> Create Contract</a>
                                 </div>
                           </div>
                         <a href="<?php echo BASE_URL; ?>reports/leads/dashboard" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('reports/leads/dashboard') ? 'active' : ''; ?>"><i class="fas fa-chart-bar mr-2"></i>Reporting</a>
                        <a href="<?php echo BASE_URL; ?>settings" class="block py-2 px-4 hover:bg-gray-200 rounded-lg <?php echo isActive('settings') ? 'active' : ''; ?>"><i class="fas fa-cog mr-2"></i>Settings</a>
                    <?php endif; ?>
                     <a href="<?php echo BASE_URL; ?>auth/logout" class="block py-2 px-4 hover:bg-gray-200 rounded-lg mt-4"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                <?php else: ?>
                   <a href="<?php echo BASE_URL; ?>auth/login" class="block py-2 px-4 hover:bg-gray-200 rounded-lg"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
                     <a href="<?php echo BASE_URL; ?>auth/register" class="block py-2 px-4 hover:bg-gray-200 rounded-lg"><i class="fas fa-user-plus mr-2"></i>Register</a>
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
                                <a href="<?php echo BASE_URL; ?>profile/view" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                                <a href="<?php echo BASE_URL; ?>auth/logout" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
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
                                        <a href="notifications/view?id=<?php echo $notification['id']; ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
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