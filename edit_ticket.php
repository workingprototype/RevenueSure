<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch ticket details
$stmt = $conn->prepare("SELECT * FROM support_tickets WHERE id = :id");
$stmt->bindParam(':id', $ticket_id);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: manage_tickets.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $assigned_to = $_POST['assigned_to'];
    $category = trim($_POST['category']);
    $status = $_POST['status'];
      $expected_resolution_date = $_POST['expected_resolution_date'];

       if (empty($title) || empty($description) || empty($priority) || empty($status)) {
        $error = "All fields are required.";
        } else {
             $stmt = $conn->prepare("UPDATE support_tickets SET title = :title, description = :description, priority = :priority, assigned_to = :assigned_to, category = :category, status = :status, expected_resolution_date = :expected_resolution_date WHERE id = :id");
            $stmt->bindParam(':id', $ticket_id);
            $stmt->bindParam(':title', $title);
             $stmt->bindParam(':description', $description);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':assigned_to', $assigned_to);
           $stmt->bindParam(':category', $category);
            $stmt->bindParam(':status', $status);
             $stmt->bindParam(':expected_resolution_date', $expected_resolution_date);
            if ($stmt->execute()) {
                  $success = "Ticket updated successfully!";
                   header("Location: view_ticket.php?id=$ticket_id&success=true");
                   exit();
                } else {
                   $error = "Error updating ticket.";
                  }
        }
}

// Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Ticket</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
<form method="POST" action="">
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Ticket Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($ticket['title']); ?>" required>
            </div>
              <div class="mb-4">
                  <label for="description" class="block text-gray-700">Ticket Description</label>
                 <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($ticket['description']); ?></textarea>
              </div>
              <div class="mb-4">
                <label for="priority" class="block text-gray-700">Priority Level</label>
                <select name="priority" id="priority" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                   <option value="High" <?php if ($ticket['priority'] === 'High') echo 'selected'; ?>>High</option>
                   <option value="Medium" <?php if ($ticket['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                   <option value="Low" <?php if ($ticket['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                </select>
            </div>
             <div class="mb-4">
                <label for="assigned_to" class="block text-gray-700">Assign To</label>
                <select name="assigned_to" id="assigned_to" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="">Select User</option>
                       <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php if ($ticket['assigned_to'] === $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['username']); ?></option>
                            <?php endforeach; ?>
                  </select>
            </div>
              <div class="mb-4">
                <label for="category" class="block text-gray-700">Category</label>
               <input type="text" name="category" id="category" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($ticket['category'] ? $ticket['category'] : ''); ?>">
             </div>
               <div class="mb-4">
                <label for="status" class="block text-gray-700">Ticket Status</label>
                   <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                       <option value="New" <?php if ($ticket['status'] === 'New') echo 'selected'; ?>>New</option>
                      <option value="In Progress" <?php if ($ticket['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                       <option value="Resolved" <?php if ($ticket['status'] === 'Resolved') echo 'selected'; ?>>Resolved</option>
                      <option value="Closed" <?php if ($ticket['status'] === 'Closed') echo 'selected'; ?>>Closed</option>
                  </select>
               </div>
             <div class="mb-4">
                    <label for="expected_resolution_date" class="block text-gray-700">Expected Resolution Date</label>
                   <input type="date" name="expected_resolution_date" id="expected_resolution_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"  value="<?php echo htmlspecialchars($ticket['expected_resolution_date'] ? $ticket['expected_resolution_date'] : ''); ?>">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Ticket</button>
               <div class="mt-4">
                 <a href="view_ticket.php?id=<?php echo $ticket_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Ticket</a>
            </div>
        </form>
</div>
<?php
// Include footer
require 'footer.php';
?>