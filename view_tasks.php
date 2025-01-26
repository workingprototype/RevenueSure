<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : 0;

// Fetch tasks for the lead
$stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE lead_id = :lead_id ORDER BY due_date ASC");
$stmt->bindParam(':lead_id', $lead_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Tasks for Lead #<?php echo $lead_id; ?></h1>

<!-- Add Task Button -->
<a href="add_task.php?lead_id=<?php echo $lead_id; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Task</a>

<!-- Tasks Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Task Type</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Due Date</th>
                <th class="px-4 py-2">Assigned To</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tasks): ?>
                <?php foreach ($tasks as $task): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['task_type']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['description']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['due_date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['username']); ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full <?php echo $task['status'] === 'Pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800'; ?>">
                                <?php echo htmlspecialchars($task['status']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-600">No tasks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>