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
            --md-sys-color-primary: #6750A4; /* Primary color from Material 3 */
            --md-sys-color-on-primary: #FFFFFF;
            --md-sys-color-primary-container: #EADDFF;
            --md-sys-color-on-primary-container: #21005E;
            --md-sys-color-secondary: #625B71;
            --md-sys-color-on-secondary: #FFFFFF;
            --md-sys-color-secondary-container: #E8DEF8;
            --md-sys-color-on-secondary-container: #1D192B;
            --md-sys-color-tertiary: #7D5260;
            --md-sys-color-on-tertiary: #FFFFFF;
            --md-sys-color-tertiary-container: #FFD8E4;
            --md-sys-color-on-tertiary-container: #31111D;
            --md-sys-color-error: #B3261E;
            --md-sys-color-error-container: #F9DEDC;
            --md-sys-color-on-error: #FFFFFF;
            --md-sys-color-on-error-container: #410E0B;
            --md-sys-color-background: #FFFBFE;
            --md-sys-color-on-background: #1C1B1F;
            --md-sys-color-surface: #FFFBFE;
            --md-sys-color-on-surface: #1C1B1F;
            --md-sys-color-surface-variant: #E7E0EC;
            --md-sys-color-on-surface-variant: #49454F;
            --md-sys-color-outline: #79747E;
            --md-sys-color-inverse-surface: #313033;
            --md-sys-color-inverse-on-surface: #F4EFF4;
            --md-sys-color-inverse-primary: #D0BCFF;
            --md-sys-color-shadow: #000000;
            --md-sys-color-surface-tint: #6750A4;
            --md-sys-color-outline-variant: #CAC4D0;
            --md-sys-color-scrim: #000000;


            --primary-color: var(--md-sys-color-primary); /* Using M3 Primary */
            --secondary-color: var(--md-sys-color-secondary); /* Using M3 Secondary */
            --background-light: var(--md-sys-color-background); /* Using M3 Background */
            --background-dark: #1f2937;
            --error-color: var(--md-sys-color-error); /* Using M3 Error */
             --nav-item-height: 48px;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--md-sys-color-background);
            color: var(--md-sys-color-on-background);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;

        }

         /*Elevated Card*/
         .elevated-card {
           background-color: var(--md-sys-color-surface);
             color: var(--md-sys-color-on-surface);
             border-radius: 16px;
             box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.2), 0px 4px 5px 0px rgba(0, 0, 0, 0.14), 0px 1px 10px 0px rgba(0, 0, 0, 0.12);
             transition: box-shadow 0.3s ease;
         }

          .elevated-card:hover {
               box-shadow: 0px 3px 5px -1px rgba(0, 0, 0, 0.3), 0px 5px 8px 0px rgba(0, 0, 0, 0.2), 0px 2px 12px 0px rgba(0, 0, 0, 0.18);
           }
          /*Filled button*/
          .filled-button {
              background-color: var(--md-sys-color-primary);
               color: var(--md-sys-color-on-primary);
                 padding: 10px 24px;
                 border-radius: 8px;
                   font-weight: 500;
                    font-size: 0.875rem; /* Equivalent to 14px */
                line-height: 1.25rem; /* Equivalent to 20px */
                  letter-spacing: 0.0178571429em; /* Approximation */
                    text-transform: none;

                     box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2), 0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
                    transition: background-color 0.3s ease, box-shadow 0.3s ease;
                    border: none;
                      cursor: pointer;

          }
             .filled-button:hover {
                  background-color: var(--md-sys-color-primary-container);
                   box-shadow: 0px 2px 4px -1px rgba(0, 0, 0, 0.3), 0px 4px 5px 0px rgba(0, 0, 0, 0.24), 0px 1px 10px 0px rgba(0, 0, 0, 0.22);
              }
              .filled-button:focus {
                  outline: none; /* Remove default focus outline */
                  box-shadow: 0px 5px 5px -3px rgba(0, 0, 0, 0.2), 0px 8px 10px 1px rgba(0, 0, 0, 0.14), 0px 3px 14px 2px rgba(0, 0, 0, 0.12);
              }
          /*Outlined Button*/
            .outlined-button {
                 border: 1px solid var(--md-sys-color-outline);
                 color: var(--md-sys-color-primary);
                 padding: 9px 23px;
                 border-radius: 8px;
                   font-weight: 500;
                    font-size: 0.875rem; /* Equivalent to 14px */
                line-height: 1.25rem; /* Equivalent to 20px */
                  letter-spacing: 0.0178571429em; /* Approximation */
                    text-transform: none;
                  transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
                    cursor: pointer;
                     background-color: transparent;
             }

             .outlined-button:hover {
                  background-color: rgba(var(--md-sys-color-primary), 0.08); /* 12% Primary color overlay */
                   border-color: var(--md-sys-color-primary);
                   color: var(--md-sys-color-primary);
              }
               .outlined-button:focus {
                  outline: none; /* Remove default focus outline */
                   border-color: var(--md-sys-color-primary);
                  box-shadow: 0 0 0 4px rgba(var(--md-sys-color-primary), 0.2);
              }

         aside {
            min-width: 280px;
             transition: all 0.3s ease-in-out;
             border-right: 1px solid var(--md-sys-color-outline-variant);
             background: var(--md-sys-color-surface-variant);
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
            color: var(--md-sys-color-on-surface-variant);
             font-size: 0.9rem;
           }
          .menu-item .submenu a:hover {
              background-color: rgba(0,0,0,0.03);
             color: var(--md-sys-color-on-surface);
           }
         .menu-item .submenu a {
              padding: 12px 30px;
             font-size: 0.85rem;
            transition: color 0.2s ease, background-color 0.2s ease;
            }

         .menu-item.active a {
          color: var(--md-sys-color-on-surface);
             background-color: var(--md-sys-color-surface);
           border-left-color: var(--md-sys-color-primary);
          }
      .submenu {
            padding-left: 10px;
            transition: all 0.4s ease; /* Slowed down transition */
             margin-top: 5px;
               border-left: 2px solid var(--md-sys-color-outline-variant);
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
                border-color: var(--md-sys-color-primary);
               box-shadow: 0 0 0 2px rgba(var(--md-sys-color-primary-container), 0.5);
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
               border: 1px solid var(--md-sys-color-outline-variant);
                 border-radius: 12px;
                  box-shadow: 0 10px 15px rgba(0,0,0,0.1);
                   background-color: var(--md-sys-color-surface);
               }
              .top-nav-dropdown a{
                padding: 10px;
                 display: block;
                   color: var(--md-sys-color-on-surface);
               transition: all 0.2s ease;
               }
                .top-nav-dropdown a:hover{
                   background: var(--md-sys-color-surface-variant);
                 }
                  .paper-doc {
            font-family: 'Georgia', serif;
            max-width: 800px;
            margin: 20px auto;
             padding: 40px 60px;
             background-color: var(--md-sys-color-surface);
              border: 1px solid var(--md-sys-color-outline-variant);
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
             border-radius: 15px;
           line-height: 1.7;
              font-size: 16px;
        }
         .paper-doc h1, .paper-doc h2, .paper-doc h3, .paper-doc h4, .paper-doc h5, .paper-doc h6 {
             font-family: 'Roboto Slab', serif;
            margin-bottom: 15px;
              line-height: 1.4;
            color: var(--md-sys-color-on-surface);
              font-weight: 700;

        }
          .paper-doc h1{
               font-size: 2.5rem;

           }
              .paper-doc h2 {
                font-size: 2rem;
                   border-bottom: 2px solid var(--md-sys-color-outline-variant);
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
             color: var(--md-sys-color-primary);
              text-decoration: none;
                 border-bottom: 1px solid transparent;
             transition: border-bottom 0.3s ease;
          }
      .paper-doc a:hover {
             border-bottom: 1px solid var(--md-sys-color-primary);
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
                border-left: 4px solid var(--md-sys-color-outline);
               font-style: italic;
                color: var(--md-sys-color-on-surface-variant);
                background-color: var(--md-sys-color-surface-variant);
          }
          .paper-doc .rating-bookmark-container {
               display: flex;
                justify-content: space-between;
               align-items: center;
               margin-top: 30px;
              border-top: 1px solid var(--md-sys-color-outline-variant);
                  padding-top: 15px;
          }
          .paper-doc .rating-bookmark-container button {
                transition: all 0.3s ease;
           }
             .paper-doc .rating-bookmark-container button:hover {
                   color: var(--md-sys-color-primary);
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
            <nav class="p-4 text-white mb-6 rounded-md" style="background-color: var(--md-sys-color-primary);">
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
                             <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-xl shadow-lg top-nav-dropdown">
                                <a href="<?php echo BASE_URL; ?>profile/view" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
                                <a href="<?php echo BASE_URL; ?>auth/logout" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
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
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-64 rounded-xl shadow-lg top-nav-dropdown">
                                <?php
                                $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5");
                                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                $stmt->execute();
                                $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>
                                <?php if ($notifications) : ?>
                                    <?php foreach ($notifications as $notification) : ?>
                                        <a href="notifications/view?id=<?php echo $notification['id']; ?>" class="block px-4 py-2 hover:bg-gray-100">
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