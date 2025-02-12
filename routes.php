<?php

$uri = $_GET['route'] ?? '';

$routes = [
    '' => 'views/home.php',
    'auth/login' => 'auth/login.php',
    'auth/logout' => 'auth/logout.php',
    'auth/register' => 'auth/register.php',
    'dashboard/index' => 'dashboard/index.php',
    'dashboard' => 'dashboard/index.php',

    'categories/manage' => 'categories/manage.php',
    'categories/add' => 'categories/add.php',
    'categories/edit' => 'categories/edit.php',
    'categories/delete' => 'categories/delete.php',

    'contracts/manage' => 'contracts/manage.php',
    'contracts/add' => 'contracts/add.php',
    'contracts/process' => 'contracts/process.php',
    'contracts/edit' => 'contracts/edit.php',
    'contracts/view' => 'contracts/view.php',
    'contracts/sign' => 'contracts/sign.php',
    'contracts/delete' => 'contracts/delete.php',

    'credits/manage' => 'credits/manage.php',

    'customers/manage' => 'customers/manage.php',
    'customers/add' => 'customers/add.php',
    'customers/edit' => 'customers/edit.php',
    'customers/view' => 'customers/view.php',
    'customers/add_interaction' => 'customers/add_interaction.php',
    'customers/edit_preference' => 'customers/edit_preference.php',
    'customers/delete' => 'customers/delete.php',

    'departments/manage' => 'departments/manage.php',
    'departments/add' => 'departments/add.php',
    'departments/edit' => 'departments/edit.php',
    'departments/delete' => 'departments/delete.php',

    'projects/categories/manage' => 'projects/categories/manage.php',
    'projects/categories/add' => 'projects/categories/add.php',
    'projects/categories/edit' => 'projects/categories/edit.php',
    'projects/categories/delete' => 'projects/categories/delete.php',

    'projects/manage' => 'projects/manage.php',
    'projects/add' => 'projects/add.php',
    'projects/edit' => 'projects/edit.php',
    'projects/delete' => 'projects/delete.php',
    'projects/view' => 'projects/view.php',
    'projects/gantt_chart' => 'projects/gantt_chart.php',
    'projects/kanban_board' => 'projects/kanban_board.php',

    'discussions/manage' => 'discussions/manage.php',
    'discussions/add' => 'discussions/add.php',
    'discussions/edit' => 'discussions/edit.php',
    'discussions/view' => 'discussions/view.php',
    'discussions/upload_attachment' => 'discussions/upload_attachment.php',
    'discussions/delete' => 'discussions/delete.php',

    'employees/manage' => 'employees/manage.php',
    'employees/add' => 'employees/add.php',
    'employees/edit' => 'employees/edit.php',
    'employees/delete' => 'employees/delete.php',
    'employees/search' => 'employees/search.php',

    'expenses/manage' => 'expenses/manage.php',
    'expenses/add' => 'expenses/add.php',
    'expenses/edit' => 'expenses/edit.php',
    'expenses/view' => 'expenses/view.php',
    'expenses/delete' => 'expenses/delete.php',
    'expenses/charts' => 'expenses/charts.php',
    'expenses/filters' => 'expenses/filters.php',
    'expenses/metrics' => 'expenses/metrics.php',
    'expenses/table' => 'expenses/table.php',
    'expenses/upload' => 'expenses/upload.php',

    'invoices/manage' => 'invoices/manage.php',
    'invoices/add' => 'invoices/add.php',
    'invoices/edit' => 'invoices/edit.php',
    'invoices/view' => 'invoices/view.php',
    'invoices/delete' => 'invoices/delete.php',
    'invoices/record_payment' => 'invoices/record_payment.php',

    'knowledge_base/manage' => 'knowledge_base/manage.php',
    'knowledge_base/add' => 'knowledge_base/add.php',
    'knowledge_base/edit' => 'knowledge_base/edit.php',
    'knowledge_base/view' => 'knowledge_base/view.php',
    'knowledge_base/delete' => 'knowledge_base/delete.php',
    'knowledge_base/request/manage' => 'knowledge_base/request/manage.php',
    'knowledge_base/request/add' => 'knowledge_base/request/add.php',
    'knowledge_base/categories/manage' => 'knowledge_base/categories/manage.php',
    'knowledge_base/categories/add' => 'knowledge_base/categories/add.php',
    'knowledge_base/categories/edit' => 'knowledge_base/categories/edit.php',
    'knowledge_base/categories/delete' => 'knowledge_base/categories/delete.php',

    'leads/manage' => 'leads/manage.php', //Todo: leads manage not developed yet
    'leads/add' => 'leads/add.php',
    'leads/edit' => 'leads/edit.php',
    'leads/view' => 'leads/view.php',
    'leads/delete' => 'leads/delete.php',
    'leads/import' => 'leads/import.php',
    'leads/export' => 'leads/export.php',
    'leads/search' => 'leads/search.php',
    'leads/yourleads' => 'leads/your_leads.php',
    'leads/upload_attachment' => 'leads/upload_attachment.php',
    'leads/delete_attachment' => 'leads/delete_attachment.php',
    'leads/mass_delete' => 'leads/mass_delete.php',
    'leads/track_behavior' => 'leads/track_behavior.php',
    'leads/fetch' => 'leads/fetch.php', // Add fetch route
    'profile' => 'profile/view.php',
    'profile/view' => 'profile/view.php',
    'profile/upload_profile_picture' => 'profile/upload_profile_picture.php',
    'leads' => 'leads/search.php',

    'reminders/add' => 'reminders/add.php',

    'settings' => 'settings/index.php',
    'settings/index' => 'settings/index.php',
    'settings/invoice/index' => 'settings/invoice/index.php',
    'settings/invoice' => 'settings/invoice/index.php',

    'support_tickets/manage' => 'support_tickets/manage.php',
    'support_tickets/add' => 'support_tickets/add.php',
    'support_tickets/edit' => 'support_tickets/edit.php',
    'support_tickets/view' => 'support_tickets/view.php',
    'support_tickets/delete' => 'support_tickets/delete.php',

    'support_tickets/ticket_tasks/manage' => 'support_tickets/ticket_tasks/manage.php',
    'support_tickets/ticket_tasks/toggle_status' => 'support_tickets/ticket_tasks/toggle_status.php',
    'support_tickets/ticket_tasks/add' => 'support_tickets/add.php',
    'support_tickets/ticket_tasks/edit' => 'support_tickets/edit.php',
    'support_tickets/ticket_tasks/delete' => 'support_tickets/ticket_tasks/delete.php',

    'support_tickets/calendar' => 'support_tickets/calendar.php',
    'support_tickets/card' => 'support_tickets/card.php',
    'support_tickets/grid' => 'support_tickets/grid.php',
    'support_tickets/kanban' => 'support_tickets/kanban.php',
    'support_tickets/list' => 'support_tickets/list.php',

    'support_tickets/upload_attachment' => 'support_tickets/upload_attachment.php',
    'support_tickets/delete_attachment' => 'support_tickets/delete_attachment.php',

    'tasks/manage' => 'tasks/manage.php',
    'tasks/add' => 'tasks/add.php',
    'tasks/edit' => 'tasks/edit.php',
    'tasks/view' => 'tasks/view.php',
    'tasks/viewtasks' => 'tasks/viewtasks.php',
    'tasks/delete' => 'tasks/delete.php',

    'tasks/calendar' => 'tasks/calendar.php',
    'tasks/card' => 'tasks/card.php',
    'tasks/grid' => 'tasks/grid.php',
    'tasks/list' => 'tasks/list.php',

    'tasks/toggle_status' => 'tasks/toggle_status.php',
    'tasks/update_status' => 'tasks/update_status.php',

    'team/manage' => 'team/manage.php',
    'team/add' => 'team/add.php',
    'team/edit' => 'team/edit.php',
    'team/delete' => 'team/delete.php',
    'team/roles/manage' => 'team/roles/manage.php',
    'team/roles/add' => 'team/roles/add.php',
    'team/roles/edit' => 'team/roles/edit.php',
    'team/roles/delete' => 'team/roles/delete.php',
    'team/departments/manage' => 'team/departments/manage.php',
    'team/departments/add' => 'team/departments/add.php',
    'team/departments/edit' => 'team/departments/edit.php',
    'team/departments/delete' => 'team/departments/delete.php',

    'todos/add' => 'todos/add.php',
    'todos/edit' => 'todos/edit.php',
    'todos/delete' => 'todos/delete.php',
    'todos/mark_complete' => 'todos/mark_complete.php',

    'reports/leads/dashboard' => 'reports/leads/dashboard.php',
    'notifications/view' => 'notifications/view.php',

    'accounting/dashboard' => 'accounting/dashboard.php',
    'accounting/ledger' => 'accounting/ledger.php',
    'accounting/reconciliation' => 'accounting/reconciliation.php',
    'accounting/manage_accountants' => 'accounting/manage_accountants.php',

    'payments/view' => 'payments/view.php'
];

// Define routes that should not include header and footer
$excludeHeaderFooter = ['leads/fetch'];

if (array_key_exists($uri, $routes)) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    require 'helper/core.php'; // Load database connection and other core functions

    // CSRF protection: Generate token if not exists
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Validate CSRF token for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header("HTTP/1.0 400 Bad Request");
            echo "<h1>400 Bad Request</h1>";
            echo "CSRF token validation failed.";
            exit;
        }
        // Optional: Regenerate CSRF token after successful validation
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }


    

    // Determine if we should include the header and footer
    $includeHeaderFooter = !in_array($uri, $excludeHeaderFooter);

    // Capture the start of the output buffer if needed
    if ($includeHeaderFooter) {
        ob_start();
        require 'includes/header.php';
    }

    // Fetch the content from the requested view
    $viewPath = $routes[$uri];

    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The requested resource `", htmlspecialchars($uri), "` was not found.";
        exit;
    }

    // Get the content from the buffer
    if ($includeHeaderFooter) {
        require 'includes/footer.php';
        $page_content = ob_get_clean();
        echo $page_content;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "The requested resource `", htmlspecialchars($uri), "` was not found.";
    exit;
}