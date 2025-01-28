<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$todo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch todo details
$stmt = $conn->prepare("SELECT * FROM todos WHERE id = :id");
$stmt->bindParam(':id', $todo_id);
$stmt->execute();
$todo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$todo) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
      $related_type = $_POST['related_type'];
    $related_id = $_POST['related_id'];

    // Validate inputs
    if (empty($title)) {
        $error = "Title is required.";
    } else {
        // Update customer
        $stmt = $conn->prepare("UPDATE todos SET title = :title, description = :description, due_date = :due_date, related_type = :related_type, related_id = :related_id WHERE id = :id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
       $stmt->bindParam(':due_date', $due_date);
         $stmt->bindParam(':related_type', $related_type);
          $stmt->bindParam(':related_id', $related_id);
        $stmt->bindParam(':id', $todo_id);

        if ($stmt->execute()) {
                header("Location: dashboard.php?success=true");
                 exit();
        } else {
                $error = "Error updating todo.";
        }
    }
}

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit To Do</h1>

<!-- Display error or success message -->
<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        To do updated successfully!
    </div>
<?php endif; ?>

<!-- Edit Customer Form -->
<form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
    <div class="mb-4">
        <label for="title" class="block text-gray-700">Title</label>
         <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($todo['title']); ?>" required>
    </div>
    <div class="mb-4">
        <label for="description" class="block text-gray-700">Description</label>
          <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" ><?php echo htmlspecialchars($todo['description']); ?></textarea>
    </div>
    <div class="mb-4">
        <label for="due_date" class="block text-gray-700">Due Date</label>
          <input type="datetime-local" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo $todo['due_date'] ? date('Y-m-d\TH:i', strtotime($todo['due_date'])) : ''; ?>">
    </div>
      <div class="mb-4">
                 <label for="related_type" class="block text-gray-700">Related to</label>
                <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                      <option value="" <?php echo $todo['related_type'] == '' ? 'selected' : ''; ?>>None</option>
                     <option value="task" <?php echo $todo['related_type'] == 'task' ? 'selected' : ''; ?>>Task</option>
                   <option value="lead" <?php echo $todo['related_type'] == 'lead' ? 'selected' : ''; ?>>Lead</option>
                    <option value="customer" <?php echo $todo['related_type'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                </select>
            </div>
             <div class="mb-4">
                     <label for="related_id" class="block text-gray-700">Related ID</label>
                        <input type="number" name="related_id" id="related_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($todo['related_id'] ?? ''); ?>">
              </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update To Do</button>
</form>

<?php
// Include footer
require 'footer.php';
?>