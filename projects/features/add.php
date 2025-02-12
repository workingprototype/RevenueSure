<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

function generateFeatureId($conn) {
    $stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM project_features");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_id = ($result['max_id'] ?? 0) + 1;
    return 'FEAT-' . date('Ymd') . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
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
    $feature_id = generateFeatureId($conn);
    $created_by = $_SESSION['user_id'];

     if (empty($feature_title) || empty($description) || empty($priority) || empty($status) || empty($owner_id)) {
            $error = "All fields are required.";
     }  else{
         $stmt = $conn->prepare("INSERT INTO project_features (project_id, feature_id, feature_title, description, priority, status, owner_id, estimated_completion_date, created_by) VALUES (:project_id, :feature_id, :feature_title, :description, :priority, :status, :owner_id, :estimated_completion_date, :created_by)");
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':feature_id', $feature_id);
         $stmt->bindParam(':feature_title', $feature_title);
        $stmt->bindParam(':description', $description);
         $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->bindParam(':estimated_completion_date', $estimated_completion_date);
        $stmt->bindParam(':created_by', $created_by);
        if ($stmt->execute()) {
             $success = "Feature added successfully!";
                header("Location: " . BASE_URL . "projects/view?id=$project_id&success=true");
                 exit();
         } else {
              $error = "Error adding feature.";
            }
       }
}
// Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Add Feature to Project: <?php echo htmlspecialchars($project['name']); ?></h1>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
          <div class="mb-4">
                <label for="feature_title" class="block text-gray-700">Feature Title</label>
                <input type="text" name="feature_title" id="feature_title" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                 <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                      <label for="priority" class="block text-gray-700">Priority</label>
                      <select name="priority" id="priority" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                           <option value="Low">Low</option>
                           <option value="Medium">Medium</option>
                           <option value="High">High</option>
                           <option value="Critical">Critical</option>
                       </select>
                </div>
                <div>
                     <label for="status" class="block text-gray-700">Status</label>
                     <select name="status" id="status" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                            <option value="Planned">Planned</option>
                              <option value="In Progress">In Progress</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Completed">Completed</option>
                            <option value="Deferred">Deferred</option>
                      </select>
                 </div>
            </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="owner_id" class="block text-gray-700">Owner</label>
                   <select name="owner_id" id="owner_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                      <option value="">Select User</option>
                         <?php foreach ($users as $user): ?>
                               <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                           <?php endforeach; ?>
                   </select>
           </div>
           <div>
                  <label for="estimated_completion_date" class="block text-gray-700">Estimated Completion Date</label>
                    <input type="datetime-local" name="estimated_completion_date" id="estimated_completion_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
        </div>

        <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Feature</button>
    </form>
</div>
</div>