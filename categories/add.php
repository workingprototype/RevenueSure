<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    // Validate input
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Check if the category already exists
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "A category with this name already exists.";
        } else {
            // Insert the category
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);

            if ($stmt->execute()) {
                $success = "Category added successfully!";
            } else {
                $error = "Error adding category.";
            }
        }
    }
}


?>
<div class="container mx-auto p-6 fade-in">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Add Category</h1>
        
        <!-- Display error or success message -->
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
            <!-- Add Category Form -->
          <div class="bg-white p-6 rounded-2xl shadow-xl ">
               <form method="POST" action="" >
               <?php echo csrfTokenInput(); ?>
                   <div class="mb-4">
                        <label for="name" class="block text-gray-700">Category Name</label>
                        <input type="text" name="name" id="name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                   </div>
                     <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Category</button>
                </form>
         </div>
    </div>
