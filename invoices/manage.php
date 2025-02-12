<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Determine active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'invoices';

// Invoice List Data (Default Tab)
if ($activeTab === 'invoices' || $activeTab === '') {
    // Status Filter
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    $query = "SELECT *,  (SELECT SUM(amount) FROM payments WHERE invoice_id = invoices.id) AS total_paid FROM invoices";
    $params = [];

    if (!empty($status_filter)) {
        if ($status_filter === 'unpaid') {
            $query .= " WHERE status != 'Paid'";
        } else {
            $query .= " WHERE status = :status";
            $params[':status'] = $status_filter;
        }
    }
    $query .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Payment List Data (Payments Tab)
if ($activeTab === 'payments') {
    $stmt = $conn->prepare("SELECT payments.*, invoices.invoice_number
                             FROM payments
                             INNER JOIN invoices ON payments.invoice_id = invoices.id
                             ORDER BY payment_date DESC");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Invoices</h1>

     <!-- Tab Navigation -->
        <div class="mb-4">
            <nav class="flex space-x-4" aria-label="Tabs" role="tablist">
                <a href="<?php echo BASE_URL; ?>invoices/manage" class="px-4 py-2 rounded-lg  <?php if ($activeTab === 'invoices' || $activeTab === '') echo 'bg-blue-700 text-white'; else echo 'text-gray-700 hover:text-gray-900 bg-blue-100'; ?> transition duration-300" role="tab" aria-controls="all-tab" aria-selected="<?php echo ($activeTab === 'invoices' || $activeTab === '') ? 'true' : 'false'; ?>">Invoices</a>
                <a href="<?php echo BASE_URL; ?>invoices/manage?tab=payments" class="px-4 py-2 rounded-lg <?php if ($activeTab === 'payments') echo 'bg-blue-700 text-white'; else echo 'text-gray-700 hover:text-gray-900 bg-blue-100'; ?> transition duration-300" role="tab" aria-controls="review-tab" aria-selected="<?php echo ($activeTab === 'payments') ? 'true' : 'false'; ?>">Payments</a>
             </nav>
       </div>

        <!-- Invoices Tab Content -->
        <?php if ($activeTab === 'invoices' || $activeTab === ''): ?>
            <div class="mb-8 flex justify-between items-center">
                <a href="<?php echo BASE_URL; ?>invoices/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Invoice</a>

                <!-- Status Filter -->
                <div class="flex flex-wrap gap-2">
                    <form method="GET" action="" class="flex gap-2">
                        <input type="hidden" name="tab" value="invoices">
                        <select name="status" id="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
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
                                 <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Progress</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                                  <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Total</th>
                                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                            </tr>
                        </thead>
                       <tbody>
                           <?php if ($invoices): ?>
                               <?php foreach ($invoices as $invoice):
                                        $percentage_paid = ($invoice['total_paid'] / $invoice['total']) * 100;
                                            $percentage_paid = min(100, max(0, $percentage_paid)); ?>
                                   <tr class="border-b transition hover:bg-gray-100">
                                       <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                                       <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['bill_to_name']); ?></td>
                                         <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['issue_date']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['due_date']); ?></td>
                                         <td class="px-4 py-3">
                                                 <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-100">
                                                      <div style="width:<?php echo $percentage_paid; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500" ></div>
                                                  </div>
                                                   <span class="text-gray-600"><?php echo number_format($percentage_paid, 2); ?>% Paid</span>
                                         </td>

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
                                               <button onclick="confirmDelete(<?php echo $invoice['id']; ?>)" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</button>
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
       <?php endif; ?>

        <!-- Payments Tab Content -->
        <?php if ($activeTab === 'payments'): ?>
            <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Invoice Number</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Payment Date</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Payment Method</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Transaction ID</th>
                            <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($payments): ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr class="border-b transition hover:bg-gray-100">
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($payment['invoice_number']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                    <td class="px-4 py-3">$<?php echo htmlspecialchars($payment['amount']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-600">No payments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
</div>
<script>
    function confirmDelete(invoiceId) {
        if (confirm('Are you sure you want to delete this invoice?')) {
            window.location.href = 'invoices/delete?id=' + invoiceId;
        }
    }
</script>