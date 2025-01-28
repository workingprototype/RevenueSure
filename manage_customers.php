<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all customers
$stmt = $conn->prepare("SELECT * FROM customers ORDER BY created_at DESC");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Customers</h1>

<!-- Add customer button -->
<a href="add_customer.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Customer</a>

<!-- Customers Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Phone</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($customer['name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($customer['phone']); ?></td>
                        <td class="px-4 py-2">
                            <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete_customer.php?id=<?php echo $customer['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-600">No customers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>