<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

// Fetch all employees
$stmt = $conn->prepare("SELECT * FROM employees ORDER BY created_at DESC");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Employees</h1>

<!-- Add Employee Button -->
<a href="<?php echo BASE_URL; ?>employees/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Employee</a>

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
                            <a href="<?php echo BASE_URL; ?>employees/edit?id=<?php echo $employee['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="<?php echo BASE_URL; ?>employees/delete?id=<?php echo $employee['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
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

