<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

// Fetch all categories including parent categories
$stmt = $conn->prepare("
    SELECT kb1.id, kb1.name, kb2.name as parent_name, kb1.created_at FROM knowledge_base_categories kb1
 LEFT JOIN knowledge_base_categories kb2 ON kb1.parent_id = kb2.id
        ORDER BY kb1.created_at DESC
");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$new_category_id = null;

$show_success_toast = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
        if (isset($_POST['add_category_name'])) {
              $new_category_name = trim($_POST['add_category_name']);
                 if (empty($new_category_name)) {
                     $error = "Category name is required.";
                    } else {
                      // Check if the category already exists
                      $stmt = $conn->prepare("SELECT id FROM knowledge_base_categories WHERE name = :name");
                      $stmt->bindParam(':name', $new_category_name);
                      $stmt->execute();
                          if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                              $error = "A category with this name already exists.";
                         } else {
                            // Insert the category
                               $stmt = $conn->prepare("INSERT INTO knowledge_base_categories (name) VALUES (:name)");
                                  $stmt->bindParam(':name', $new_category_name);
                                 if ($stmt->execute()) {
                                        $success = "Category added successfully!";
                                        $show_success_toast = true;
                                       $new_category_id = $conn->lastInsertId();
                                    } else {
                                         $error = "Error adding category.";
                                     }
                             }
                 }
         }
}

?>
    <div class="container mx-auto p-6 fade-in">
         <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Knowledge Base Categories</h1>
          <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
           <?php endif; ?>
         
         <!-- Add Category Button -->
         <div class="flex justify-between items-center mb-8">
            <button onclick="openCategoryModal()" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
               <i class="fas fa-plus-circle mr-2"></i> Add Category
             </button>
        </div>
        <!-- Categories Table -->
        <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
            <table class="w-full text-left">
                <thead>
                   <tr class="bg-gray-200 text-gray-700">
                        <th class="px-4 py-3">Name</th>
                         <th class="px-4 py-3">Parent Category</th>
                         <th class="px-4 py-3">Actions</th>
                     </tr>
                 </thead>
              <tbody>
                    <?php if ($categories): ?>
                        <?php foreach ($categories as $category): ?>
                             <tr class="border-b border-gray-300">
                                 <td class="px-4 py-3"><?php echo htmlspecialchars($category['name']); ?></td>
                                 <td class="px-4 py-3"><?php echo htmlspecialchars($category['parent_name'] ? $category['parent_name'] : 'N/A' ); ?></td>
                                    <td class="px-4 py-3 flex gap-2">
                                         <a href="<?php echo BASE_URL; ?>knowledge_base/categories/edit?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                         <a href="<?php echo BASE_URL; ?>knowledge_base/categories/delete?id=<?php echo $category['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
                                   </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                       <tr>
                           <td colspan="3" class="px-4 py-2 text-center text-gray-600">No categories found.</td>
                        </tr>
                    <?php endif; ?>
              </tbody>
         </table>
     </div>
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
                                      Add Knowledge Base Category
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
         <!-- Success Toast -->
           <div
              id="toast"
             class="fixed top-12 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 hidden"
               role="alert"
              >
             <div class="flex items-center">
                  <span class="mr-2"><i class="fas fa-check-circle"></i></span>
                    <span>Category added successfully!</span>
                       <button onclick="closeToast()" class="ml-2 text-gray-600 hover:text-gray-800" > <i class="fas fa-times"></i></button>
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
     <?php if ($show_success_toast): ?>
          document.addEventListener('DOMContentLoaded', function() {
                 document.getElementById('toast').classList.remove('hidden');
                setTimeout(() => {
                        document.getElementById('toast').classList.add('hidden');
                 }, 3000);
            });
    <?php endif; ?>
 </script>
