<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

// Fetch leads, customers, and projects for the "Related to" dropdown
$stmt = $conn->prepare("SELECT id, name FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM projects WHERE project_manager_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $related_type = $_POST['related_type'] ?? null;
    $related_id = (!empty($_POST['related_id']) && $_POST['related_type'] != "") ? (int)$_POST['related_id'] : null;

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO ai_workbooks (user_id, title, description, related_type, related_id) VALUES (:user_id, :title, :description, :related_type, :related_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':related_type', $related_type);
        $stmt->bindParam(':related_id', $related_id);

        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "ai/workbooks/manage?success=true");
            exit();
        } else {
            $error = "Error creating workbook.";
        }
    }
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Workbook</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Workbook Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description (Optional)</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
            <div class="mb-4">
                <label for="related_type" class="block text-gray-700">Related To (Optional)</label>
                <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">None</option>
                    <option value="customer">Customer</option>
                    <option value="project">Project</option>
                </select>
            </div>
             <div id="related_id_container" class="mb-4 hidden">
              <label for="related_id" class="block text-gray-700">Select Related</label>
               <select name="related_id" id="related_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                      <option value="">Select</option>
                      <?php if ($related_type === 'customer'): ?>
                         <?php foreach ($customers as $customer): ?>
                                 <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                            <?php endforeach; ?>
                         <?php elseif ($related_type === 'project'): ?>
                              <?php foreach ($projects as $project): ?>
                                     <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                                <?php endforeach; ?>
                       <?php endif; ?>
                 </select>
             </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Create Workbook</button>
        </form>
    </div>
</div>
<script>
 function showRelatedInput(type) {
    var relatedIdContainer = document.getElementById('related_id_container');
     if (type) {
            relatedIdContainer.classList.remove('hidden');
        } else {
             relatedIdContainer.classList.add('hidden');
         }
    }
</script>