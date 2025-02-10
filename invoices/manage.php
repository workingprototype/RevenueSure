<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

// Get status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT * FROM invoices";
$params = [];

// Modify the query based on the status filter
if (!empty($status_filter)) {
      if($status_filter === 'unpaid'){
           $query .= " WHERE status != 'Paid'";
      } else {
             $query .= " WHERE status = :status";
             $params[':status'] = $status_filter;
        }
}
 $query .= " ORDER BY created_at DESC";
// Fetch all invoices
$stmt = $conn->prepare($query);
  foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Invoices</h1>

    <!-- Add invoice button -->
    <div class="flex justify-between items-center mb-8">
    <a href="<?php echo BASE_URL; ?>invoices/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"> <i class="fas fa-plus-circle mr-2"></i> Add Invoice</a>
    <!-- Filter by status -->
      <div class="flex flex-wrap gap-2">
            <form method="GET" action="" class="flex gap-2">
             <select name="status" id="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">All</option>
                 <option value="unpaid" <?php if(isset($_GET['status']) && $_GET['status'] === 'unpaid') echo 'selected'; ?>>Unpaid</option>
                <option value="Overdue" <?php if(isset($_GET['status']) && $_GET['status'] === 'Overdue') echo 'selected'; ?>>Overdue</option>
                <option value="Partially Paid" <?php if(isset($_GET['status']) && $_GET['status'] === 'Partially Paid') echo 'selected'; ?>>Partially Paid</option>
               <option value="Paid" <?php if(isset($_GET['status']) && $_GET['status'] === 'Paid') echo 'selected'; ?>>Paid</option>
             </select>
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">Filter</button>
           </form>
         <a href="<?php echo BASE_URL; ?>invoices/manage" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">Clear Filter</a>
      </div>
    </div>
<!-- Invoices Table -->
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Invoice Number</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Bill To</th>
                      <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Issue Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Due Date</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                      <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Total</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($invoices): ?>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr class="border-b transition hover:bg-gray-100">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['bill_to_name']); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['issue_date']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['due_date']); ?></td>
                            <td class="px-4 py-3">
                                 <span class="px-2 py-1 rounded-full <?php
                                    switch ($invoice['status']) {
                                        case 'Unpaid':
                                            echo 'bg-red-100 text-red-800';
                                             break;
                                       case 'Partially Paid':
                                           echo 'bg-yellow-100 text-yellow-800';
                                           break;
                                        case 'Paid':
                                             echo 'bg-green-100 text-green-800';
                                               break;
                                        case 'Overdue':
                                             echo 'bg-gray-100 text-gray-800';
                                                break;
                                        default:
                                          echo 'bg-gray-100 text-gray-800';
                                            break;
                                    }
                                 ?>"><?php echo htmlspecialchars($invoice['status']); ?></span>
                             </td>
                            <td class="px-4 py-3">$<?php echo htmlspecialchars($invoice['total']); ?></td>
                            <td class="px-4 py-3 flex gap-2">
                                <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo $invoice['id']; ?>" class="text-purple-600 hover:underline">  <i class="fas fa-eye"></i> View</a>
                                <a href="<?php echo BASE_URL; ?>invoices/edit?id=<?php echo $invoice['id']; ?>" class="text-blue-600 hover:underline">  <i class="fas fa-edit"></i> Edit</a>
                                <button onclick="confirmDelete(<?php echo $invoice['id']; ?>)" class="text-red-600 hover:underline"> <i class="fas fa-trash-alt"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center text-gray-600">No invoices found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function confirmDelete(invoiceId) {
        if (confirm('Are you sure you want to delete this invoice?')) {
            window.location.href = 'invoices/delete?id=' + invoiceId;
        }
    }
</script>
