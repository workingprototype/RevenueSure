<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

// Fetch all project categories
$stmt = $conn->prepare("SELECT * FROM project_categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
                                <a href="<?php echo BASE_URL; ?>projects/categories/edit?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="<?php echo BASE_URL; ?>projects/categories/edit?id=<?php echo $category['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
         <div class="mt-4">
              <a href="<?php echo BASE_URL; ?>projects/categories/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Project Category</a>
         </div>
