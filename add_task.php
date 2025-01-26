<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_type = $_POST['task_type'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (lead_id, user_id, task_type, description, due_date) VALUES (:lead_id, :user_id, :task_type, :description, :due_date)");
    $stmt->bindParam(':lead_id', $lead_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':task_type', $task_type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $due_date);

    if ($stmt->execute()) {
        header("Location: view_tasks.php?lead_id=$lead_id");
        exit();
    } else {
        echo "<script>alert('Error adding task.');</script>";
    }
}

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Add Task</h1>

<!-- Add Task Form -->
<form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
    <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">

    <div class="mb-4">
        <label for="task_type" class="block text-gray-700">Task Type</label>
        <select name="task_type" id="task_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            <option value="Follow-Up">Follow-Up</option>
            <option value="Meeting">Meeting</option>
            <option value="Deadline">Deadline</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="description" class="block text-gray-700">Description</label>
        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required></textarea>
    </div>

    <div class="mb-4">
        <label for="due_date" class="block text-gray-700">Due Date</label>
        <input type="datetime-local" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Task</button>
</form>

<?php
// Include footer
require 'footer.php';
?>