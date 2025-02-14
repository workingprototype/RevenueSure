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
        $related_id = (!empty($_POST['related_id']) && $_POST['related_type'] != "") ? (int)$_POST['related_id'] : null; // Nullable, and cast to int.  Important!


        if (empty($title) || empty($content)) {
            $error = "Title and content are required.";
        }  else {

            $stmt = $conn->prepare("INSERT INTO notes (title, content, category_id, is_shared, related_type, related_id, created_by) VALUES (:title, :content, :category_id, :is_shared, :related_type, :related_id, :user_id)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);  // No htmlspecialchars on insert
            $stmt->bindParam(':category_id', $category_id, $category_id===null ? PDO::PARAM_NULL : PDO::PARAM_INT); // Handle possible NULL.  Important!
            $stmt->bindParam(':is_shared', $is_shared, PDO::PARAM_INT);
            $stmt->bindParam(':related_type', $related_type, $related_type===null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':related_id', $related_id, $related_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT); // Handle possible NULL, and correct type.
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                $success = "Note added successfully!";
                  header("Location: " . BASE_URL . "notes/manage?success=true"); //Redirect needs to be added.
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

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Note</h1>

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

    <!-- Add Note Form -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
              <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700">Content</label>
                <!-- Use a rich text editor like CKEditor here -->
                <textarea name="content" id="content" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
            <!-- Category -->
           <div class="mb-4">
                <label for="category_id" class="block text-gray-700">Category</label>
                <div class="relative">
                    <select name="category_id" id="category_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                        <option value="">Select Category</option>
                         <?php foreach ($categories as $category): ?>
                              <option value="<?php echo $category['id']; ?>" <?php if($new_category_id == $category['id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                           <?php endforeach; ?>
                   </select>
                   <button type="button"  onclick="openCategoryModal()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 transition duration-200">
                             <i class="fas fa-plus-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Related To -->
           <div class="mb-4">
            <label for="related_type" class="block text-gray-700">Related To</label>
                <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" onchange="toggleRelatedFields(this.value)">
                   <option value="">None</option>
                    <option value="lead">Lead</option>
                     <option value="customer">Customer</option>
                     <option value="project">Project</option>
                </select>
            </div>

            <!-- Related ID (Conditional based on related_type) -->
            <div id="related_fields" class="mb-4 hidden">
                <!--  Leads Dropdown -->
                   <div id="lead_select" class="mb-4 hidden">
                    <label for="lead_id" class="block text-gray-700">Select Lead</label>
                    <select name="related_id" id="lead_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                        <option value="">Select Lead</option>
                          <?php foreach ($leads as $lead): ?>
                               <option value="<?php echo $lead['id']; ?>"><?php echo htmlspecialchars($lead['name']); ?></option>
                           <?php endforeach; ?>
                    </select>
                </div>
                <!-- Customers Dropdown -->
              <div id="customer_select" class="mb-4 hidden">
                <label for="customer_id" class="block text-gray-700">Select Customer</label>
                 <select name="related_id" id="customer_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                     <option value="">Select Customer</option>
                     <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                    <?php endforeach; ?>
                 </select>
              </div>
               <!-- Project Dropdown -->
              <div id="project_select" class="mb-4 hidden">
               <label for="project_id" class="block text-gray-700">Select Project</label>
                <select name="related_id" id="project_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                  <option value="">Select Project</option>
                 <?php foreach ($projects as $project): ?>
                       <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                   <?php endforeach; ?>
                 </select>
             </div>
          </div>

          <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_shared" id="is_shared" class="mr-2">
                   <span class="text-gray-700">Share with Team</span>
                  </label>
            </div>
            <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Note</button>
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
                             <?php echo csrfTokenInput(); ?>
                                  <div class="mb-4">
                                       <label for="add_category_name" class="block text-gray-700">Category Name</label>
                                          <input type="text" name="add_category_name" id="add_category_name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                                     </div>
                                      <div class="flex justify-end">
                                            <button type="submit"  class="bg-blue-700 text-white px-4 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Category</button>
                                             <button type="button" onclick="closeCategoryModal()" class="bg-gray-700 text-white px-4 py-3 rounded-xl hover:bg-gray-900 transition duration-300 shadow-md ml-2">Cancel</button>
                                        </div>
                                </form>
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

         function closeToast(){
             document.getElementById('toast').classList.add('hidden');
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
            .create( document.querySelector( '#content' ), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable','undo', 'redo'],
                 } )
             .catch( error => {
                console.error( error );
             } );
            });
</script>