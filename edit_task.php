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
     $project_id = $task['project_id'];
     $lead_id = $task['lead_id'];
    
    $stmt = $conn->prepare("UPDATE tasks SET task_type = :task_type, description = :description, due_date = :due_date, status = :status WHERE id = :id");
    $stmt->bindParam(':task_type', $task_type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $task_id);

    if ($stmt->execute()) {
      if($lead_id){
        header("Location: view_tasks.php?lead_id={$task['lead_id']}");
        exit();
      }else if($project_id){
        header("Location: view_tasks.php?project_id={$task['project_id']}");
        exit();
      }
        header("Location: view_tasks.php");
        exit();
    } else {
        echo "<script>alert('Error updating task.');</script>";
    }
// Handle reminder
if (!empty($_POST['reminder'])) {
    $reminder_time = $_POST['reminder'];
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, related_id, type, created_at) VALUES (:user_id, :message, :related_id, 'task_reminder', :created_at)");
    $message = "Reminder: Task '{$task['description']}' is due on {$task['due_date']}.";
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':related_id', $task_id);
    $stmt->bindParam(':created_at', $reminder_time);
    $stmt->execute();
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
        <label for="due_date" class="block text-gray-700">Due Date</label>
        <input type="datetime-local" name="due_date" id="due_date" value="<?php echo date('Y-m-d\TH:i', strtotime($task['due_date'])); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
    </div>

    <div class="mb-4">
        <label for="status" class="block text-gray-700">Status</label>
        <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            <option value="Pending" <?php echo $task['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="Completed" <?php echo $task['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
        </select>
    </div>
     <div class="mb-4">
        <label for="reminder" class="block text-gray-700">Set Reminder</label>
           <input type="datetime-local" name="reminder" id="reminder" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Task</button>
</form>

<?php
// Include footer
require 'footer.php';
?>