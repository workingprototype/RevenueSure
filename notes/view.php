<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$note_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Process inline update if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_inline'])) {
    $title       = trim($_POST['title']);
    $content     = $_POST['content'];
    $category_id = $_POST['category_id'];
    $is_shared   = isset($_POST['is_shared']) ? 1 : 0;
    $related_type = (isset($_POST['related_type']) && $_POST['related_type'] !== '') ? $_POST['related_type'] : null;
    $related_id   = (isset($_POST['related_id']) && $_POST['related_id'] !== '') ? $_POST['related_id'] : null;
    
    if(empty($title) || empty($content)){
        $error = "Title and content are required.";
    } else {
        $stmt = $conn->prepare("UPDATE notes SET title = :title, content = :content, category_id = :category_id, is_shared = :is_shared, related_type = :related_type, related_id = :related_id WHERE id = :id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':is_shared', $is_shared, PDO::PARAM_INT);
        $stmt->bindParam(':related_type', $related_type);
        $stmt->bindParam(':related_id', $related_id);
        $stmt->bindParam(':id', $note_id);
    
        if ($stmt->execute()) {
            $success = "Note updated successfully!";
        } else {
            $error = "Error updating note.";
        }
    }
}

// Fetch note details
$stmt = $conn->prepare("SELECT notes.*, note_categories.name as category_name FROM notes LEFT JOIN note_categories ON notes.category_id = note_categories.id WHERE notes.id = :id");
$stmt->bindParam(':id', $note_id);
$stmt->execute();
$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    header("Location: " . BASE_URL . "notes/manage");
    exit();
}

// Fetch categories for the inline editing dropdown
$stmt = $conn->prepare("SELECT * FROM note_categories ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If the note has a related entity, fetch its name and build a clickable link
$related_display = "";
if ($note['related_type'] && $note['related_id']) {
    if ($note['related_type'] === 'lead') {
        $stmt = $conn->prepare("SELECT name FROM leads WHERE id = :id");
        $stmt->bindParam(':id', $note['related_id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $related_display = '<a href="' . BASE_URL . 'leads/view?id=' . $note['related_id'] . '">' . htmlspecialchars($row['name']) . '</a>';
        }
    } elseif ($note['related_type'] === 'customer') {
        $stmt = $conn->prepare("SELECT name FROM customers WHERE id = :id");
        $stmt->bindParam(':id', $note['related_id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $related_display = '<a href="' . BASE_URL . 'customers/view?id=' . $note['related_id'] . '">' . htmlspecialchars($row['name']) . '</a>';
        }
    } elseif ($note['related_type'] === 'project') {
        $stmt = $conn->prepare("SELECT name FROM projects WHERE id = :id");
        $stmt->bindParam(':id', $note['related_id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $related_display = '<a href="' . BASE_URL . 'projects/view?id=' . $note['related_id'] . '">' . htmlspecialchars($row['name']) . '</a>';
        }
    }
}
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
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">View Note</h2>
      </div>
      <div class="flex space-x-4">
        <!-- Edit Inline Button with Pencil Icon -->
        <button onclick="toggleEditMode()" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
          </svg>
          <span>Edit Inline</span>
        </button>
        <!-- Back to Manage Button with Left Arrow Icon -->
        <a href="<?= BASE_URL; ?>notes/manage" class="flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M21 12H3"></path>
          </svg>
          <span>Back To Your Notes</span>
        </a>
      </div>
    </div>

    <!-- Alerts -->
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

    <!-- View Mode: Display note details -->
    <div id="viewMode" class="bg-white p-6 rounded-2xl shadow-sm transition-shadow duration-300 border border-gray-100">
      <div class="space-y-3">
        <p><strong>Title:</strong> <?php echo htmlspecialchars($note['title']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($note['category_name']); ?></p>
        <p><strong>Content:</strong><br>
          <!-- Render the rich text as HTML -->
          <?php echo $note['content']; ?>
        </p>
        <p><strong>Is Shared:</strong> <?php echo $note['is_shared'] ? 'Yes' : 'No'; ?></p>
        <?php if ($related_display): ?>
          <p><strong>Related to:</strong> <?php echo $related_display; ?></p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Edit Mode: Inline editing form (hidden by default) -->
    <div id="editMode" class="hidden bg-white p-6 rounded-2xl shadow-sm transition-shadow duration-300 border border-gray-100 mt-6">
      <form method="POST" action="">
        <?= csrfTokenInput(); ?>
        <input type="hidden" name="update_inline" value="1">
        <div class="mb-4">
          <label for="edit_title" class="block text-gray-700">Title</label>
          <input type="text" name="title" id="edit_title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required value="<?= htmlspecialchars($note['title']); ?>">
        </div>
        <div class="mb-4">
          <label for="edit_content" class="block text-gray-700">Content</label>
          <!-- The textarea will be replaced by ClassicEditor -->
          <textarea name="content" id="edit_content" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required><?= $note['content']; ?></textarea>
        </div>
        <div class="mb-4">
          <label for="edit_category_id" class="block text-gray-700">Category</label>
          <select name="category_id" id="edit_category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id']; ?>" <?= ($category['id'] == $note['category_id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($category['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-4">
          <label for="edit_related_type" class="block text-gray-700">Related To</label>
          <select name="related_type" id="edit_related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">None</option>
            <option value="lead" <?= ($note['related_type'] === 'lead') ? 'selected' : ''; ?>>Lead</option>
            <option value="customer" <?= ($note['related_type'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
            <option value="project" <?= ($note['related_type'] === 'project') ? 'selected' : ''; ?>>Project</option>
          </select>
        </div>
        <div class="mb-4">
          <label class="inline-flex items-center">
            <input type="checkbox" name="is_shared" id="edit_is_shared" class="mr-2" <?= ($note['is_shared']) ? 'checked' : ''; ?>>
            <span class="text-gray-700">Share with Team</span>
          </label>
        </div>
        <div class="flex space-x-4">
          <!-- Save Changes Button with Check Mark Icon -->
          <button type="submit" class="flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Save Changes</span>
          </button>
          <!-- Cancel Button with Cross Icon -->
          <button type="button" onclick="toggleEditMode()" class="flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>Cancel</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  let inlineEditor = null;
  function toggleEditMode() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    viewMode.classList.toggle('hidden');
    editMode.classList.toggle('hidden');
    // Initialize ClassicEditor for inline edit mode if not already created
    if (!editMode.classList.contains('hidden') && !inlineEditor) {
      ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: [
            'heading', '|',
            'bold', 'italic', 'link', 'codeBlock', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'
        ]
    })
    .then(editor => {
        console.log('Editor initialized', editor);
    })
    .catch(error => {
        console.error(error);
    });

    }
  }
</script>
