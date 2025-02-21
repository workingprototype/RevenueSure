<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';


try {
    // Fetch all customers, ordering by most recently created
    $stmt = $conn->prepare("SELECT * FROM customers ORDER BY created_at DESC");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Database error. Please try again later.";
    $customers = [];
}

?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Customers</h1>

    <!-- Add customer button -->
    <div class="flex justify-between items-center mb-8">
        <a href="<?php echo BASE_URL; ?>customers/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Customer</a>
    </div>

    <!-- Customers Table -->
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                     <th class="px-4 py-3">Company</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Last Interaction</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($customers): ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr class="border-b transition hover:bg-gray-100">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($customer['email']); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($customer['company'] ?? ''); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($customer['phone']); ?></td>
                             <td class="px-4 py-3"><?php echo $customer['last_interaction'] ? date('Y-m-d H:i', strtotime($customer['last_interaction'])) : "N/A"; ?></td>
                            <td class="px-4 py-3 flex gap-2">
                                 <a href="<?php echo BASE_URL; ?>customers/view?id=<?php echo (int)$customer['id']; ?>" class="text-purple-600 hover:underline"> <i class="fas fa-eye"></i> View</a>
                                <a href="<?php echo BASE_URL; ?>customers/edit?id=<?php echo (int)$customer['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_URL; ?>customers/delete?id=<?php echo (int)$customer['id']; ?>" class="text-red-600 hover:underline ml-2" onclick="return confirm('Are you sure you want to delete this customer?')"> <i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-600">No customers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>