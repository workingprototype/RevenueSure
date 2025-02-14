<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

// Ensure note id is provided
if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "notes/manage");
    exit();
}

$note_id = $_GET['id'];

// Fetch the note to be edited
$stmt = $conn->prepare("SELECT * FROM notes WHERE id = :id AND created_by = :user_id");
$stmt->bindParam(':id', $note_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    $error = "Note not found or you are not authorized to edit this note.";
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $is_shared = isset($_POST['is_shared']) ? 1 : 0;
    $related_type = (isset($_POST['related_type']) && $_POST['related_type'] !== '') ? $_POST['related_type'] : null;
    $related_id = $_POST['related_id'] ?? null;

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $stmt = $conn->prepare("UPDATE notes SET title = :title, content = :content, category_id = :category_id, is_shared = :is_shared, related_type = :related_type, related_id = :related_id WHERE id = :id AND created_by = :user_id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':is_shared', $is_shared, PDO::PARAM_INT);
        $stmt->bindParam(':related_type', $related_type);
        $stmt->bindParam(':related_id', $related_id);
        $stmt->bindParam(':id', $note_id);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            $success = "Note updated successfully!";
            header("Location: " . BASE_URL . "notes/manage?success=true");
            exit();
        } else {
            $error = "Error updating note.";
        }
    }
}

// Use submitted values if available; otherwise, use note values from the database
$title_value        = isset($_POST['title']) ? $_POST['title'] : $note['title'];
$content_value      = isset($_POST['content']) ? $_POST['content'] : $note['content'];
$category_value     = isset($_POST['category_id']) ? $_POST['category_id'] : $note['category_id'];
$is_shared_value    = isset($_POST['is_shared']) ? 1 : $note['is_shared'];
$related_type_value = isset($_POST['related_type']) ? $_POST['related_type'] : $note['related_type'];

// Fetch categories for the dropdown
$stmt = $conn->prepare("SELECT * FROM note_categories ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 p-8">
  <div class="max-w-6xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
      <div>
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
          Digital Notebook
        </h1>
        <p class="text-gray-600 mt-2">Your thoughts, organized beautifully</p>
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">Edit Note</h2>
      </div>
      <a href="<?= BASE_URL; ?>notes/manage" 
         class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-purple-600 hover:to-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300">
        <!-- Left Arrow SVG Icon -->
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"></path>
        </svg>
        <span>Back to Notes</span>
      </a>
    </div>

    <!-- Alert Messages -->
    <?php if ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?= $error; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?= $success; ?>
      </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <form method="POST" action="">
        <?= csrfTokenInput(); ?>
        <div class="mb-4">
          <label for="title" class="block text-gray-700">Title</label>
          <input type="text" name="title" id="title" 
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" 
                 required value="<?= htmlspecialchars($title_value); ?>">
        </div>
        <div class="mb-4">
          <label for="content" class="block text-gray-700">Content</label>
          <!-- The textarea will be enhanced with ClassicEditor -->
          <textarea name="content" id="content" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" 
                    required><?= htmlspecialchars($content_value); ?></textarea>
        </div>
        <div class="mb-4">
          <label for="category_id" class="block text-gray-700">Category</label>
          <select name="category_id" id="category_id" 
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" 
                  required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id']; ?>" <?= ($category['id'] == $category_value) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($category['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-4">
          <label for="related_type" class="block text-gray-700">Related To</label>
          <select name="related_type" id="related_type" 
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
            <option value="">None</option>
            <option value="lead" <?= ($related_type_value === 'lead') ? 'selected' : ''; ?>>Lead</option>
            <option value="customer" <?= ($related_type_value === 'customer') ? 'selected' : ''; ?>>Customer</option>
            <option value="project" <?= ($related_type_value === 'project') ? 'selected' : ''; ?>>Project</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="inline-flex items-center">
            <input type="checkbox" name="is_shared" id="is_shared" class="mr-2" <?= ($is_shared_value == 1) ? 'checked' : ''; ?>>
            <span class="text-gray-700">Share with Team</span>
          </label>
        </div>
        <button type="submit" 
                class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
          <!-- Check Mark SVG Icon -->
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <span>Update Note</span>
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Initialize ClassicEditor on the content textarea -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
    })
    .catch(error => {
        console.error(error);
    });
});
</script>
