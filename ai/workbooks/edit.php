<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$workbook_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch workbook details
$stmt = $conn->prepare("SELECT * FROM ai_workbooks WHERE id = :id");
$stmt->bindParam(':id', $workbook_id);
$stmt->execute();
$workbook = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$workbook) {
    header("Location: " . BASE_URL . "ai/workbooks/manage");
    exit();
}
 // Fetch leads, customers, and projects for the "Related to" dropdown
$stmt = $conn->prepare("SELECT id, name FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM projects WHERE project_manager_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = $_POST['description'];
    $related_type = $_POST['related_type'] ?? null;
    $related_id = (!empty($_POST['related_id']) && $_POST['related_type'] != "") ? (int)$_POST['related_id'] : null;

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        $stmt = $conn->prepare("UPDATE ai_workbooks SET title = :title, description = :description, related_type = :related_type, related_id = :related_id WHERE id = :id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':related_type', $related_type);
        $stmt->bindParam(':related_id', $related_id);
        $stmt->bindParam(':id', $workbook_id);

        if ($stmt->execute()) {
           header("Location: " . BASE_URL . "ai/workbooks/manage?success=true");
           exit();
        } else {
            $error = "Error updating workbook.";
        }
    }
}

?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Workbook</h1>

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
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Workbook Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($workbook['title']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description (Optional)</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($workbook['description']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="related_type" class="block text-gray-700">Related To (Optional)</label>
                <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                    <option value="">None</option>
                    <option value="customer" <?php if ($workbook['related_type'] === 'customer') echo 'selected'; ?>>Customer</option>
                    <option value="project" <?php if ($workbook['related_type'] === 'project') echo 'selected'; ?>>Project</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Workbook</button>
        </form>
    </div>
</div>