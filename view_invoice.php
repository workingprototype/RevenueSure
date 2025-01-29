<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_invoices.php");
    exit();
}

$invoice_id = $_GET['id'];

// Fetch Invoice Details
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = :invoice_id");
$stmt->bindParam(':invoice_id', $invoice_id);
$stmt->execute();
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    echo "Invoice not found.";
    exit;
}

// Fetch Invoice Items
$stmt = $conn->prepare("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id");
$stmt->bindParam(':invoice_id', $invoice_id);
$stmt->execute();
$invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<h1 class="text-3xl font-bold text-gray-800 mb-6">View Invoice</h1>
     <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
               Invoice updated successfully!
            </div>
     <?php endif; ?>
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Invoice Details</h2>
         <p><strong>Invoice Number:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
         <p><strong>Issue Date:</strong> <?php echo htmlspecialchars($invoice['issue_date']); ?></p>
          <p><strong>Due Date:</strong> <?php echo htmlspecialchars($invoice['due_date']); ?></p>
         <p><strong>Payment Terms:</strong> <?php echo htmlspecialchars($invoice['payment_terms']); ?></p>
    </div>
    <div class="mb-4">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Bill To</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($invoice['bill_to_name']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($invoice['bill_to_address']); ?></p>
         <p><strong>Email:</strong> <?php echo htmlspecialchars($invoice['bill_to_email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($invoice['bill_to_phone']); ?></p>

    </div>
     <?php if($invoice['ship_to_address']): ?>
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Ship To</h2>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($invoice['ship_to_address']); ?></p>
    </div>
     <?php endif; ?>
     <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Items</h2>
         <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="px-4 py-2">Product/Service</th>
                     <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Unit Price</th>
                      <th class="px-4 py-2">Tax</th>
                       <th class="px-4 py-2">Discount</th>
                     <th class="px-4 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
              <?php if($invoice_items): ?>
                   <?php foreach ($invoice_items as $item): ?>
                      <tr class="border-b">
                       <td class="px-4 py-2"><?php echo htmlspecialchars($item['product_service']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($item['unit_price']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($item['tax']); ?></td>
                         <td class="px-4 py-2"><?php echo htmlspecialchars($item['discount']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($item['subtotal']); ?></td>
                      </tr>
                   <?php endforeach; ?>
                  <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-600">No items found.</td>
                       </tr>
               <?php endif; ?>
              </tbody>
          </table>
       </div>
         <div class="mb-4">
            <p><strong>Subtotal:</strong> $<?php echo htmlspecialchars($invoice['subtotal']); ?></p>
              <p><strong>Tax:</strong> $<?php echo htmlspecialchars($invoice['tax']); ?></p>
                 <p><strong>Discount:</strong> $<?php echo htmlspecialchars($invoice['discount']); ?></p>
             <p><strong>Additional Charges:</strong> $<?php echo htmlspecialchars($invoice['additional_charges']); ?></p>
             <p><strong>Total:</strong> $<?php echo htmlspecialchars($invoice['total']); ?></p>
        </div>
    <?php if($invoice['notes']): ?>
        <div class="mb-4">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Notes</h2>
            <p><?php echo nl2br(htmlspecialchars($invoice['notes'])); ?></p>
        </div>
    <?php endif; ?>
    <?php if($invoice['footer']): ?>
        <div class="mb-4">
             <h2 class="text-xl font-bold text-gray-800 mb-4">Footer</h2>
                <p><?php echo nl2br(htmlspecialchars($invoice['footer'])); ?></p>
          </div>
        <?php endif; ?>
          <div class="mb-4">
            <a href="edit_invoice.php?id=<?php echo $invoice['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Edit Invoice</a>
             <a href="manage_invoices.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back To Invoices</a>
          </div>
</div>
<?php
// Include footer
require 'footer.php';
?>