<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all project categories
$stmt = $conn->prepare("SELECT * FROM project_categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Project Categories</h1>

        <!-- Categories Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo $category['id']; ?></td>
                            <td class="px-4 py-2"><?php echo $category['name']; ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_project_category.php?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_project_category.php?id=<?php echo $category['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
         <div class="mt-4">
              <a href="add_project_category.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Project Category</a>
         </div>
<?php
// Include footer
require 'footer.php';
?>