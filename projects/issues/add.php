<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;
function generateIssueId($conn) {
    $stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM project_issues");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_id = ($result['max_id'] ?? 0) + 1;
    return 'ISSUE-' . date('Ymd') . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
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
    $steps_to_reproduce = $_POST['steps_to_reproduce'];
    $environment_version = trim($_POST['environment_version']);
     $resolution_date = $_POST['resolution_date'] ?? null;
    $reported_by = $_SESSION['user_id'];

    if (empty($issue_title) || empty($description) || empty($issue_type) || empty($priority) || empty($steps_to_reproduce) ) {
        $error = "All fields are required.";
    } else {
        $issue_id = generateIssueId($conn);
        $stmt = $conn->prepare("INSERT INTO project_issues (project_id, issue_id, issue_title, description, issue_type, priority, assigned_to, related_feature_id, steps_to_reproduce, environment_version, reported_by, resolution_date) VALUES (:project_id, :issue_id, :issue_title, :description, :issue_type, :priority, :assigned_to, :related_feature_id, :steps_to_reproduce, :environment_version, :reported_by, :resolution_date)");
        $stmt->bindParam(':project_id', $project_id);
           $stmt->bindParam(':issue_id', $issue_id);
        $stmt->bindParam(':issue_title', $issue_title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':issue_type', $issue_type);
        $stmt->bindParam(':priority', $priority);
          $stmt->bindParam(':assigned_to', $assigned_to);
           $stmt->bindParam(':related_feature_id', $related_feature_id);
           $stmt->bindParam(':steps_to_reproduce', $steps_to_reproduce);
          $stmt->bindParam(':environment_version', $environment_version);
        $stmt->bindParam(':reported_by', $reported_by);
         $stmt->bindParam(':resolution_date', $resolution_date);
        if ($stmt->execute()) {
            $success = "Issue added successfully!";
             header("Location: " . BASE_URL . "projects/issues/manage?project_id=$project_id&success=true");
            exit();
         } else {
             $error = "Error adding issue.";
         }
     }

}
// Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Report Issue For: <?php echo htmlspecialchars($project['name']); ?></h1>
       <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
      <!-- Report Issue Form -->
     <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
              <div class="mb-4">
                <label for="issue_title" class="block text-gray-700">Issue Title</label>
                  <input type="text" name="issue_title" id="issue_title" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
              </div>
               <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description</label>
                        <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required></textarea>
               </div>
               <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                   <div>
                       <label for="issue_type" class="block text-gray-700">Issue Type</label>
                           <select name="issue_type" id="issue_type" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                               <option value="Bug">Bug</option>
                                <option value="Enhancement">Enhancement</option>
                                <option value="Task">Task</option>
                                 <option value="Improvement">Improvement</option>
                             </select>
                   </div>
                   <div>
                       <label for="priority" class="block text-gray-700">Priority</label>
                           <select name="priority" id="priority" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                               <option value="Low">Low</option>
                                 <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                 <option value="Critical">Critical</option>
                            </select>
                      </div>
               </div>
              <div class="mb-4">
                 <label for="related_feature_id" class="block text-gray-700">Related Feature (Optional)</label>
                   <select name="related_feature_id" id="related_feature_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select Feature</option>
                            <?php foreach ($features as $feature): ?>
                                   <option value="<?php echo $feature['id']; ?>"><?php echo htmlspecialchars($feature['feature_title']); ?></option>
                             <?php endforeach; ?>
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
                    <label for="steps_to_reproduce" class="block text-gray-700">Steps to Reproduce</label>
                        <textarea name="steps_to_reproduce" id="steps_to_reproduce" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                </div>
             <div class="mb-4">
                  <label for="environment_version" class="block text-gray-700">Environment/Version</label>
                   <input type="text" name="environment_version" id="environment_version" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
        <div class="mb-4">
              <label for="resolution_date" class="block text-gray-700">Expected Resolution Date</label>
               <input type="datetime-local" name="resolution_date" id="resolution_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
    <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Report Issue</button>
    </form>
</div>
</div>