<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Initialize filters
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$currency = isset($_GET['currency']) ? $_GET['currency'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$transaction_type = isset($_GET['transaction_type']) ? $_GET['transaction_type'] : '';
$reconciliation_status = isset($_GET['reconciliation_status']) ? $_GET['reconciliation_status'] : '';

// Set active tab based on the 'tab' parameter in the GET request
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'all'; // Default to 'all' tab

// Handle Send for Review action within the same page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_entry_id'])) {
    $entry_id = (int)$_POST['review_entry_id'];
    if ($entry_id > 0) {
        try {
            $stmt = $conn->prepare("UPDATE ledger_entries SET requires_review = 1 WHERE id = :entry_id");
            $stmt->bindParam(':entry_id', $entry_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $success = "Entry sent for review.";
            } else {
                $error = "Error sending for review.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Invalid entry ID.";
    }
}
// Build the base query for combining invoices and expenses
$query = "
    SELECT transaction_date, description, debit_amount, credit_amount, currency, category, reconciliation_status, transaction_type, id, requires_review
    FROM (
        SELECT issue_date as transaction_date, concat('Invoice #', invoice_number, ' Payment') as description, 0 as debit_amount, total as credit_amount, billing_country as currency, 'Revenue' as category, 'Unreconciled' as reconciliation_status, 'Invoice' as transaction_type, id, 0 as requires_review
         FROM invoices
        WHERE status = 'Paid'

        UNION ALL
        SELECT expense_date as transaction_date, name as description, amount as debit_amount, 0 as credit_amount, 'USD' as currency, transaction_nature as category, 'Unreconciled' as reconciliation_status, 'Expense' as transaction_type, id, 0 as requires_review FROM expenses
    ) AS combined_ledger WHERE 1=1
";

$params = [];

// Apply filters
if (!empty($date_from)) {
    $query .= " AND transaction_date >= :date_from";
    $params[':date_from'] = $date_from;
}
if (!empty($date_to)) {
    $query .= " AND transaction_date <= :date_to";
    $params[':date_to'] = $date_to;
}
if (!empty($currency)) {
    $query .= " AND currency = :currency";
    $params[':currency'] = $currency;
}
if (!empty($category)) {
    $query .= " AND category = :category";
    $params[':category'] = $category;
}
if (!empty($transaction_type)) {
    $query .= " AND transaction_type = :transaction_type";
    $params[':transaction_type'] = $transaction_type;
}

if (!empty($reconciliation_status)) {
    $query .= " AND reconciliation_status = :reconciliation_status";
    $params[':reconciliation_status'] = $reconciliation_status;
}
//Add condition for 'transaction for review' tab
if ($activeTab === 'review') {
   $query .= " AND requires_review = 1";
}

// Sorting (Implement the sorting logic as needed)
$query .= " ORDER BY transaction_date DESC";

// Prepare and execute the statement
$stmt = $conn->prepare($query);
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value);
}
$stmt->execute();
$ledger_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStatusColor($status){
     return match ($status) {
        'Unreconciled' => 'bg-red-100 text-red-800',
        'Matched' => 'bg-green-100 text-green-800',
        'Discrepancy' => 'bg-yellow-100 text-yellow-800',
         default => 'bg-gray-100 text-gray-800',
    };
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Ledger</h1>

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

   <!-- Tab Navigation -->
    <div class="mb-4">
        <nav class="flex space-x-4" aria-label="Tabs" role="tablist">
            <a href="<?php echo BASE_URL; ?>accounting/ledger" class="px-4 py-2 rounded-lg  <?php if ($activeTab === 'all') echo 'bg-blue-700 text-white'; else echo 'text-gray-700 hover:text-gray-900 bg-blue-100'; ?> transition duration-300" role="tab" aria-controls="all-tab" aria-selected="<?php echo ($activeTab === 'all') ? 'true' : 'false'; ?>">All Transactions</a>
            <a href="<?php echo BASE_URL; ?>accounting/ledger?tab=review" class="px-4 py-2 rounded-lg <?php if ($activeTab === 'review') echo 'bg-blue-700 text-white'; else echo 'text-gray-700 hover:text-gray-900 bg-blue-100'; ?> transition duration-300" role="tab" aria-controls="review-tab" aria-selected="<?php echo ($activeTab === 'review') ? 'true' : 'false'; ?>">Transactions For Review</a>
         </nav>
   </div>
    <!-- Filter Form -->
    <div class="mb-4">
        <form method="GET" action="" class="flex flex-wrap gap-2 items-center">
            <input type="hidden" name="tab" value="<?php echo htmlspecialchars($activeTab); ?>">

            <label for="date_from" class="block text-gray-700">Date From:</label>
            <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($date_from); ?>" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">

            <label for="date_to" class="block text-gray-700">Date To:</label>
            <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($date_to); ?>" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">

            <label for="currency" class="block text-gray-700">Currency:</label>
            <select name="currency" id="currency" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                <option value="">All</option>
                <option value="USD" <?php if ($currency === 'USD') echo 'selected'; ?>>USD</option>
                <option value="EUR" <?php if ($currency === 'EUR') echo 'selected'; ?>>EUR</option>
                <option value="GBP" <?php if ($currency === 'GBP') echo 'selected'; ?>>GBP</option>
                  <option value="inr" <?php if ($currency === 'inr') echo 'selected'; ?>>INR</option>
            </select>

            <label for="category" class="block text-gray-700">Category:</label>
            <select name="category" id="category" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                <option value="">All</option>
                <option value="Revenue" <?php if ($category === 'Revenue') echo 'selected'; ?>>Revenue</option>
                <option value="Expense" <?php if ($category === 'Expense') echo 'selected'; ?>>Expense</option>
                <option value="Asset" <?php if ($category === 'Asset') echo 'selected'; ?>>Asset</option>
                <option value="Liability" <?php if ($category === 'Liability') echo 'selected'; ?>>Liability</option>
                <option value="Equity" <?php if ($category === 'Equity') echo 'selected'; ?>>Equity</option>
                 <option value="Reimbursable" <?php if ($category === 'Reimbursable') echo 'selected'; ?>>Reimbursable</option>
                   <option value="Business Expense" <?php if ($category === 'Business Expense') echo 'selected'; ?>>Business Expense</option>
                    <option value="Personal Expense" <?php if ($category === 'Personal Expense') echo 'selected'; ?>>Personal Expense</option>
            </select>

           <label for="transaction_type" class="block text-gray-700">Type:</label>
            <select name="transaction_type" id="transaction_type" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                <option value="">All</option>
                  <option value="Invoice" <?php if ($transaction_type === 'Invoice') echo 'selected'; ?>>Invoice</option>
                    <option value="Expense" <?php if ($transaction_type === 'Expense') echo 'selected'; ?>>Expense</option>
            </select>
            <?php if($activeTab === 'review') : ?>
                 <label for="reconciliation_status" class="block text-gray-700">Reconcilliation status:</label>
                <select name="reconciliation_status" id="reconciliation_status" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="">All</option>
                     <option value="Unreconciled" <?php if ($reconciliation_status === 'Unreconciled') echo 'selected'; ?>>Unreconciled</option>
                        <option value="Matched" <?php if ($reconciliation_status === 'Matched') echo 'selected'; ?>>Matched</option>
                      <option value="Discrepancy" <?php if ($reconciliation_status === 'Discrepancy') echo 'selected'; ?>>Discrepancy</option>
                </select>
             <?php endif; ?>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Filter</button>
           <a href="<?php echo BASE_URL; ?>accounting/ledger" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Clear Filter</a>
      </form>
    </div>

    <!-- Ledger Entries Table -->
    <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
        

                    <table class="table-auto w-full">
                        <thead>
                            <tr class="bg-gray-100">
                             <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Description</th>
                               <th class="px-4 py-2">Debit</th>
                                <th class="px-4 py-2">Credit</th>
                                <th class="px-4 py-2">Currency</th>
                               <th class="px-4 py-2">Category</th>
                                <th class="px-4 py-2">Reconciliation Status</th>
                                <th class="px-4 py-2">Actions</th>
                           </tr>
                      </thead>
                        <tbody>
                            <?php if ($ledger_entries): ?>
                                <?php
                                  foreach ($ledger_entries as $entry):
                                       $statusColorClass = getStatusColor($entry['reconciliation_status']);
                                     $credit_amount = ($entry['credit_amount']) > 0 ? '$'.htmlspecialchars($entry['credit_amount']) : '';
                                      $debit_amount = ($entry['debit_amount']) > 0 ? '$'.htmlspecialchars($entry['debit_amount']) : '';

                                     // Determine the URL based on transaction type
                                         $viewUrl = '';
                                        $viewText = '';
                                          if ($entry['transaction_type'] === 'Invoice') {
                                               $viewUrl = BASE_URL . "invoices/view?id=" . htmlspecialchars($entry['id']);
                                             $viewText = "View Invoice";
                                           } elseif ($entry['transaction_type'] === 'Expense') {
                                                $viewUrl = BASE_URL . "expenses/view?id=" . htmlspecialchars($entry['id']);
                                                 $viewText = "View Expense";
                                        }
                                      ?>
                                    <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2"><?php echo htmlspecialchars($entry['transaction_date']); ?></td>
                                          <td class="border px-4 py-2">
                                                <?php if (!empty($viewUrl)) : ?>
                                                      <a href="<?php echo $viewUrl; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($entry['description']); ?></a>
                                                 <?php else : ?>
                                                      <?php echo htmlspecialchars($entry['description']); ?>
                                                  <?php endif; ?>
                                          </td>
                                          <td class="border px-4 py-2 text-right <?php echo $entry['debit_amount'] > 0 ? 'text-red-600' : ''; ?>"><?php echo $debit_amount ?></td>
                                            <td class="border px-4 py-2 text-right <?php echo $entry['credit_amount'] > 0 ? 'text-green-600' : ''; ?>"><?php echo  $credit_amount ?></td>
                                              <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['currency']); ?></td>
                                           <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['category']); ?></td>
                                            <td class="border px-4 py-2">
                                                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php echo $statusColorClass ?>"><?php echo htmlspecialchars($entry['reconciliation_status']); ?></span>
                                           </td>
                                           <td class="border px-4 py-2">
                                                <?php if (!empty($viewUrl)) : ?>
                                                   <a href="<?php echo $viewUrl; ?>" class="text-blue-600 hover:underline"><?php echo $viewText ?></a>
                                                 <?php else : ?>
                                                       N/A
                                               <?php endif; ?>
                                               <?php if ($activeTab !== 'review'): ?>
                                                <form method="POST" action="" class="inline">
                                                    <?php echo csrfTokenInput(); ?>
                                                    <input type="hidden" name="review_entry_id" value="<?php echo $entry['id']; ?>">
                                                    <button type="submit" class="text-yellow-600 hover:underline">Send for Review</button>
                                                </form>
                                                <?php endif; ?>
                                          </td>
                                </tr>
                             <?php endforeach; ?>
                       <?php else: ?>
                               <tr>
                                     <td colspan="9" class="px-4 py-2 text-center">No ledger entries found.</td>
                                </tr>
                     <?php endif; ?>
                 </tbody>
          </table>
 </div>

</div>