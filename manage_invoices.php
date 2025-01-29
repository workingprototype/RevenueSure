<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all invoices
$stmt = $conn->prepare("SELECT * FROM invoices ORDER BY created_at DESC");
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Invoices</h1>

<!-- Add invoice button -->
<a href="add_invoice.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Invoice</a>

<!-- Invoices Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Invoice Number</th>
                <th class="px-4 py-2">Bill To</th>
                  <th class="px-4 py-2">Issue Date</th>
                <th class="px-4 py-2">Due Date</th>
                  <th class="px-4 py-2">Total</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($invoices): ?>
                <?php foreach ($invoices as $invoice): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($invoice['bill_to_name']); ?></td>
                         <td class="px-4 py-2"><?php echo htmlspecialchars($invoice['issue_date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($invoice['due_date']); ?></td>
                        <td class="px-4 py-2">$<?php echo htmlspecialchars($invoice['total']); ?></td>
                         <td class="px-4 py-2">
                            <a href="view_invoice.php?id=<?php echo $invoice['id']; ?>" class="text-purple-600 hover:underline">View</a>
                            <a href="edit_invoice.php?id=<?php echo $invoice['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete_invoice.php?id=<?php echo $invoice['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-600">No invoices found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>