<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM support_ticket_tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: view_ticket.php");
    exit();
}

$ticket_id = $task['ticket_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
      $assigned_to = $_POST['assigned_to'];
      $priority = $_POST['priority'];
     

    if (empty($title) || empty($description) || empty($due_date) || empty($priority)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE support_ticket_tasks SET title = :title, description = :description, due_date = :due_date, assigned_to = :assigned_to, priority = :priority WHERE id = :id");
         $stmt->bindParam(':id', $task_id);
          $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
             $stmt->bindParam(':due_date', $due_date);
              $stmt->bindParam(':assigned_to', $assigned_to);
                $stmt->bindParam(':priority', $priority);
         if ($stmt->execute()) {
              header("Location: view_ticket.php?id=$ticket_id");
                exit();
             } else {
                $error = "Error updating task.";
         }
    }
}
// Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Include header
require 'header.php';
?>

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Task</h1>
      <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
              <div class="mb-4">
                   <label for="title" class="block text-gray-700">Task Title</label>
                     <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($task['title']); ?>" required>
              </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>
             <div class="mb-4">
                 <label for="due_date" class="block text-gray-700">Due Date</label>
                  <input type="datetime-local" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo date('Y-m-d\TH:i', strtotime($task['due_date'])); ?>" required>
              </div>
                <div class="mb-4">
                     <label for="assigned_to" class="block text-gray-700">Assign To</label>
                    <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select User</option>
                              <?php foreach ($users as $user): ?>
                                  <option value="<?php echo $user['id']; ?>" <?php if ($task['assigned_to'] === $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['username']); ?></option>
                             <?php endforeach; ?>
                    </select>
                </div>
              <div class="mb-4">
                  <label for="priority" class="block text-gray-700">Priority</label>
                   <select name="priority" id="priority" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                            <option value="Low" <?php if ($task['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                            <option value="Medium" <?php if ($task['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                              <option value="High" <?php if ($task['priority'] === 'High') echo 'selected'; ?>>High</option>
                    </select>
              </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Task</button>
         <div class="mt-4">
             <a href="view_ticket.php?id=<?php echo $ticket_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Ticket</a>
         </div>
       </form>
    </div>
<?php
// Include footer
require 'footer.php';
?>