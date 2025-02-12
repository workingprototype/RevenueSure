<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch payment details
$stmt = $conn->prepare("SELECT payments.*, invoices.invoice_number, invoices.bill_to_name FROM payments JOIN invoices ON payments.invoice_id = invoices.id WHERE payments.id = :payment_id");
$stmt->bindParam(':payment_id', $payment_id);
$stmt->execute();
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    echo "Payment not found.";
    exit;
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Payment Details</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Payment Information</h2>
        <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment['id']); ?></p>
        <p><strong>Invoice Number:</strong> <?php echo htmlspecialchars($payment['invoice_number']); ?></p>
        <p><strong>Bill To:</strong> <?php echo htmlspecialchars($payment['bill_to_name']); ?></p>
        <p><strong>Payment Date:</strong> <?php echo htmlspecialchars($payment['payment_date']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method']); ?></p>
        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></p>
        <p><strong>Amount:</strong> $<?php echo htmlspecialchars($payment['amount']); ?></p>
    </div>

    <div class="mt-4">
       <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo $payment['invoice_id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">Back To Invoice</a>
       <a href="<?php echo BASE_URL; ?>invoices/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Invoices</a>
    </div>
</div>