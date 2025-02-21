<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Fetch counts for different user types
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stmt->execute();
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM employees");
$stmt->execute();
$employee_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM accountants");
$stmt->execute();
$accountant_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM leads");
$stmt->execute();
$lead_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM customers");
$stmt->execute();
$customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch a limited number of users, employees, and customers for display
$stmt = $conn->prepare("SELECT id, username, 'user' as type FROM users WHERE role = 'user' LIMIT 5");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, name as username, 'employee' as type FROM employees LIMIT 5");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, name as username, 'accountant' as type FROM accountants LIMIT 5");
$stmt->execute();
$accountants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, name as username, 'lead' as type FROM leads LIMIT 5");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, name as username, 'customer' as type FROM customers LIMIT 5");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Combine all results
$all_entities = array_merge($users, $employees, $accountants, $leads, $customers);

// Function to get a random color for badges
function getRandomColor() {
    $colors = ['red', 'green', 'blue', 'yellow', 'purple', 'teal', 'orange'];
    return $colors[array_rand($colors)];
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Global Dashboard</h1>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Users Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-blue-500">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Users</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo htmlspecialchars($user_count); ?></p>
            <p class="text-gray-600 mt-2">Registered users on the platform.</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <i class="fas fa-users mr-1"></i> Active
            </span>
        </div>

        <!-- Employees Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-green-500">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Employees</h3>
            <p class="text-3xl font-bold text-green-600"><?php echo htmlspecialchars($employee_count); ?></p>
            <p class="text-gray-600 mt-2">Number of employees in the system.</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="fas fa-briefcase mr-1"></i> Employed
            </span>
        </div>

        <!-- Accountants Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-purple-500">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Accountants</h3>
            <p class="text-3xl font-bold text-purple-600"><?php echo htmlspecialchars($accountant_count); ?></p>
            <p class="text-gray-600 mt-2">Accountants with access to financial data.</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                <i class="fas fa-calculator mr-1"></i> Accounting
            </span>
        </div>
         <!-- Leads Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-red-500">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Leads</h3>
            <p class="text-3xl font-bold text-red-600"><?php echo htmlspecialchars($lead_count); ?></p>
            <p class="text-gray-600 mt-2">Leads within the system.</p>
             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                <i class="fas fa-user-plus mr-1"></i> Leads
            </span>
        </div>

        <!-- Customers Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-yellow-500">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Customers</h3>
            <p class="text-3xl font-bold text-yellow-600"><?php echo htmlspecialchars($customer_count); ?></p>
            <p class="text-gray-600 mt-2">Active customers.</p>
             <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                <i class="fas fa-handshake mr-1"></i> Customers
            </span>
        </div>
    </div>

    <!-- List of Entities -->
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-x-auto">
        <h2 class="text-2xl font-semibold text-800 mb-4">All Entities</h2>
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 font-semibold text-700 text-sm">Name</th>
                    <th class="px-4 py-3 font-semibold text-700 text-sm">Type</th>
                    <th class="px-4 py-3 font-semibold text-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($all_entities): ?>
                    <?php foreach ($all_entities as $entity): ?>
                        <tr class="border-b transition hover:bg-gray-100">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($entity['username']); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo getRandomColor(); ?>-100 text-<?php echo getRandomColor(); ?>-800">
                                   <?php echo htmlspecialchars($entity['type']); ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo BASE_URL; ?>profile/unified_view?id=<?php echo $entity['id']; ?>&type=<?php echo $entity['type']; ?>" class="text-blue-600 hover:underline">View Profile</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-600">No entities found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>