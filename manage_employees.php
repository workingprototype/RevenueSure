<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all employees
$stmt = $conn->prepare("SELECT * FROM employees ORDER BY created_at DESC");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Employees</h1>

<!-- Add Employee Button -->
<a href="add_employee.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Employee</a>

<!-- Employees Table -->
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
            <?php if ($employees): ?>
                <?php foreach ($employees as $employee): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($employee['name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($employee['email']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($employee['phone']); ?></td>
                        <td class="px-4 py-2">
                            <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete_employee.php?id=<?php echo $employee['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-600">No employees found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>