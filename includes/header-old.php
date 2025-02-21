<?php
require_once __DIR__ . '/../helper/core.php'; // Include core functions
require_once __DIR__ . '/../helper/cache.php'; // Include cache functions

$cacheKey = 'header_' . (isset($_SESSION['user_id']) ? 'user_' . $_SESSION['user_id'] : 'anonymous');

$cacheExpiration = 3600; // Cache for 1 hour

if (ENABLE_CACHE && isCacheValid($cacheKey, $cacheExpiration)) {
    echo getCache($cacheKey);
} else {
    ob_start(); // Start output buffering
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RevenueSure</title>
  <meta name="description" content="A platform to manage leads and businesses efficiently." />
  <meta name="keywords" content="leads, businesses, management, platform" />
  <meta name="author" content="Your Name" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

  <!-- Include FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet" /> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/1.1.1/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
:root {
  --primary-color: #6200ea; /* Deep Purple */
  --secondary-color: #03dac6; /* Cyan */
  --background-light: #f5f5f5;
  --background-dark: #121212;
  --error-color: #b00020;
  --nav-item-height: 56px;
}

body {
  font-family: 'Inter', sans-serif;
  background-color: var(--background-light);
  color: #333;
  margin: 0;
  padding: 0;
}

aside {
  min-width: 300px;
  transition: all 0.3s ease-in-out;
  border-right: 1px solid rgba(0, 0, 0, 0.1);
  background: #fff;
}

aside nav {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.menu-item a {
  display: flex;
  align-items: center;
  padding: 16px 24px;
  margin: 0 12px;
  transition: color 0.4s ease, background-color 0.4s ease, border-left-color 0.4s ease;
  border-left: 4px solid transparent;
  color: #666;
  font-size: 1rem;
}

.menu-item .submenu a:hover {
  background-color: rgba(0, 0, 0, 0.05);
  color: #000;
}

.menu-item .submenu a {
  padding: 14px 36px;
  font-size: 0.9rem;
  transition: color 0.2s ease, background-color 0.2s ease;
}

.menu-item.active > a {
  color: #000;
  background-color: rgba(0, 0, 0, 0.06);
  border-left-color: var(--primary-color);
}

.submenu a.active {
  color: #000;
  background-color: rgba(0, 0, 0, 0.06);
  border-left: 4px solid var(--primary-color);
}

.submenu {
  padding-left: 12px;
  transition: max-height 0.4s ease;
  margin-top: 8px;
  border-left: 2px solid #ccc;
  overflow: hidden;
  max-height: 0;
}

.menu-item:hover .submenu,
.menu-item.active .submenu {
  max-height: 1000px;
  animation: fadeIn 0.6s ease-in-out;
}

.fade-in-up {
  animation: fadeInUp 0.3s ease-in-out;
}

.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
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
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

input, select, textarea {
  transition: all 0.3s ease-in-out;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.top-nav-button {
  transition: all 0.3s ease-in-out;
}

.top-nav-button:hover {
  background: rgba(255, 255, 255, 0.1);
}

.top-nav-dropdown {
  z-index: 50;
  border: 1px solid rgba(0, 0, 0, 0.07);
  border-radius: 12px;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.top-nav-dropdown a {
  padding: 12px;
  display: block;
  transition: all 0.2s ease;
}

.top-nav-dropdown a:hover {
  background: #f2f2f2;
}

.paper-doc {
  font-family: 'Georgia', serif;
  max-width: 800px;
  margin: 20px auto;
  padding: 40px 60px;
  background-color: #fff;
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

.paper-doc h1 {
  font-size: 2.5rem;
}

.paper-doc h2 {
  font-size: 2rem;
  border-bottom: 2px solid #f2f2f2;
  padding-bottom: 6px;
}

.paper-doc h3 {
  font-size: 1.75rem;
}

.paper-doc h4 {
  font-size: 1.5rem;
}

.paper-doc h5 {
  font-size: 1.25rem;
}

.paper-doc h6 {
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

.paper-doc p {
  margin-bottom: 15px;
}

.paper-doc ol, .paper-doc ul {
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
  <!-- Mobile Top Navigation (shows hamburger) -->
  <nav class="bg-black p-4 text-white md:hidden flex justify-between items-center border-b-4 border-white">
    <a href="<?php echo BASE_URL; ?>" class="text-xl font-bold uppercase py-2">RevenueSure</a>
    <button id="mobileMenuButton" class="p-2 focus:outline-none">
      <i class="fa-solid fa-bars fa-lg"></i>
    </button>
  </nav>

  <!-- Main Container -->
  <div class="flex h-screen">
    <!-- Sidebar: hidden on mobile, shown on md+ -->
    <aside id="sidebar" class="bg-gray-100 text-gray-700 w-64 p-4 hidden md:block border-r-4 border-black">
    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold block mb-4 pt-4 pl-4 text-gray-800 uppercase" id="headerLogo">RevenueSure</a>
      <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- User Menu -->
          <?php
          function isActive($path) {
              $current_page = $_GET['route'] ?? '';
              return strpos($current_page, $path) === 0;
          }
          function isParentActive($path) {
              $current_page = $_GET['route'] ?? '';
              return strpos($current_page, $path) !== false;
          }
          ?>
          <a href="<?php echo BASE_URL; ?>dashboard" class="menu-item block hover: px-4 py-3 <?php echo isActive('dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house mr-2"></i>Dashboard
          </a>
          <a href="<?php echo BASE_URL; ?>credits/manage" class="menu-item block hover: px-4 py-3 <?php echo isActive('credits/manage') ? 'active' : ''; ?>">
            <i class="fa-solid fa-wallet mr-2"></i>Manage Credits
          </a>
          <a href="<?php echo BASE_URL; ?>notes/manage" class="block py-2 px-4 hover: px-4 py-3 <?php echo isActive('notes/manage') ? 'active' : ''; ?>">
            <i class="fa-solid fa-sticky-note mr-2"></i>Note Taking
          </a>

          <!-- Admin Menu -->
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <h6 class="text-gray-500 uppercase mt-4 mb-2 px-4">Admin</h6>
            <div class="menu-item <?php if (isParentActive('leads/add') || isParentActive('leads/manage') || isParentActive('leads/import') || isParentActive('leads/yourleads')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-address-book mr-2"></i>Manage Leads
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>leads/manage" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('leads/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-circle mr-2"></i>Manage Leads
                </a>
                <a href="<?php echo BASE_URL; ?>leads/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('leads/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Lead
                </a>
                <a href="<?php echo BASE_URL; ?>leads/yourleads" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('leads/yourleads') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-circle mr-2"></i>Your Leads
                </a>
                <a href="<?php echo BASE_URL; ?>leads/search" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('leads/search') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-search mr-2"></i>Search Leads
                </a>
                <a href="<?php echo BASE_URL; ?>leads/import" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('leads/import') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-file-import mr-2"></i>Import Leads
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('customers/add') || isParentActive('customers/manage') || isParentActive('customers/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-user-friends mr-2"></i>Manage Customers
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>customers/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('customers/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i> Add Customer
                </a>
                <a href="<?php echo BASE_URL; ?>customers/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('customers/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Customers
                </a>
                <a href="<?php echo BASE_URL; ?>customers/view" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('customers/view') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-eye mr-2"></i>Customer Profile
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('employees/add') || isParentActive('employees/manage')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-user-tie mr-2"></i>Manage Employees
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>employees/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('employees/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-plus mr-2"></i>Add Employee
                </a>
                <a href="<?php echo BASE_URL; ?>employees/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('employees/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-users-cog mr-2"></i>View Employees
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('categories/manage') || isParentActive('categories/add')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-th-large mr-2"></i>Manage Categories
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>categories/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('categories/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>View Categories
                </a>
                <a href="<?php echo BASE_URL; ?>categories/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('categories/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Category
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('invoices/manage') || isParentActive('invoices/add') || isParentActive('invoices/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-file-invoice mr-2"></i>Manage Invoices
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>invoices/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('invoices/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Invoices
                </a>
                <a href="<?php echo BASE_URL; ?>invoices/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('invoices/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Invoice
                </a>
                <a href="<?php echo BASE_URL; ?>invoices/view" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('invoices/view') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-eye mr-2"></i>Invoice Details
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('projects/manage') || isParentActive('projects/add') || isParentActive('projects/view') || isParentActive('projects/categories/manage') || isParentActive('discussions/manage') || isParentActive('tasks/viewtasks')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-clipboard-check mr-2"></i>Manage Projects & Tasks
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>projects/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>Projects
                </a>
                 <a href="<?php echo BASE_URL; ?>tasks/viewtasks" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('tasks/viewtasks') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-tasks mr-2"></i>Tasks
                </a>
                <a href="<?php echo BASE_URL; ?>discussions/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('discussions/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Discussions
                </a>
                <a href="<?php echo BASE_URL; ?>projects/features/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/features/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Features Tracker
                </a>
                <a href="<?php echo BASE_URL; ?>projects/issues/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/issues/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Issue Tracker
                </a>
                <a href="<?php echo BASE_URL; ?>projects/categories/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/categories/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>Project Categories
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('contracts/manage') || isParentActive('contracts/add')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-file-signature mr-2"></i>Manage Contracts
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>contracts/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('contracts/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Contracts
                </a>
                <a href="<?php echo BASE_URL; ?>contracts/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('contracts/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i> Create Contract
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('support_tickets/manage') || isParentActive('support_tickets/add') || isParentActive('support_tickets/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-headset mr-2"></i>Support Tickets
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>support_tickets/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('support_tickets/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>Manage Tickets
                </a>
                <a href="<?php echo BASE_URL; ?>support_tickets/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('support_tickets/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Ticket
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('team/manage') || isParentActive('team/add') || isParentActive('team/edit')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
              <i class="fa-solid fa-user-friends mr-2"></i>Manage Team
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>team/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('team/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Team
                </a>
                <a href="<?php echo BASE_URL; ?>team/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('team/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-plus mr-2"></i>Add Team Member
                </a>
                <a href="<?php echo BASE_URL; ?>team/roles/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('team/roles/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-tag mr-2"></i>Manage Roles
                </a>
                <a href="<?php echo BASE_URL; ?>departments/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('departments/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>Manage Departments
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('knowledge_base/manage') || isParentActive('knowledge_base/add') || isParentActive('knowledge_base/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-book-open mr-2"></i>Knowledge Base
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>knowledge_base/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('knowledge_base/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i> View Articles
                </a>
                <a href="<?php echo BASE_URL; ?>knowledge_base/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('knowledge_base/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i> Add Article
                </a>
                <a href="<?php echo BASE_URL; ?>knowledge_base/categories/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('knowledge_base/categories/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>Manage KB Categories
                </a>
                <a href="<?php echo BASE_URL; ?>knowledge_base/request/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('knowledge_base/request/manage') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list-alt mr-2"></i>Knowledge Base Requests
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('expenses/manage') || isParentActive('expenses/add')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-receipt mr-2"></i>Manage Expenses
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>expenses/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('expenses/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Expenses
                </a>
                <a href="<?php echo BASE_URL; ?>expenses/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('expenses/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Record Expense
                </a>
              </div>
            </div>
            <!-- User Mailbox-->
            <div class="menu-item <?php if (isParentActive('mail/index') || isParentActive('mail/compose')) echo 'active'; ?>">
                <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                  <i class="fa-solid fa-file-signature mr-2"></i>Mailbox
                </a>
                <div class="submenu">
                  <a href="<?php echo BASE_URL; ?>mail/index" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('mail/compose') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list-ul mr-2"></i>Inbox
                  </a>
                  <a href="<?php echo BASE_URL; ?>mail/compose" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('mail/compose') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus mr-2"></i> Compose
                  </a>
                  </a>
                  <a href="<?php echo BASE_URL; ?>mail/settings" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('mail/settings') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus mr-2"></i> E-mail Settings
                  </a>
                </div>
              </div>

              <!-- Accounting Menu -->
            <div class="menu-item <?php if (isParentActive('accounting/dashboard') || isParentActive('accounting/ledger') || isParentActive('accounting/reconciliation') || isParentActive('accounting/manage_accountants')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-calculator mr-2"></i>Accounting
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>accounting/dashboard" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/dashboard') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>accounting/ledger" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/ledger') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-book-open mr-2"></i>Ledger
                </a>
                <a href="<?php echo BASE_URL; ?>accounting/reconciliation" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/reconciliation') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-check-double mr-2"></i>Reconciliation
                </a>
                <a href="<?php echo BASE_URL; ?>accounting/manage_accountants" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/manage_accountants') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-cog mr-2"></i>Manage Accountants
                </a>
              </div>
            </div>

            <div>
              <a href="<?php echo BASE_URL; ?>reports/leads/dashboard" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('reports/leads/dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie mr-2"></i>Reporting
              </a>
              <a href="<?php echo BASE_URL; ?>settings" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('settings') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gear mr-2"></i>Settings
              </a>
            </div>
          <?php endif; ?>

          <a href="<?php echo BASE_URL; ?>auth/logout" class="block py-2 px-4 hover:bg-gray-200 mt-4">
            <i class="fa-solid fa-right-from-bracket mr-2"></i>Logout
          </a>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>auth/login" class="block py-2 px-4 hover:bg-gray-200">
            <i class="fa-solid fa-right-to-bracket mr-2"></i>Login
          </a>
          <a href="<?php echo BASE_URL; ?>auth/register" class="block py-2 px-4 hover:bg-gray-200">
            <i class="fa-solid fa-user-plus mr-2"></i>Register
          </a>
        <?php endif; ?>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 p-4">
      <!-- Top Navigation (for Notifications) -->
      <nav class="bg-black p-4 text-white mb-6 border-b-4 border-white">
        <div class="container mx-auto flex justify-end items-center">
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="relative top-nav-button mr-4 hover:bg-gray-500 p-1.5 transition-colors">
              <button id="profileButton" class="relative flex items-center">
                <?php
                $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $profile_picture = $user['profile_picture'];

                if ($profile_picture) : ?>
                  <img src="<?php echo BASE_URL . $profile_picture; ?>" alt="Profile Picture" class="w-8 h-8 object-cover" style="border: 2px solid white;">
                <?php else : ?>
                  <i class="fa-solid fa-user-circle fa-lg"></i>
                <?php endif; ?>
              </button>
              <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border-4 border-black top-nav-dropdown">
                <a href="<?php echo BASE_URL; ?>profile/view" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                <a href="<?php echo BASE_URL; ?>auth/logout" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
              </div>
            </div>
            <div class="relative top-nav-button hover:bg-gray-500 p-1.5 transition-colors">
              <button id="notificationButton" class="relative">
                <!-- Bell Icon -->
                <i class="fa-solid fa-bell"></i>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = :user_id AND is_read = 0");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $unread = $stmt->fetch(PDO::FETCH_ASSOC)['unread'];
                if ($unread > 0) : ?>
                  <!-- Notification Count -->
                  <span class="bg-red-500 text-white text-xs px-2 py-1 absolute -top-2 -right-2" style="border: 1px solid white;"><?php echo $unread; ?></span>
                <?php endif; ?>
              </button>
              <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border-4 border-black top-nav-dropdown">
                <?php
                $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if ($notifications) : ?>
                  <?php foreach ($notifications as $notification) : ?>
                    <a href="<?php echo BASE_URL; ?>notifications/view?id=<?php echo $notification['id']; ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
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

<?php
   $headerContent = ob_get_clean(); // Get the buffered content
     if (ENABLE_CACHE){
        setCache($cacheKey, $headerContent); // Save to cache
     }
    echo $headerContent; // Output the content
}
?>
      <script>

        document.getElementById('notificationButton').addEventListener('click', function() {
          document.getElementById('notificationDropdown').classList.toggle('hidden');
        });
        document.getElementById('profileButton').addEventListener('click', function() {
          document.getElementById('profileDropdown').classList.toggle('hidden');
        });
      </script>
      <script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const aside = document.querySelector('aside');

    mobileMenuButton.addEventListener('click', function() {
        aside.classList.toggle('open');
    });
});
</script>
      <!-- Main Content Area -->
      <div class="container mx-auto px-4">
        <!-- Your main content goes here -->