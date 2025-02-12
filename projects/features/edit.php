<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$feature_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Fetch feature details
$stmt = $conn->prepare("SELECT * FROM project_features WHERE id = :feature_id");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$feature = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$feature) {
    header("Location: " . BASE_URL . "projects/view?id=$project_id");
    exit();
}
// Fetch project details
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) {
    header("Location: " . BASE_URL . "projects/manage");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feature_title = trim($_POST['feature_title']);
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $owner_id = $_POST['owner_id'];
    $estimated_completion_date = $_POST['estimated_completion_date'];
    $actual_completion_date = !empty($_POST['actual_completion_date']) ? $_POST['actual_completion_date'] : null;

      if (empty($feature_title) || empty($description) || empty($priority) || empty($status)) {
          $error = "All fields are required.";
     }  else{
            $stmt = $conn->prepare("UPDATE project_features SET feature_title = :feature_title, description = :description, priority = :priority, status = :status, owner_id = :owner_id, estimated_completion_date = :estimated_completion_date, actual_completion_date = :actual_completion_date WHERE id = :id");
            $stmt->bindParam(':id', $feature_id);
           $stmt->bindParam(':feature_title', $feature_title);
             $stmt->bindParam(':description', $description);
             $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':owner_id', $owner_id);
             $stmt->bindParam(':estimated_completion_date', $estimated_completion_date);
            $stmt->bindParam(':actual_completion_date', $actual_completion_date);

         if ($stmt->execute()) {
             $success = "Feature updated successfully!";
             header("Location: " . BASE_URL . "projects/view?id=$project_id&success=true");
            exit();
           } else {
               $error = "Error updating feature.";
            }
         }
}
// Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Edit Feature: <?php echo htmlspecialchars($feature['feature_title']); ?></h1>

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

    <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                 <label for="feature_title" class="block text-gray-700">Feature Title</label>
                 <input type="text" name="feature_title" id="feature_title" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($feature['feature_title']); ?>" required>
            </div>
               <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description</label>
                  <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($feature['description']); ?></textarea>
               </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <div>
                         <label for="priority" class="block text-gray-700">Priority</label>
                              <select name="priority" id="priority" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                                   <option value="Low" <?php if ($feature['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                                    <option value="Medium" <?php if ($feature['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                                     <option value="High" <?php if ($feature['priority'] === 'High') echo 'selected'; ?>>High</option>
                                     <option value="Critical" <?php if ($feature['priority'] === 'Critical') echo 'selected'; ?>>Critical</option>
                             </select>
                      </div>
                    <div>
                         <label for="status" class="block text-gray-700">Status</label>
                           <select name="status" id="status" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                               <option value="Planned" <?php if ($feature['status'] === 'Planned') echo 'selected'; ?>>Planned</option>
                                 <option value="In Progress" <?php if ($feature['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                                 <option value="Under Review" <?php if ($feature['status'] === 'Under Review') echo 'selected'; ?>>Under Review</option>
                                  <option value="Completed" <?php if ($feature['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
                                    <option value="Deferred" <?php if ($feature['status'] === 'Deferred') echo 'selected'; ?>>Deferred</option>
                            </select>
                    </div>
                    <div>
                <label for="owner_id" class="block text-gray-700">Owner</label>
                   <select name="owner_id" id="owner_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                      <option value="">Select User</option>
                         <?php foreach ($users as $user): ?>
                               <option value="<?php echo $user['id']; ?>" <?php if($feature['owner_id'] == $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['username']); ?></option>
                           <?php endforeach; ?>
                   </select>
           </div>
                     <div>
                  <label for="estimated_completion_date" class="block text-gray-700">Estimated Completion Date</label>
                    <input type="datetime-local" name="estimated_completion_date" id="estimated_completion_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo date('Y-m-d\TH:i', strtotime($feature['estimated_completion_date'])); ?>">
            </div>
                     <div>
                    <label for="actual_completion_date" class="block text-gray-700">Actual Completion Date</label>
                   <input type="date" name="actual_completion_date" id="actual_completion_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo ($feature['actual_completion_date']) ? date('Y-m-d\TH:i', strtotime($feature['actual_completion_date'])) : null; ?>">
                </div>
              
            </div>
             <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Update Feature</button>
        </form>
</div>
</div>