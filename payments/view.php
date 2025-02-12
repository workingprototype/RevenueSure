<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch payment details
$stmt = $conn->prepare("SELECT payments.*, invoices.invoice_number, invoices.bill_to_name, invoices.billing_country,
  DATE_FORMAT(payments.payment_date, '%Y-%m-%d %H:%i:%s') AS formatted_payment_date, invoices.total as invoice_total, invoices.issue_date, invoices.due_date, invoices.paid_amount as total_paid
   FROM payments JOIN invoices ON payments.invoice_id = invoices.id WHERE payments.id = :payment_id");
$stmt->bindParam(':payment_id', $payment_id);
$stmt->execute();
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    echo "Payment not found.";
    exit();
}

$invoice_id = $payment['invoice_id'];

// Fetch other required details related to this payment
$stmt = $conn->prepare("SELECT
total, issue_date, due_date FROM invoices  WHERE id = :invoice_id");
$stmt->bindParam(':invoice_id', $invoice_id);
$stmt->execute();
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$invoice) {
    echo "Invoice not found.";
    exit();
}
// Calculate remaining balance
$remaining_balance = $invoice['total'] - $payment['amount'];

// Calculate percentage paid
$percentage_paid = ($payment['total_paid'] / $invoice['total']) * 100;
$percentage_paid = min(100, max(0, $percentage_paid)); // Ensure within 0-100 range
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Payment Details</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Payment Information Card -->
        <div class="elevated-card p-6" style="border-radius: 22px">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
              <i class="fas fa-credit-card mr-2 text-blue-500"></i> Payment Information
           </h2>
            <div class="mb-4">
                <p class="text-gray-700"><span class="font-semibold">Payment ID:</span> <?php echo htmlspecialchars($payment['id']); ?></p>
                <p class="text-gray-700"><span class="font-semibold">Invoice Number:</span> <?php echo htmlspecialchars($payment['invoice_number']); ?></p>
                <p class="text-gray-700"><span class="font-semibold">Invoice Total:</span> $<?php echo htmlspecialchars($invoice['total']); ?></p>
                <p class="text-gray-700"><span class="font-semibold">Amount Paid:</span> $<?php echo htmlspecialchars($payment['amount']); ?></p>
                  <p class="text-gray-700"><span class="font-semibold">Remaining Balance:</span> $<?php echo htmlspecialchars($remaining_balance); ?></p>

                <p class="text-gray-700"><span class="font-semibold">Payment Date:</span> <?php echo htmlspecialchars($payment['formatted_payment_date']); ?></p>
                <p class="text-gray-700"> <span class="font-semibold">Payment Method:</span> <?php echo htmlspecialchars($payment['payment_method']); ?></p>
                <?php if (!empty($payment['transaction_id'])): ?>
                    <p class="text-gray-700"> <span class="font-semibold">Transaction ID:</span> <?php echo htmlspecialchars($payment['transaction_id']); ?></p>
               <?php endif; ?>
            </div>
              <!-- Progress bar (using Tailwind CSS classes) -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Payment Progress</label>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-100">
                    <div style="width:<?php echo $percentage_paid; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500" ></div>
                  </div>
                   <span class="text-gray-600"><?php echo number_format($percentage_paid, 2); ?>% Paid</span>
            </div>
        </div>
         <div class="elevated-card p-6" style="border-radius: 22px">
             <h2 class="text-xl font-bold text-gray-800 mb-4">
                  <i class="fas fa-file-invoice-dollar mr-2 text-green-500"></i> Invoice Information
                </h2>
                  <div class="mb-4">
                          <p class="text-gray-700">  <span class="font-semibold">Bill To:</span> <?php echo htmlspecialchars($payment['bill_to_name']); ?></p>
                            <p class="text-gray-700">  <span class="font-semibold">Billing Country:</span> <?php echo htmlspecialchars($payment['billing_country']); ?></p>
                            <p class="text-gray-700"> <span class="font-semibold">Invoice Total:</span> $<?php echo htmlspecialchars($invoice['total']); ?></p>
                             
                        </div>
           </div>

        <!-- Bill-To Information Card -->
        <div class="elevated-card p-6" >
            <h2 class="text-xl font-bold text-gray-800 mb-4">Bill-To Information</h2>
            <p class="text-gray-700"><strong>Name:</strong> <?php echo htmlspecialchars($payment['bill_to_name']); ?></p>
           
        </div>
    </div>

     <div class="mt-6 text-center">
        <a href="<?php echo BASE_URL; ?>invoices/view?id=<?php echo $payment['invoice_id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">View Original Invoice</a>
         <a href="<?php echo BASE_URL; ?>invoices/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Go Back To Invoices</a>
        </div>
</div>