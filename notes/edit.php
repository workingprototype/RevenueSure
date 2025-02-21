<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$note_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch note details
$stmt = $conn->prepare("SELECT * FROM notes WHERE id = :id");
$stmt->bindParam(':id', $note_id);
$stmt->execute();
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    header("Location: " . BASE_URL . "notes/manage");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $is_shared = isset($_POST['is_shared']) ? 1 : 0;
    $related_type = $_POST['related_type'] ?? null;
    $related_id = (!empty($_POST['related_id']) && $_POST['related_type'] != "") ? (int)$_POST['related_id'] : null;

    // Validate inputs
    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        // Update note
        $stmt = $conn->prepare("UPDATE notes SET title = :title, content = :content, category_id = :category_id, is_shared = :is_shared, related_type = :related_type, related_id = :related_id WHERE id = :id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':is_shared', $is_shared, PDO::PARAM_INT);
        $stmt->bindParam(':related_type', $related_type);
        $stmt->bindParam(':related_id', $related_id);
        $stmt->bindParam(':id', $note_id);

        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "notes/view?id=$note_id");
             exit();
        } else {
            $error = "Error updating note.";
        }
    }
}

// Fetch categories for the dropdown
$stmt = $conn->prepare("SELECT * FROM note_categories ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch leads, customers, and projects
$stmt = $conn->prepare("SELECT id, name FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM projects");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch tasks for dropdown
$stmt = $conn->prepare("SELECT id, description FROM tasks WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch support tickets for dropdown
$stmt = $conn->prepare("SELECT id, title FROM support_tickets WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Note</h1>

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

<!-- Edit Note Form -->
<div class="bg-white p-6 rounded-lg shadow-md">
<form method="POST" action="">
<?php echo csrfTokenInput(); ?>
    <div class="mb-4">
        <label for="title" class="block text-gray-700">Title</label>
        <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($note['title']); ?>" required>
    </div>
    <div class="mb-4">
        <label for="content" class="block text-gray-700">Content</label>
        <textarea name="content" id="content" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($note['content']); ?></textarea>
    </div>
    <div class="mb-4">
        <label for="category_id" class="block text-gray-700">Category</label>
        <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $note['category_id']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-4">
        <label for="related_type" class="block text-gray-700">Related To</label>
        <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showRelatedInput(this.value)">
            <option value="">None</option>
            <option value="lead" <?php if ($note['related_type'] === 'lead') echo 'selected'; ?>>Lead</option>
            <option value="customer" <?php if ($note['related_type'] === 'customer') echo 'selected'; ?>>Customer</option>
            <option value="project" <?php if ($note['related_type'] === 'project') echo 'selected'; ?>>Project</option>
            <option value="task" <?php if ($note['related_type'] === 'task') echo 'selected'; ?>>Task</option>
            <option value="support_ticket" <?php if ($note['related_type'] === 'support_ticket') echo 'selected'; ?>>Support Ticket</option>
        </select>
    </div>
    <!-- Dynamic Related ID Select -->
    <div id="related_id_container" class="mb-4 <?php echo empty($note['related_type']) ? 'hidden' : ''; ?>">
         <label for="related_id" class="block text-gray-700">Related ID</label>
         <select name="related_id" id="related_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">Select Record</option>
             <?php
                $relatedOptions = [];
                 switch ($note['related_type']) {
                    case 'lead':
                         $relatedOptions = $leads;
                         break;
                     case 'customer':
                          $relatedOptions = $customers;
                           break;
                     case 'project':
                          $relatedOptions = $projects;
                           break;
                        case 'task':
                             $relatedOptions = $tasks;
                              break;
                           case 'support_ticket':
                                $relatedOptions = $tickets;
                                break;
                   }
                  if ($relatedOptions) {
                       foreach ($relatedOptions as $option) {
                              $selected = ($option['id'] == $note['related_id']) ? 'selected' : '';
                                echo '<option value="' . $option['id'] . '" ' . $selected . '>' . htmlspecialchars($option['name'] ?? $option['description'] ?? $option['title']) . '</option>';
                         }
                   }
              ?>
         </select>
    </div>

    <div class="mb-4">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_shared" id="is_shared" class="mr-2" <?php if ($note['is_shared']) echo 'checked'; ?>>
            <span class="text-gray-700">Share with Team</span>
        </label>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Note</button>
</form>
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
     document.addEventListener('DOMContentLoaded', function() {
        showRelatedInput('<?php echo $note['related_type']; ?>');
     });
</script>