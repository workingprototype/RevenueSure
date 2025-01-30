<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all task templates
$stmt = $conn->prepare("SELECT * FROM task_templates");
$stmt->execute();
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Task Templates</h1>

    <!-- Add Task Template Button -->
    <div class="mb-8">
        <a href="add_task_template.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Task Template</a>
     </div>
        <!-- Task Templates Table -->
     <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
             <thead class="bg-gray-50">
                <tr>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Template Name</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Description</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php if ($templates): ?>
                   <?php foreach ($templates as $template): ?>
                       <tr class="border-b transition hover:bg-gray-100">
                        <td class="px-4 py-3"><?php echo htmlspecialchars($template['name']); ?></td>
                         <td class="px-4 py-3"><?php echo htmlspecialchars($template['description']); ?></td>
                       <td class="px-4 py-3 flex gap-2">
                             <a href="edit_task_template.php?id=<?php echo $template['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                             <a href="delete_task_template.php?id=<?php echo $template['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
                           </td>
                      </tr>
                   <?php endforeach; ?>
              <?php else: ?>
                    <tr>
                         <td colspan="3" class="px-4 py-2 text-center text-gray-600">No task templates found.</td>
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