<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, username, email, credits, role FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all leads
$stmt = $conn->prepare("SELECT * FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lead Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">Lead Platform</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:underline">Dashboard</a>
                <a href="admin_dashboard.php" class="hover:underline">Admin Dashboard</a>
                <a href="logout.php" class="hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

        <!-- Users Table -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Users</h2>
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Credits</th>
                        <th class="px-4 py-2">Role</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo $user['id']; ?></td>
                            <td class="px-4 py-2"><?php echo $user['username']; ?></td>
                            <td class="px-4 py-2"><?php echo $user['email']; ?></td>
                            <td class="px-4 py-2"><?php echo $user['credits']; ?></td>
                            <td class="px-4 py-2"><?php echo $user['role']; ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Leads Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Leads</h2>
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Phone</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo $lead['id']; ?></td>
                            <td class="px-4 py-2"><?php echo $lead['name']; ?></td>
                            <td class="px-4 py-2"><?php echo $lead['email']; ?></td>
                            <td class="px-4 py-2"><?php echo $lead['phone']; ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_lead.php?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>