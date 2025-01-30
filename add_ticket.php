<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($description) || empty($priority) || empty($status)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO support_tickets (user_id, title, description, priority, assigned_to, category, status, expected_resolution_date) VALUES (:user_id, :title, :description, :priority, :assigned_to, :category, :status, :expected_resolution_date)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':priority', $priority);
          $stmt->bindParam(':assigned_to', $assigned_to);
         $stmt->bindParam(':category', $category);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':expected_resolution_date', $expected_resolution_date);

        if ($stmt->execute()) {
            $ticket_id = $conn->lastInsertId();
           $success = "Ticket created successfully!";
           header("Location: view_ticket.php?id=$ticket_id&success=true");
           exit();
        } else {
            $error = "Error creating ticket.";
        }
    }
}

// Fetch users for assignee
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Create Support Ticket</h1>

    <!-- Display error or success message -->
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
    <!-- Create Ticket Form -->
    <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
             <div class="mb-4">
                  <label for="title" class="block text-gray-700">Ticket Title</label>
                    <input type="text" name="title" id="title" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
              </div>
            <div class="mb-4">
               <label for="description" class="block text-gray-700">Ticket Description</label>
               <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
           </div>
           <div class="mb-4">
             <label for="priority" class="block text-gray-700">Priority Level</label>
                  <select name="priority" id="priority" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                       <option value="High">High</option>
                      <option value="Medium">Medium</option>
                      <option value="Low">Low</option>
                  </select>
           </div>
            <div class="mb-4">
                <label for="assigned_to" class="block text-gray-700">Assign To</label>
                  <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="">Select User</option>
                       <?php foreach ($users as $user): ?>
                           <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                           <?php endforeach; ?>
                 </select>
              </div>
              <div class="mb-4">
                <label for="category" class="block text-gray-700">Category</label>
                <input type="text" name="category" id="category" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
              </div>
             <div class="mb-4">
                  <label for="status" class="block text-gray-700">Ticket Status</label>
                   <select name="status" id="status" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                      <option value="New">New</option>
                       <option value="In Progress">In Progress</option>
                      <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                  </select>
            </div>
              <div class="mb-4">
                    <label for="expected_resolution_date" class="block text-gray-700">Expected Resolution Date</label>
                    <input type="date" name="expected_resolution_date" id="expected_resolution_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
               </div>
            <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Create Ticket</button>
        </form>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>