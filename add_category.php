<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

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

// Include header
require 'header.php';
?>


    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Add Category</h1>

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
        <form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Category Name</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Category</button>
        </form>

<?php
// Include footer
require 'footer.php';
?>