<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$issue_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Fetch issue details
$stmt = $conn->prepare("SELECT * FROM project_issues WHERE id = :issue_id");
$stmt->bindParam(':issue_id', $issue_id);
$stmt->execute();
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
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
 // Fetch Features Details.
$stmt = $conn->prepare("SELECT id, feature_title FROM project_features WHERE project_id = :project_id");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$features = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $issue_title = trim($_POST['issue_title']);
    $description = $_POST['description'];
    $issue_type = $_POST['issue_type'];
    $priority = $_POST['priority'];
    $assigned_to = $_POST['assigned_to'];
    $related_feature_id = $_POST['related_feature_id'] ?? null;
    $resolution_date = $_POST['resolution_date'] ?? null;
    $steps_to_reproduce = $_POST['steps_to_reproduce'];
     $environment_version = trim($_POST['environment_version']);
         $status = $_POST['status'];

   if (empty($issue_title) || empty($description) || empty($issue_type) || empty($priority) || empty($steps_to_reproduce) || empty($status)) {
        $error = "All fields are required.";
   }  else {
        $stmt = $conn->prepare("UPDATE project_issues SET issue_title = :issue_title, description = :description, issue_type = :issue_type, priority = :priority, assigned_to = :assigned_to, related_feature_id = :related_feature_id, steps_to_reproduce = :steps_to_reproduce, environment_version = :environment_version, status = :status, resolution_date = :resolution_date  WHERE id = :id");
         $stmt->bindParam(':id', $issue_id);
        $stmt->bindParam(':issue_title', $issue_title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':issue_type', $issue_type);
         $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':assigned_to', $assigned_to);
          $stmt->bindParam(':related_feature_id', $related_feature_id);
           $stmt->bindParam(':steps_to_reproduce', $steps_to_reproduce);
         $stmt->bindParam(':environment_version', $environment_version);
          $stmt->bindParam(':status', $status);
         $stmt->bindParam(':resolution_date', $resolution_date);
         if ($stmt->execute()) {
             $success = "Issue updated successfully!";
             header("Location: " . BASE_URL . "projects/view?id=$project_id&success=true");
              exit();
        } else {
                $error = "Error updating issue.";
            }
       }

}
// Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Edit Issue: <?php echo htmlspecialchars($issue['issue_title']); ?></h1>

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
                    <label for="issue_title" class="block text-gray-700">Issue Title</label>
                       <input type="text" name="issue_title" id="issue_title" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($issue['issue_title']); ?>" required>
                </div>
                <div class="mb-4">
                     <label for="description" class="block text-gray-700">Description</label>
                      <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($issue['description']); ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                   <div>
                         <label for="issue_type" class="block text-gray-700">Issue Type</label>
                        <select name="issue_type" id="issue_type" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                              <option value="Bug" <?php if ($issue['issue_type'] === 'Bug') echo 'selected'; ?>>Bug</option>
                              <option value="Enhancement" <?php if ($issue['issue_type'] === 'Enhancement') echo 'selected'; ?>>Enhancement</option>
                              <option value="Task" <?php if ($issue['issue_type'] === 'Task') echo 'selected'; ?>>Task</option>
                             <option value="Improvement" <?php if ($issue['issue_type'] === 'Improvement') echo 'selected'; ?>>Improvement</option>
                        </select>
                   </div>
                   <div>
                       <label for="priority" class="block text-gray-700">Priority</label>
                         <select name="priority" id="priority" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                              <option value="Low" <?php if ($issue['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                               <option value="Medium" <?php if ($issue['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                                <option value="High" <?php if ($issue['priority'] === 'High') echo 'selected'; ?>>High</option>
                                 <option value="Critical" <?php if ($issue['priority'] === 'Critical') echo 'selected'; ?>>Critical</option>
                        </select>
                  </div>
                  <div>
                <label for="assigned_to" class="block text-gray-700">Assigned To</label>
                    <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                         <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                                  <option value="<?php echo $user['id']; ?>" <?php if ($issue['assigned_to'] === $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['username']); ?></option>
                             <?php endforeach; ?>
                   </select>
            </div>

                <div>
                    <label for="status" class="block text-gray-700">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="Open" <?php if ($issue['status'] === 'Open') echo 'selected'; ?>>Open</option>
                                 <option value="In Progress" <?php if ($issue['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                                <option value="Resolved" <?php if ($issue['status'] === 'Resolved') echo 'selected'; ?>>Resolved</option>
                                <option value="Closed" <?php if ($issue['status'] === 'Closed') echo 'selected'; ?>>Closed</option>
                                   <option value="Reopened" <?php if ($issue['status'] === 'Reopened') echo 'selected'; ?>>Reopened</option>
                         </select>
                  </div>
              </div>
            <div class="mb-4">
                <label for="steps_to_reproduce" class="block text-gray-700">Steps to Reproduce</label>
                <textarea name="steps_to_reproduce" id="steps_to_reproduce" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($issue['steps_to_reproduce']); ?></textarea>
             </div>
             <div class="mb-4">
                   <label for="environment_version" class="block text-gray-700">Environment/Version</label>
                <input type="text" name="environment_version" id="environment_version" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($issue['environment_version']); ?>">
             </div>
               <div class="mb-4">
                 <label for="resolution_date" class="block text-gray-700">Resolution Date</label>
                   <input type="date" name="resolution_date" id="resolution_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo date('Y-m-d\TH:i', strtotime($issue['resolution_date'])); ?>">
                </div>
   

            <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Update Issue</button>
        </form>
</div>
</div>