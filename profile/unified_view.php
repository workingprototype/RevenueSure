<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Determine the user ID and type
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id']; // Default to current user
$user_type = isset($_GET['type']) ? $_GET['type'] : 'user'; // Default to user

// Validate user type
$allowed_types = ['user', 'employee', 'accountant', 'customer', 'lead', 'admin'];
if (!in_array($user_type, $allowed_types)) {
    $error = "Invalid user type.";
}

// Fetch user details based on type
$user = null;
$profile_picture = null;
$stmt = null;

if ($user_type == 'user' || $user_type == 'admin') {
    $stmt = $conn->prepare("SELECT users.*, team_roles.name AS role_name, team_departments.name AS department_name FROM users LEFT JOIN team_roles ON users.role_id = team_roles.id LEFT JOIN team_departments ON users.department_id = team_departments.id WHERE users.id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture = $user['profile_picture'] ?? null;
} elseif ($user_type == 'employee') {
    $stmt = $conn->prepare("SELECT employees.* FROM employees WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture = $user['profile_picture'] ?? null;
} elseif ($user_type == 'customer') {
    $stmt = $conn->prepare("SELECT customers.* FROM customers WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture = $user['profile_picture'] ?? null;
} elseif ($user_type == 'accountant') {
    $stmt = $conn->prepare("SELECT accountants.* FROM accountants WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture = null;
} elseif ($user_type == 'lead') {
    $stmt = $conn->prepare("SELECT leads.* FROM leads WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile_picture = null;
}

if (!$user) {
    $error = "User not found.";
}

// Fetch linked records (Projects, Tasks, etc.)
$projects = [];
$tasks = [];
$tickets = [];
$contracts = [];
$leads = [];
$customers = [];
$invoices = [];
$expenses = [];

if ($user) {
    switch ($user_type) {
        case 'user':
        case 'admin':
            // Fetch projects where user is the project manager
            $stmt = $conn->prepare("SELECT * FROM projects WHERE project_manager_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch tasks assigned to the user
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch support tickets assigned to or created by the user
            $stmt = $conn->prepare("SELECT * FROM support_tickets WHERE assigned_to = :user_id OR user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'employee':
            // Fetch tasks assigned to the employee
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch support tickets assigned to the employee
            $stmt = $conn->prepare("SELECT * FROM support_tickets WHERE assigned_to = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'customer':
            // Fetch contracts related to the customer
            $stmt = $conn->prepare("SELECT * FROM contracts WHERE customer_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'accountant':
            // Fetch invoices for the accountant
            $stmt = $conn->prepare("SELECT * FROM invoices");
            $stmt->execute();
            $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch expenses for the accountant
            $stmt = $conn->prepare("SELECT * FROM expenses");
            $stmt->execute();
            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'lead':
            // Fetch leads for the lead
            $stmt = $conn->prepare("SELECT * FROM leads");
            $stmt->execute();
            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Fetch customers for the lead
            $stmt = $conn->prepare("SELECT * FROM customers");
            $stmt->execute();
            $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
}

// New: Function to safely output a value (prevents XSS)
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<div class="container mx-auto p-6 fade-in">
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo e($error); ?></div>
    <?php endif; ?>

    <h1 class="text-4xl font-bold text-gray-900 mb-6">
        <?php echo e($user['username'] ?? $user['name'] ?? 'User Profile'); ?>
    </h1>

    <!-- Basic Profile Information -->
    <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
        <div class="flex items-center mb-4">
            <?php if ($profile_picture): ?>
                <img src="<?php echo BASE_URL . e($profile_picture); ?>" alt="Profile Picture" class="rounded-full w-32 h-32 object-cover mr-4">
            <?php else: ?>
                <div class="rounded-full w-32 h-32 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-user fa-3x text-gray-500"></i>
                </div>
            <?php endif; ?>
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">
                    <?php echo e($user['username'] ?? $user['name'] ?? 'User Profile'); ?>
                </h2>
                <p class="text-gray-600"><?php echo e($user['email'] ?? 'N/A'); ?></p>
                <?php if (isset($user['role_name'])): ?>
                    <p class="text-gray-600">Role: <?php echo e($user['role_name']); ?></p>
                <?php endif; ?>
                 <?php if (isset($user['department_name'])): ?>
                    <p class="text-gray-600">Department: <?php echo e($user['department_name']); ?></p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Detailed Information Sections -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <!-- Contact Information -->
            <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Contact Information</h3>
                <p class="text-gray-600">Email: <?php echo e($user['email'] ?? 'N/A'); ?></p>
                <p class="text-gray-600">Phone: <?php echo e($user['phone'] ?? 'N/A'); ?></p>
                <p class="text-gray-600">Address: <?php echo e($user['address'] ?? 'N/A'); ?></p>
            </div>

            <?php if ($user_type === 'accountant'): ?>
                <!-- Accountant Specific Information -->
                <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Accountant Information</h3>
                    <p class="text-gray-600">Accountant ID: <?php echo e($user['id'] ?? 'N/A'); ?></p>
                    <p class="text-gray-600">Role: <?php echo e($user['role'] ?? 'N/A'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <div>
            <!-- Linked Records Section -->
            <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Linked Records</h3>
                
                <!-- Projects Managed (for users and admins) -->
                <?php if (!empty($projects) && in_array($user_type, ['user', 'admin'])): ?>
                    <h4 class="text-lg font-semibold text-gray-700 mt-4">Projects Managed</h4>
                    <ul>
                        <?php foreach ($projects as $project): ?>
                            <li class="mb-2"><a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo e($project['id']); ?>" class="text-blue-600 hover:underline"><?php echo e($project['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Tasks Assigned (for users and employees) -->
                <?php if (!empty($tasks) && in_array($user_type, ['user', 'employee'])): ?>
                    <h4 class="text-lg font-semibold text-gray-700 mt-4">Tasks Assigned</h4>
                    <ul>
                        <?php foreach ($tasks as $task): ?>
                            <li class="mb-2"><a href="<?php echo BASE_URL; ?>tasks/view?id=<?php echo e($task['id']); ?>" class="text-blue-600 hover:underline"><?php echo e($task['task_name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <!-- Support Tickets (for users and employees) -->
                <?php if (!empty($tickets) && in_array($user_type, ['user', 'employee'])): ?>
                    <h4 class="text-lg font-semibold text-gray-700 mt-4">Support Tickets</h4>
                    <ul>
                        <?php foreach ($tickets as $ticket): ?>
                            <li class="mb-2"><a href="<?php echo BASE_URL; ?>support_tickets/view?id=<?php echo e($ticket['id']); ?>" class="text-blue-600 hover:underline"><?php echo e($ticket['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <!-- Contracts (for customers) -->
                <?php if (!empty($contracts) && $user_type === 'customer'): ?>
                    <h4 class="text-lg font-semibold text-gray-700 mt-4">Contracts</h4>
                    <ul>
                        <?php foreach ($contracts as $contract): ?>
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>contracts/view?id=<?php echo e($contract['id']); ?>" class="text-blue-600 hover:underline">
                                    <?php echo e($contract['name'] ?? 'Contract'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Invoices (for accountants) -->
                <?php if (!empty($invoices) && $user_type === 'accountant'): ?>
                    <h4 class="text-lg font-semibold text-gray-700 mt-4">Invoices</h4>
                    <ul>
                        <?php foreach ($invoices as $invoice): ?>
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo e($invoice['id']); ?>" class="text-blue-600 hover:underline">
                                    <?php echo e($invoice['invoice_number']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Expenses (for accountants) -->
                <?php if (!empty($expenses) && $user_type === 'accountant'): ?>
                    <h4 class="text-lg font-semibold text-gray-700 mt-4">Expenses</h4>
                    <ul>
                        <?php foreach ($expenses as $expense): ?>
                            <li class="mb-2">
                                <a href="<?php echo BASE_URL; ?>expenses/view?id=<?php echo e($expense['id']); ?>" class="text-blue-600 hover:underline">
                                    <?php echo e($expense['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Leads and Customers (for leads) -->
                <?php if ($user_type === 'lead'): ?>
                    <?php if (!empty($leads)): ?>
                        <h4 class="text-lg font-semibold text-gray-700 mt-4">Leads</h4>
                        <ul>
                            <?php foreach ($leads as $lead): ?>
                                <li class="mb-2">
                                    <a href="<?php echo BASE_URL; ?>leads/view?id=<?php echo e($lead['id']); ?>" class="text-blue-600 hover:underline">
                                        <?php echo e($lead['name'] ?? 'Lead'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if (!empty($customers)): ?>
                        <h4 class="text-lg font-semibold text-gray-700 mt-4">Customers</h4>
                        <ul>
                            <?php foreach ($customers as $customer): ?>
                                <li class="mb-2">
                                    <a href="<?php echo BASE_URL; ?>customers/view?id=<?php echo e($customer['id']); ?>" class="text-blue-600 hover:underline">
                                        <?php echo e($customer['name'] ?? 'Customer'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard Button -->
    <div class="mt-6">
        <a href="<?php echo BASE_URL; ?>views/global_dashboard" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Global View
        </a>
    </div>
</div>