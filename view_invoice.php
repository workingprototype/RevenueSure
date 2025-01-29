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

// Decode JSON tax array
$itemized_tax = json_decode($invoice['tax'], true);

// Calculate subtotal from individual items
$subtotal = 0;
if ($invoice_items){
   foreach($invoice_items as $item){
       $subtotal += $item['subtotal'];
    }
}

// Calculate total tax
$total_tax = array_sum($itemized_tax);

// Calculate the total discount
$discount = 0;
if ($invoice['discount_type'] == 'percentage') {
    $discount = $subtotal * ($invoice['discount_amount'] / 100);
 } else {
    $discount = $invoice['discount_amount'];
}

// Calculate total
$total = ($subtotal + $total_tax + $invoice['additional_charges']) - $discount;

// Include header
require 'header.php';
?>

<div class="container mx-auto mt-10 p-4">
  <div class="bg-gray-100 p-8 rounded-lg shadow-md">

    <div class="flex justify-between items-start mb-8">
        <div>
          <div class="bg-gray-800 text-white p-4 mb-4 rounded-md">
              <h1 class="text-2xl font-bold"><?php echo "Company Name"?></h1>
             <p class="text-sm"><?php echo "Company Tag Line"; ?></p>
          </div>
           <div class="mb-4">
               <p class="text-gray-700 text-sm"><?php echo "Company Address Line 1"?></p>
              <p class="text-gray-700 text-sm"><?php echo "Company Address Line 2"?></p>
              <p class="text-gray-700 text-sm"><?php echo "Company Phone Number"?></p>
            </div>
        </div>
         <div class="text-right">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">INVOICE</h2>
             <p class="text-gray-700 text-sm"><strong>Date:</strong> <?php echo htmlspecialchars($invoice['issue_date']); ?></p>
            <p class="text-gray-700 text-sm"><strong>Invoice #:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
               <p class="text-gray-700 text-sm"><strong>FOR:</strong> Store expansion</p>
        </div>
    </div>
    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">BILL TO:</h3>
        <p class="text-gray-700 mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($invoice['bill_to_name']); ?></p>
         <p class="text-gray-700 mb-1"><strong>Company:</strong> Downtown Pets</p>
         <p class="text-gray-700 mb-1"><strong>Address:</strong> <?php echo htmlspecialchars($invoice['bill_to_address']); ?></p>
        <p class="text-gray-700 mb-1"><strong>City State Zip:</strong> Manhattan, NY 15161</p>
        <p class="text-gray-700"><strong>Phone:</strong> <?php echo htmlspecialchars($invoice['bill_to_phone']); ?></p>
    </div>
       <div class="overflow-x-auto">
    <table class="w-full text-left bg-gray-700 text-white rounded-lg mb-6">
        <thead class="bg-gray-700 text-white">
            <tr>
                <th class="px-4 py-2">DESCRIPTION</th>
                <th class="px-4 py-2">HOURS</th>
                <th class="px-4 py-2">RATE</th>
                  <th class="px-4 py-2">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <?php if($invoice_items): ?>
                <?php foreach ($invoice_items as $item): ?>
                <tr class="border-b border-gray-500 text-gray-200">
                    <td class="px-4 py-2"><?php echo htmlspecialchars($item['product_service']); ?></td>
                     <td class="px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td class="px-4 py-2">$<?php echo htmlspecialchars($item['unit_price']); ?></td>
                      <td class="px-4 py-2">$<?php echo htmlspecialchars($item['subtotal']); ?></td>
                </tr>
                 <?php endforeach; ?>
            <?php else: ?>
                <tr>
                  <td colspan="4" class="px-4 py-2 text-center text-gray-600">No items found.</td>
                 </tr>
            <?php endif; ?>
              </tbody>
    </table>
    </div>
      <div class="text-right flex justify-end flex-col gap-2">
          <div class="flex justify-end gap-4">
            <p class="text-gray-700 text-lg font-semibold">SUBTOTAL</p>
            <p class="text-gray-700 text-lg">$<?php echo htmlspecialchars($subtotal); ?></p>
        </div>
          <div class="flex justify-end gap-4">
             <p class="text-gray-700 text-lg font-semibold">TAX RATE</p>
              <p class="text-gray-700 text-lg"> <?php echo htmlspecialchars($invoice['tax_method']); ?></p>
            </div>
             <div class="flex justify-end gap-4">
            <p class="text-gray-700 text-lg font-semibold">SALES TAX</p>
             <p class="text-gray-700 text-lg">$<?php echo htmlspecialchars($total_tax); ?></p>
        </div>
           <div class="flex justify-end gap-4">
              <p class="text-gray-700 text-lg font-semibold">OTHER</p>
                <p class="text-gray-700 text-lg">$<?php echo htmlspecialchars($invoice['additional_charges']); ?></p>
           </div>
         <div class="flex justify-end gap-4 p-2 bg-gray-800 text-white rounded-lg">
            <p class="text-lg font-bold">TOTAL</p>
           <p class="text-lg font-bold">$<?php echo htmlspecialchars($total); ?></p>
        </div>
     </div>
         <div class="mt-4 text-gray-600 text-sm">
          <p>Make all checks payable to <?php echo "Company Name"?></p>
             <p>Total due in <?php echo "Due Date in days ( 15 or 10 etc )"?>. Overdue accounts subject to a service charge of <?php echo "10"?>% per <?php echo "month"?>.</p>
              <p class="mt-4"> <?php echo "Thank you for your business! or Other Custom Message"?></p>
        </div>
            <div class="mt-4">
            <a href="edit_invoice.php?id=<?php echo $invoice['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Edit Invoice</a>
             <a href="manage_invoices.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back To Invoices</a>
           </div>
   </div>
</div>


<?php
// Include footer
require 'footer.php';
?>