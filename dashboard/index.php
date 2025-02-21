<?php
require_once ROOT_PATH . 'helper/core.php';

redirectIfUnauthorized();



$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    // Fetch user details
    $stmt = $conn->prepare("SELECT username, credits FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $user['username'];

    // Fetch leads count
    $stmt = $conn->prepare("SELECT COUNT(*) as total_leads FROM leads");
    $stmt->execute();
    $leads_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_leads'];

    // Fetch todos
    $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = :user_id ORDER BY due_date ASC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch outstanding payments data
    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total_unpaid, 
        SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) as total_overdue,
        SUM(CASE WHEN status = 'Partially Paid' THEN 1 ELSE 0 END) as total_partially_paid,
        SUM(total - paid_amount) as outstanding_amount
        FROM invoices
        WHERE status != 'Paid'
    ");
    $stmt->execute();
    $outstanding_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    displayAlert("Database error: " . $e->getMessage(), 'error');
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        <!-- Credits Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-blue-500 transition hover:shadow-2xl">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your Credits</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo htmlspecialchars($user['credits'] ?? 0); ?></p>
            <p class="text-gray-600 mt-2">Credits available for accessing leads.</p>
            <a href="<?php echo BASE_URL; ?>credits/manage" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Manage Credits</a>
        </div>

        <!-- Leads Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-blue-500 transition hover:shadow-2xl">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Total Leads</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo htmlspecialchars($leads_count ?? 0); ?></p>
            <p class="text-gray-600 mt-2">Leads available in the platform.</p>
            <a href="<?php echo BASE_URL; ?>leads/search" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search Leads</a>
        </div>

        <!-- Admin-Specific Card -->
        <?php if (isAdmin()): ?>
            <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-green-500 transition hover:shadow-2xl">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Admin Actions</h3>
                <p class="text-gray-600">Manage users and leads.</p>
                <a href="<?php echo BASE_URL; ?>reports/leads/dashboard" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Go to Reporting Dashboard</a>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-purple-500 transition hover:shadow-2xl">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Outstanding Payments</h3>
                <p class="text-gray-600 mb-2">
                    <strong>Total Unpaid Invoices:</strong>
                    <span class="bg-red-100 text-red-800 rounded-full px-2 py-1">
                        <?php echo htmlspecialchars($outstanding_data['total_unpaid'] ?? 0); ?>
                    </span>
                </p>
                <p class="text-gray-600 mb-2">
                    <strong>Total Partially Paid Invoices:</strong>
                    <span class="bg-yellow-100 text-yellow-800 rounded-full px-2 py-1">
                        <?php echo htmlspecialchars($outstanding_data['total_partially_paid'] ?? 0); ?>
                    </span>
                </p>
                <p class="text-gray-600 mb-2">
                    <strong>Total Overdue Invoices:</strong>
                    <span class="bg-gray-100 text-gray-800 rounded-full px-2 py-1">
                        <?php echo htmlspecialchars($outstanding_data['total_overdue'] ?? 0); ?>
                    </span>
                </p>

                <p class="text-gray-600 mt-2">
                    <strong>Total Outstanding Amount:</strong> $<?php echo htmlspecialchars($outstanding_data['outstanding_amount'] ?? 0); ?>
                </p>
                <a href="<?php echo BASE_URL; ?>invoices/manage" class="mt-4 inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">View Outstanding Invoices</a>
            </div>
            <!-- Unified View Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-indigo-500">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Explore All Entities</h3>
            <p class="text-gray-600 mt-2">View and manage all users, employees, leads, and customers in one place.</p>
            <a href="<?php echo BASE_URL; ?>views/global_dashboard" class="bg-indigo-700 text-white px-4 py-2 rounded-xl hover:bg-indigo-900 transition duration-300 inline-block mt-4">
                <i class="fas fa-users mr-2"></i> View All Entities
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-xl mt-8 border-l-4 border-blue-500 transition hover:shadow-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-4 relative">
            <i class="fas fa-list-check absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> To-Do List
        </h2>
        <form method="POST" action="<?php echo BASE_URL; ?>todos/add" class="mb-6">
            <?php echo csrfTokenInput(); ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700">Title</label>
                    <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label for="due_date" class="block text-gray-700">Due Date</label>
                    <input type="datetime-local" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                
            <div class="mb-4 flex items-center">
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300">Add To Do</button>
            </div>
        </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
            <div class="mb-4">
                <label for="related_type" class="block text-gray-700">Related to</label>
                <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showRelatedInput(this.value)">
                    <option value="">None</option>
                    <option value="task">Task</option>
                    <option value="lead">Lead</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div id="related_id_container" class="mb-4 hidden">
                <label for="related_id" class="block text-gray-700">Related</label>
                <input type="text" name="related_id" id="related_id" class="w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" data-autocomplete-id="related-autocomplete">
                <div id="related-autocomplete-suggestions" class="absolute z-10 mt-2 w-full bg-white border rounded shadow-md hidden"></div>
            </div>
        </form>
        <?php if ($todos): ?>
            <div class="mt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" id="show_completed" class="mr-2" >
                    <span class="text-gray-700">Show Completed</span>
                </label>
            </div>
            <ul id="todo_items" class="mt-4">
                <?php foreach ($todos as $todo): ?>
                    <li class="mb-2 p-3 rounded-lg border relative <?php echo $todo['is_completed'] ? 'bg-green-100 border-green-200 line-through completed-todo' : 'border-gray-200'; ?>">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold <?php echo $todo['is_completed'] ? 'text-green-700' : 'text-gray-800'; ?>"> <?php echo htmlspecialchars($todo['title']); ?></h3>
                                <p class="text-gray-600"> <?php echo htmlspecialchars($todo['description'] ? $todo['description'] : ""); ?></p>
                                <?php if($todo['due_date']): ?>
                                    <p class="text-gray-600 text-sm">
                                        Due Date:  <?php echo date('Y-m-d H:i', strtotime($todo['due_date'])); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($todo['related_type'] && $todo['related_id']): ?>
                                    <p class="text-gray-500 text-sm">
                                        <strong>Related:</strong>
                                        <?php echo ucfirst(htmlspecialchars($todo['related_type'])); ?> #<?php echo htmlspecialchars($todo['related_id']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?php echo BASE_URL; ?>todos/edit?id=<?php echo $todo['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="<?php echo BASE_URL; ?>todos/delete?id=<?php echo $todo['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                                <a href="<?php echo BASE_URL; ?>todos/mark_complete?id=<?php echo $todo['id']; ?>&completed=<?php echo ($todo['is_completed'] == 1 ? 0 : 1); ?>" class="text-green-600 hover:underline"><?php echo $todo['is_completed'] == 1 ? 'Mark Incomplete' : 'Mark Complete'; ?></a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No to-dos added.</p>
        <?php endif; ?>
    </div>
</div>