<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: view_tasks.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_type = $_POST['task_type'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE tasks SET task_type = :task_type, description = :description, due_date = :due_date, status = :status WHERE id = :id");
    $stmt->bindParam(':task_type', $task_type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $task_id);

    if ($stmt->execute()) {
        header("Location: view_tasks.php?lead_id={$task['lead_id']}");
        exit();
    } else {
        echo "<script>alert('Error updating task.');</script>";
    }
}

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Task</h1>

<!-- Edit Task Form -->
<form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
    <div class="mb-4">
        <label for="task_type" class="block text-gray-700">Task Type</label>
        <select name="task_type" id="task_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            <option value="Follow-Up" <?php echo $task['task_type'] === 'Follow-Up' ? 'selected' : ''; ?>>Follow-Up</option>
            <option value="Meeting" <?php echo $task['task_type'] === 'Meeting' ? 'selected' : ''; ?>>Meeting</option>
            <option value="Deadline" <?php echo $task['task_type'] === 'Deadline' ? 'selected' : ''; ?>>Deadline</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="description" class="block text-gray-700">Description</label>
        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($task['description']); ?></textarea>
    </div>

    <div class="mb-4">
        <label for="due_date" class="block text-gray-700">Due Date</