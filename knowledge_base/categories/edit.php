<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "knowledge_base/categories/manage");
    exit();
}

$category_id = $_GET['id'];

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM knowledge_base_categories WHERE id = :id");
$stmt->bindParam(':id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: " . BASE_URL . "knowledge_base/categories/manage");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
     $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

    // Validate input
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
         // Check if the category already exists
            $stmt = $conn->prepare("SELECT id FROM knowledge_base_categories WHERE name = :name AND id != :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':id', $category_id);
           $stmt->execute();
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                  $error = "A category with this name already exists.";
            } else {
                $stmt = $conn->prepare("UPDATE knowledge_base_categories SET name = :name, parent_id = :parent_id WHERE id = :id");
                $stmt->bindParam(':name', $name);
               $stmt->bindParam(':parent_id', $parent_id);
                $stmt->bindParam(':id', $category_id);
                 if ($stmt->execute()) {
                    $success = "Category updated successfully!";
                    header("Location: " . BASE_URL . "knowledge_base/categories/manage?success=true");
                      exit();
                 } else {
                    $error = "Error updating category.";
              }
          }
    }
}

// Fetch all parent categories
$stmt = $conn->prepare("SELECT id, name FROM knowledge_base_categories WHERE id != :id ORDER BY name ASC");
$stmt->bindParam(':id', $category_id);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

    <div class="container mx-auto p-6 fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Knowledge Base Category</h1>
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
         <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
          <form method="POST" action="">
          <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
              <label for="name" class="block text-gray-700">Category Name</label>
                 <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($category['name']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="parent_id" class="block text-gray-700">Parent Category (Optional)</label>
                   <select name="parent_id" id="parent_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                       <option value="">Select Parent Category</option>
                           <?php foreach ($categories as $parent): ?>
                               <option value="<?php echo $parent['id']; ?>" <?php if ($category['parent_id'] == $parent['id']) echo 'selected'; ?>><?php echo htmlspecialchars($parent['name']); ?></option>
                           <?php endforeach; ?>
                  </select>
              </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 uppercase tracking-wide">Update Category</button>
               <div class="mt-4">
                 <a href="<?php echo BASE_URL; ?>knowledge_base/categories/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 uppercase tracking-wide">Back to Categories</a>
                </div>
           </form>
        </div>
    </div>
