<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all categories including parent categories
$stmt = $conn->prepare("
    SELECT kb1.id, kb1.name, kb2.name as parent_name, kb1.created_at FROM knowledge_base_categories kb1
 LEFT JOIN knowledge_base_categories kb2 ON kb1.parent_id = kb2.id
        ORDER BY kb1.created_at DESC
");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

    <div class="container mx-auto p-6 fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Knowledge Base Categories</h1>

        <!-- Add Category Button -->
          <div class="flex justify-between items-center mb-8">
            <a href="add_knowledge_base_category.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
                  <i class="fas fa-plus-circle mr-2"></i> Add Category
               </a>
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
                            <a href="edit_knowledge_base_category.php?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                            <a href="delete_knowledge_base_category.php?id=<?php echo $category['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
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
    </div>
<?php
// Include footer
require 'footer.php';
?>