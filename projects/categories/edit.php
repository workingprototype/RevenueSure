<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "projects/categories/manage");
    exit();
}

$category_id = $_GET['id'];

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM project_categories WHERE id = :id");
$stmt->bindParam(':id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: " . BASE_URL . "projects/categories/manage");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    // Validate input
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Check if the category already exists
        $stmt = $conn->prepare("SELECT id FROM project_categories WHERE name = :name AND id != :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $category_id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "A category with this name already exists.";
        } else {
            // Update the category
            $stmt = $conn->prepare("UPDATE project_categories SET name = :name WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':id', $category_id);

            if ($stmt->execute()) {
                $success = "Category updated successfully!";
                 header("Location: " . BASE_URL . "projects/categories/manage?success=true");
                exit();
            } else {
                $error = "Error updating category.";
            }
        }
    }
}


?>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Project Category</h1>

        <!-- Display error or success message -->
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                Category updated successfully!
            </div>
        <?php endif; ?>

        <!-- Edit Category Form -->
        <form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Category Name</label>
                <input type="text" name="name" id="name" value="<?php echo $category['name']; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Category</button>
              <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>projects/categories/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Project Categories</a>
            </div>
        </form>
