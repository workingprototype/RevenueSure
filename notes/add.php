<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';
$show_success_toast = false; // Initialize
$new_category_id = null; // for category addition

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title']) && !isset($_POST['add_category_name'])) { // Main note form
        $title = trim($_POST['title']);
        $content = $_POST['content']; // No sanitization here, as it's rich text.
        $category_id = $_POST['category_id'] ?: null;  // Allow null, handle empty string.
        $is_shared = isset($_POST['is_shared']) ? 1 : 0;
        $related_type = $_POST['related_type'] ?: null; // Allow null
        $related_id = (!empty($_POST['related_id']) && $_POST['related_type'] != "") ? (int)$_POST['related_id'] : null; // Nullable, and cast to int.

        if (empty($title) || empty($content)) {
            $error = "Title and content are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO notes (title, content, category_id, is_shared, related_type, related_id, created_by) VALUES (:title, :content, :category_id, :is_shared, :related_type, :related_id, :user_id)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);  // No htmlspecialchars on insert
            $stmt->bindParam(':category_id', $category_id, $category_id===null ? PDO::PARAM_NULL : PDO::PARAM_INT); 
            $stmt->bindParam(':is_shared', $is_shared, PDO::PARAM_INT);
            $stmt->bindParam(':related_type', $related_type, $related_type===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':related_id', $related_id, $related_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $success = "Note added successfully!";
                header("Location: " . BASE_URL . "notes/manage?success=true");
                exit();
            } else {
                $error = "Error adding note.";
            }
        }
    } elseif (isset($_POST['add_category_name'])) { // Category addition form
        $new_category_name = trim($_POST['add_category_name']);
        if (empty($new_category_name)) {
            $error = "Category name is required.";
        } else {
            $stmt = $conn->prepare("SELECT id FROM note_categories WHERE name = :name");
            $stmt->bindParam(':name', $new_category_name);
            $stmt->execute();
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $error = "A category with this name already exists.";
            } else {
                $stmt = $conn->prepare("INSERT INTO note_categories (name) VALUES (:name)");
                $stmt->bindParam(':name', $new_category_name);
                if ($stmt->execute()) {
                    $success = "Category added successfully!";
                    $show_success_toast = true;
                    $new_category_id = $conn->lastInsertId(); // Capture the new ID
                } else {
                    $error = "Error adding category.";
                }
            }
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
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">Add New Note</h2>
      </div>
      <a href="<?= BASE_URL; ?>notes/manage" 
         class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-purple-600 hover:to-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300">
        <!-- Left Arrow SVG Icon -->
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M21 12H3"></path>
        </svg>
        <span>Back to Manage</span>
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

    <!-- Add Note Form Card -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <form method="POST" action="">
        <?= csrfTokenInput(); ?>
        <div class="mb-4">
          <label for="title" class="block text-gray-700">Title</label>
          <input type="text" name="title" id="title" 
                 class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" 
                 required>
        </div>
        <div class="mb-4">
          <label for="content" class="block text-gray-700">Content</label>
          <!-- Use a rich text editor like ClassicEditor here -->
          <textarea name="content" id="content" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
        </div>
        <!-- Category -->
        <div class="mb-4">
          <label for="category_id" class="block text-gray-700">Category</label>
          <div class="relative">
            <select name="category_id" id="category_id" 
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
              <option value="">Select Category</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id']; ?>" <?php if($new_category_id == $category['id']) echo 'selected'; ?>>
                  <?= htmlspecialchars($category['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="button" onclick="openCategoryModal()" 
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 transition duration-200">
              <!-- Plus SVG Icon -->
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
        <!-- Related To -->
        <div class="mb-4">
          <label for="related_type" class="block text-gray-700">Related To</label>
          <select name="related_type" id="related_type" 
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" 
                  onchange="toggleRelatedFields(this.value)">
            <option value="">None</option>
            <option value="lead">Lead</option>
            <option value="customer">Customer</option>
            <option value="project">Project</option>
          </select>
        </div>
        <!-- Related ID (Conditional based on related_type) -->
        <div id="related_fields" class="mb-4 hidden">
          <!-- Leads Dropdown -->
          <div id="lead_select" class="mb-4 hidden">
            <label for="lead_id" class="block text-gray-700">Select Lead</label>
            <select name="related_id" id="lead_id" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
              <option value="">Select Lead</option>
              <?php foreach ($leads as $lead): ?>
                <option value="<?= $lead['id']; ?>"><?= htmlspecialchars($lead['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Customers Dropdown -->
          <div id="customer_select" class="mb-4 hidden">
            <label for="customer_id" class="block text-gray-700">Select Customer</label>
            <select name="related_id" id="customer_id" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
              <option value="">Select Customer</option>
              <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id']; ?>"><?= htmlspecialchars($customer['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Projects Dropdown -->
          <div id="project_select" class="mb-4 hidden">
            <label for="project_id" class="block text-gray-700">Select Project</label>
            <select name="related_id" id="project_id" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
              <option value="">Select Project</option>
              <?php foreach ($projects as $project): ?>
                <option value="<?= $project['id']; ?>"><?= htmlspecialchars($project['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <!-- Share with Team -->
        <div class="mb-4">
          <label class="inline-flex items-center">
            <input type="checkbox" name="is_shared" id="is_shared" class="mr-2">
            <span class="text-gray-700">Share with Team</span>
          </label>
        </div>
        <!-- Add Note Button -->
        <button type="submit" 
                class="flex items-center bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">
          <!-- Plus Icon SVG -->
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          <span>Add Note</span>
        </button>
      </form>
      <!-- Category Add Modal -->
      <div id="categoryModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
          </div>
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-headline">
                Add Notes Category
              </h3>
              <form method="POST" action="">
                <?= csrfTokenInput(); ?>
                <div class="mb-4">
                  <label for="add_category_name" class="block text-gray-700">Category Name</label>
                  <input type="text" name="add_category_name" id="add_category_name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="flex justify-end">
                  <button type="submit" class="bg-blue-700 text-white px-4 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">
                    Add Category
                  </button>
                  <button type="button" onclick="closeCategoryModal()" class="bg-gray-700 text-white px-4 py-3 rounded-xl hover:bg-gray-900 transition duration-300 shadow-md ml-2">
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
  }
  function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
  }
  function toggleRelatedFields(selectedType) {
    document.getElementById('lead_select').classList.add('hidden');
    document.getElementById('customer_select').classList.add('hidden');
    document.getElementById('project_select').classList.add('hidden');
    if(selectedType) {
      document.getElementById('related_fields').classList.remove('hidden');
      document.getElementById(selectedType + '_select').classList.remove('hidden');
    } else {
      document.getElementById('related_fields').classList.add('hidden');
    }
  }
  <?php if ($show_success_toast): ?>
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('toast').classList.remove('hidden');
      setTimeout(() => {
        document.getElementById('toast').classList.add('hidden');
      }, 3000);
    });
  <?php endif; ?>
  document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
        .create( document.querySelector('#content'), {
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'underline',
                'strikethrough',
                'link',
                'bulletedList',
                'numberedList',
                'blockQuote',
                '|',
                'code',
                'codeBlock',
                '|',
                'insertTable',
                'mediaEmbed',
                '|',
                'alignment',
                'fontBackgroundColor',
                'fontColor',
                'fontSize',
                '|',
                'undo',
                'redo'
            ],
            table: {
                contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
            }
        })
        .catch( error => {
            console.error( error );
        });
});
</script>
