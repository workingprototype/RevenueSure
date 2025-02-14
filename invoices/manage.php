<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Initialize filters and active tab
$status_filter   = isset($_GET['status']) ? $_GET['status'] : '';
$date_from       = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to         = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$customer_id     = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$payment_method  = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';
$activeTab       = isset($_GET['tab']) ? $_GET['tab'] : 'invoices';

// Fetch all customers (for filter dropdown)
$stmt = $conn->prepare("SELECT id, name FROM customers ORDER BY name ASC");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ------------------- Metrics Queries -------------------

// Total Revenue
$stmt = $conn->prepare("SELECT SUM(total) as total_revenue FROM invoices WHERE status = 'Paid'");
$stmt->execute();
$total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?: 0;

// Average Invoice Value
$stmt = $conn->prepare("SELECT AVG(total) as average_invoice FROM invoices WHERE status = 'Paid'");
$stmt->execute();
$average_invoice = $stmt->fetch(PDO::FETCH_ASSOC)['average_invoice'] ?: 0;

// Monthly Revenue Trend
$stmt = $conn->prepare("SELECT DATE_FORMAT(issue_date, '%Y-%m') AS month, SUM(total) AS monthly_revenue FROM invoices WHERE status = 'Paid' GROUP BY month ORDER BY month");
$stmt->execute();
$monthly_revenue_trend = $stmt->fetchAll(PDO::FETCH_ASSOC);
$chartLabels = [];
$chartData   = [];
foreach ($monthly_revenue_trend as $trend) {
    $chartLabels[] = $trend['month'];
    $chartData[]   = $trend['monthly_revenue'];
}

// Unpaid Invoices Count & Total
$stmt = $conn->prepare("SELECT COUNT(*) as unpaid_count, SUM(total) as unpaid_total FROM invoices WHERE status != 'Paid'");
$stmt->execute();
$unpaid_data  = $stmt->fetch(PDO::FETCH_ASSOC);
$total_unpaid = $unpaid_data['unpaid_total'] ?: 0;
$unpaid_count = $unpaid_data['unpaid_count'] ?: 0;

// Overdue Invoices Count & Total
$stmt = $conn->prepare("SELECT COUNT(*) as overdue_count, SUM(total) as overdue_total FROM invoices WHERE status = 'Overdue'");
$stmt->execute();
$overdue_data  = $stmt->fetch(PDO::FETCH_ASSOC);
$total_overdue = $overdue_data['overdue_total'] ?: 0;
$overdue_count = $overdue_data['overdue_count'] ?: 0;

// ------------------- Main Queries -------------------

// Base SQL Query for Invoices
$invoices_query = "SELECT invoices.*, customers.name AS customer_name,
                   (SELECT SUM(amount) FROM payments WHERE invoice_id = invoices.id) AS total_paid
                   FROM invoices
                   LEFT JOIN customers ON invoices.customer_id = customers.id
                   WHERE 1=1";

$payment_query = "SELECT payments.*, invoices.invoice_number, invoices.bill_to_name, invoices.customer_id
                  FROM payments
                  INNER JOIN invoices ON payments.invoice_id = invoices.id
                  WHERE 1=1";

$invoicesParams = [];
$paymentParams = [];
$filters = [];
$paymentsFilters = [];

// Apply filters for invoices
if (!empty($payment_method)) {
    $filters[] = "EXISTS (SELECT 1 FROM payments WHERE payments.invoice_id = invoices.id AND payments.payment_method = :payment_method)";
    $invoicesParams[':payment_method'] = $payment_method;
}
if (!empty($status_filter)) {
    $filters[] = "invoices.status = :status";
    $invoicesParams[':status'] = $status_filter;
}
if (!empty($date_from)) {
    $filters[] = "invoices.issue_date >= :date_from";
    $invoicesParams[':date_from'] = $date_from;
}
if (!empty($date_to)) {
    $filters[] = "invoices.issue_date <= :date_to";
    $invoicesParams[':date_to'] = $date_to . ' 23:59:59';
}
if (!empty($customer_id)) {
    $filters[] = "invoices.customer_id = :customer_id";
    $invoicesParams[':customer_id'] = $customer_id;
}

// Apply filters for payments (when in Payments tab)
if ($activeTab === 'payments') {
    if (!empty($payment_method)) {
        $paymentsFilters[] = "payments.payment_method = :payment_method";
        $paymentParams[':payment_method'] = $payment_method;
    }
    if (!empty($date_from)) {
        $paymentsFilters[] = "payments.payment_date >= :date_from";
        $paymentParams[':date_from'] = $date_from;
    }
    if (!empty($date_to)) {
        $paymentsFilters[] = "payments.payment_date <= :date_to";
        $paymentParams[':date_to'] = $date_to . ' 23:59:59';
    }
    if (!empty($customer_id)) {
        $paymentsFilters[] = "invoices.customer_id = :customer_id";
        $paymentParams[':customer_id'] = $customer_id;
    }
}

// Combine filters into queries
if (!empty($filters)) {
    $invoices_query .= " AND " . implode(" AND ", $filters);
}
if (!empty($paymentsFilters)) {
    $payment_query .= " AND " . implode(" AND ", $paymentsFilters);
}

// Execute Invoices Query (when active tab is Invoices)
if ($activeTab == 'invoices' || $activeTab == '') {
    $invoices_query .= " ORDER BY invoices.issue_date DESC";
    $stmt = $conn->prepare($invoices_query);
    foreach ($invoicesParams as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Execute Payments Query (when active tab is Payments)
if ($activeTab === 'payments') {
    $payment_query .= " ORDER BY payments.payment_date DESC";
    $stmt = $conn->prepare($payment_query);
    foreach ($paymentParams as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// (Optional) Re-fetch customers for filter dropdown
$stmt = $conn->prepare("SELECT id, name FROM customers ORDER BY name ASC");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- HTML for the Page -->
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Invoices</h1>
    <!-- Tab Bar -->
    <div class="mb-4">
        <nav class="flex space-x-4" aria-label="Tabs" role="tablist">
            <a href="<?php echo BASE_URL; ?>invoices/manage" class="px-4 py-2 rounded-lg <?php echo ($activeTab === 'invoices' || $activeTab === '') ? 'bg-blue-700 text-white' : 'text-gray-700 hover:text-gray-900 bg-blue-100'; ?> transition duration-300" role="tab" aria-controls="invoices-tab" aria-selected="<?php echo ($activeTab === 'invoices' || $activeTab === '') ? 'true' : 'false'; ?>">Invoices</a>
            <a href="<?php echo BASE_URL; ?>invoices/manage?tab=payments" class="px-4 py-2 rounded-lg <?php echo ($activeTab === 'payments') ? 'bg-blue-700 text-white' : 'text-gray-700 hover:text-gray-900 bg-blue-100'; ?> transition duration-300" role="tab" aria-controls="payments-tab" aria-selected="<?php echo ($activeTab === 'payments') ? 'true' : 'false'; ?>">Payments</a>
        </nav>
    </div>

    <?php if ($activeTab === 'invoices' || $activeTab === ''): ?>
        <!-- Filters UI -->
        <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg mb-6">
            <form method="GET" action="" class="flex flex-wrap gap-2 items-center">
                <input type="hidden" name="tab" value="invoices">
                <label for="date_from" class="block text-gray-700">Date From:</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($date_from); ?>" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <label for="date_to" class="block text-gray-700">Date To:</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($date_to); ?>" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                <label for="customer_id" class="block text-gray-700">Customer:</label>
                <select name="customer_id" id="customer_id" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                    <option value="">All Customers</option>
                    <?php if ($customers): ?>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" <?php if(isset($_GET['customer_id']) && $_GET['customer_id'] == $customer['id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <label for="status" class="block text-gray-700">Status:</label>
                <select name="status" id="status" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                    <option value="">All</option>
                    <option value="unpaid" <?php if(isset($_GET['status']) && $_GET['status'] === 'unpaid') echo 'selected'; ?>>Unpaid</option>
                    <option value="Overdue" <?php if(isset($_GET['status']) && $_GET['status'] === 'Overdue') echo 'selected'; ?>>Overdue</option>
                    <option value="Partially Paid" <?php if(isset($_GET['status']) && $_GET['status'] === 'Partially Paid') echo 'selected'; ?>>Partially Paid</option>
                    <option value="Paid" <?php if(isset($_GET['status']) && $_GET['status'] === 'Paid') echo 'selected'; ?>>Paid</option>
                </select>
                <label for="payment_method" class="block text-gray-700">Payment Method:</label>
                <select name="payment_method" id="payment_method" class="w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                    <option value="">All</option>
                    <option value="Credit Card" <?php if (isset($_GET['payment_method']) && $_GET['payment_method'] === 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                    <option value="Bank Transfer" <?php if (isset($_GET['payment_method']) && $_GET['payment_method'] === 'Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
                    <option value="PayPal" <?php if (isset($_GET['payment_method']) && $_GET['payment_method'] === 'PayPal') echo 'selected'; ?>>PayPal</option>
                    <option value="Cheque" <?php if (isset($_GET['payment_method']) && $_GET['payment_method'] === 'Cheque') echo 'selected'; ?>>Cheque</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Filter</button>
                <a href="<?php echo BASE_URL; ?>invoices/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Clear Filter</a>
            </form>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="elevated-card p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-700">Total Revenue</h2>
                <p class="text-2xl font-bold text-green-600">$<?php echo htmlspecialchars(number_format($total_revenue, 2)); ?></p>
            </div>
            <div class="elevated-card p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-700">Average Invoice Value</h2>
                <p class="text-2xl font-bold text-blue-600">$<?php echo htmlspecialchars(number_format($average_invoice, 2)); ?></p>
            </div>
            <div class="elevated-card p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-700">Unpaid Invoices</h2>
                <p class="text-2xl font-bold text-red-600"><?php echo htmlspecialchars($unpaid_count); ?></p>
                <p class="text-gray-600">$<?php echo htmlspecialchars(number_format($total_unpaid, 2)); ?></p>
            </div>
            <div class="elevated-card p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-700">Overdue Invoices</h2>
                <p class="text-2xl font-bold text-red-700"><?php echo htmlspecialchars($overdue_count); ?></p>
                <p class="text-gray-600">$<?php echo htmlspecialchars(number_format($total_overdue, 2)); ?></p>
            </div>
        </div>

        <!-- Add Invoice Button -->
        <div class="mb-8 flex justify-between items-center">
            <a href="<?php echo BASE_URL; ?>invoices/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
                <i class="fas fa-plus-circle mr-2"></i>Add Invoice
            </a>
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
                        <?php foreach ($invoices as $invoice):
                            // Calculate percentage paid for progress bar
                            $stmt = $conn->prepare("SELECT SUM(amount) AS total_paid FROM payments WHERE invoice_id = :invoice_id");
                            $stmt->bindParam(':invoice_id', $invoice['id']);
                            $stmt->execute();
                            $payment_info = $stmt->fetch(PDO::FETCH_ASSOC);
                            $total_paid = $payment_info['total_paid'] ?? 0;
                            $percentage_paid = ($total_paid / $invoice['total']) * 100;
                            $percentage_paid = min(100, max(0, $percentage_paid));
                        ?>
                            <tr class="border-b transition hover:bg-gray-100">
                                <!-- Invoice Number (link to invoice details) -->
                                <td class="px-4 py-3">
                                    <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo $invoice['id']; ?>">
                                        <?php echo htmlspecialchars($invoice['invoice_number']); ?>
                                    </a>
                                </td>
                                <!-- Bill To (link to customer profile if available) -->
                                <td class="px-4 py-3">
                                    <?php if (!empty($invoice['customer_id'])): ?>
                                        <a href="<?php echo BASE_URL; ?>customers/view?id=<?php echo $invoice['customer_id']; ?>">
                                            <?php echo htmlspecialchars($invoice['bill_to_name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($invoice['bill_to_name']); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['issue_date']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($invoice['due_date']); ?></td>
                                <!-- Status Column: Progress bar and status tag -->
                                <td class="px-4 py-3">
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-100">
                                        <div style="width:<?php echo $percentage_paid; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                                    </div>
                                    <span class="text-gray-600"><?php echo number_format($percentage_paid, 2); ?>% Paid</span>
                                    <br>
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
                                    <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo $invoice['id']; ?>" class="text-purple-600 hover:underline">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>invoices/edit?id=<?php echo $invoice['id']; ?>" class="text-blue-600 hover:underline">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button onclick="confirmDelete(<?php echo $invoice['id']; ?>)" class="text-red-600 hover:underline ml-2">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
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

        <!-- Revenue per Month Trend Chart -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Revenue per Month Trend</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <?php if ($monthly_revenue_trend): ?>
                    <canvas id="revenueChart"></canvas>
                <?php else: ?>
                    <p>No data to display.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($activeTab === 'payments'): ?>
        <!-- Payments Table -->
        <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Invoice Number</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Bill To</th>
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
                                <!-- Invoice Number (link to invoice details) -->
                                <td class="px-4 py-3">
                                    <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo $payment['invoice_id']; ?>">
                                        <?php echo htmlspecialchars($payment['invoice_number']); ?>
                                    </a>
                                </td>
                                <!-- Bill To (link to customer profile if available) -->
                                <td class="px-4 py-3">
                                    <?php if (!empty($payment['customer_id'])): ?>
                                        <a href="<?php echo BASE_URL; ?>customers/view?id=<?php echo $payment['customer_id']; ?>">
                                            <?php echo htmlspecialchars($payment['bill_to_name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($payment['bill_to_name']); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                <!-- Transaction ID (link to payment details) -->
                                <td class="px-4 py-3">
                                    <a href="<?php echo BASE_URL; ?>payments/view?id=<?php echo $payment['id']; ?>">
                                        <?php echo htmlspecialchars($payment['transaction_id']); ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3">$<?php echo htmlspecialchars($payment['amount']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-600">No payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<!-- Chart.js and JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueChartCtx = document.getElementById('revenueChart').getContext('2d');
    const chartLabels = <?php echo json_encode($chartLabels); ?>;
    const chartData = <?php echo json_encode($chartData); ?>;
    
    new Chart(revenueChartCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Monthly Revenue',
                data: chartData,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    }
                }
            }
        }
    });
});

function confirmDelete(invoiceId) {
    if (confirm('Are you sure you want to delete this invoice?')) {
        window.location.href = '<?php echo BASE_URL; ?>invoices/delete?id=' + invoiceId;
    }
}
</script>
