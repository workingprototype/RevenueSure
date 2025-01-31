<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_knowledge_base.php");
    exit();
}

$article_id = $_GET['id'];

// Fetch article details with category
$stmt = $conn->prepare("SELECT * FROM knowledge_base_articles WHERE id = :id");
$stmt->bindParam(':id', $article_id);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: manage_knowledge_base.php");
    exit();
}

$error = '';
$success = '';
$show_success_toast = false;
$new_category_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title']) && !isset($_POST['add_category_name'])) {
            $title = trim($_POST['title']);
            $content = $_POST['content'];
            $category_id = $_POST['category_id'];
            $visibility = $_POST['visibility'];

           if (empty($title) || empty($content) || empty($category_id) || empty($visibility)) {
                $error = "All fields are required.";
            } else {
                // Update the article
                $stmt = $conn->prepare("UPDATE knowledge_base_articles SET title = :title, content = :content, category_id = :category_id, visibility = :visibility WHERE id = :id");
                $stmt->bindParam(':id', $article_id);
                 $stmt->bindParam(':title', $title);
                $stmt->bindParam(':content', $content);
               $stmt->bindParam(':category_id', $category_id);
                  $stmt->bindParam(':visibility', $visibility);
               if ($stmt->execute()) {
                  $success = "Article updated successfully!";
                     header("Location: view_knowledge_base_article.php?id=$article_id&success=true");
                         exit();
                  } else {
                     $error = "Error updating article.";
                    }
             }
     }
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
// Fetch categories for the dropdown
$stmt = $conn->prepare("SELECT * FROM knowledge_base_categories ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<div class="container mx-auto mt-10 p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Edit Knowledge Base Article</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                  Article updated successfully!
            </div>
         <?php endif; ?>
       <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($article['title']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700">Content</label>
                <textarea name="content" id="content" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($article['content']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700">Category</label>
                    <div class="relative">
                       <select name="category_id" id="category_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                             <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                      <option value="<?php echo $category['id']; ?>" <?php if($new_category_id == $category['id'] || $article['category_id'] == $category['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($category['name']); ?></option>
                                 <?php endforeach; ?>
                       </select>
                      <button type="button"  onclick="openCategoryModal()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 transition duration-200">
                              <i class="fas fa-plus-circle"></i>
                         </button>
                 </div>
            </div>
           <div class="mb-4">
              <label for="visibility" class="block text-gray-700">Visibility</label>
               <select name="visibility" id="visibility" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                  <option value="all" <?php if($article['visibility'] == 'all') echo 'selected'; ?>>All Employees</option>
                   <option value="team" <?php if($article['visibility'] == 'team') echo 'selected'; ?>>Specific Teams</option>
                     <option value="admin" <?php if($article['visibility'] == 'admin') echo 'selected'; ?>>Admins Only</option>
                </select>
           </div>
             <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Update Article</button>
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
                             Add Knowledge Base Category
                             </h3>
                           <form method="POST" action="">
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
         <div class="mt-4">
              <a href="view_knowledge_base_article.php?id=<?php echo $article_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Article</a>
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
       document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create( document.querySelector( '#content' ), {
                   toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable','undo', 'redo'],
                      heading: {
                        options: [
                             { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                              { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                         ]
                    }
                    })
                 .catch( error => {
                      console.error( error );
                     } );
            });
 </script>
<?php
// Include footer
require 'footer.php';
?>