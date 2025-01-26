<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all categories
$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - RevenueSure</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">RevenueSure</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:underline">Dashboard</a>
                <a href="admin_dashboard.php" class="hover:underline">Admin Dashboard</a>
                <a href="add_category.php" class="hover:underline">Add Category</a>
                <a href="logout.php" class="hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Categories</h1>

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
                                <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>