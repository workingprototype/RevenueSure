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
     echo "<script>alert('Invoice not found.'); window.location.href='manage_invoices.php';</script>";
    exit;
}
// Fetch Invoice Items
$stmt = $conn->prepare("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id");
$stmt->bindParam(':invoice_id', $invoice_id);
$stmt->execute();
$invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Invoice Details
       $lead_id = !empty($_POST['lead_id']) ? $_POST['lead_id'] : null;
    $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
    $issue_date = $_POST['issue_date'];
    $due_date = $_POST['due_date'];
    $bill_to_name = $_POST['bill_to_name'];
    $bill_to_address = $_POST['bill_to_address'];
    $bill_to_email = $_POST['bill_to_email'];
     $bill_to_phone = $_POST['bill_to_phone'];
    $ship_to_address = $_POST['ship_to_address'];
    $payment_terms = $_POST['payment_terms'];
    $notes = $_POST['notes'];
    $footer = $_POST['footer'];
    $items = $_POST['items'] ?? [];
      $additional_charges = $_POST['additional_charges'] ? $_POST['additional_charges'] : 0;


    // Calculate total
     $subtotal = 0;
      foreach ($items as $item) {
            $subtotal += $item['subtotal'];
        }
    $tax = 0;
    foreach ($items as $item) {
          $tax += $item['tax'];
        }
     $discount = 0;
    foreach ($items as $item) {
         $discount += $item['discount'];
     }
    $total = ($subtotal + $tax + $additional_charges) - $discount;

    // Update Invoice Data
    $stmt = $conn->prepare("UPDATE invoices SET lead_id = :lead_id, customer_id = :customer_id, issue_date = :issue_date, due_date = :due_date, bill_to_name = :bill_to_name, bill_to_address = :bill_to_address, bill_to_email = :bill_to_email, bill_to_phone = :bill_to_phone, ship_to_address = :ship_to_address, subtotal = :subtotal, tax = :tax, discount = :discount, additional_charges = :additional_charges, total = :total, payment_terms = :payment_terms, notes = :notes, footer = :footer WHERE id = :invoice_id");

      $stmt->bindParam(':lead_id', $lead_id);
        $stmt->bindParam(':customer_id', $customer_id);
    $stmt->bindParam(':issue_date', $issue_date);
    $stmt->bindParam(':due_date', $due_date);
      $stmt->bindParam(':bill_to_name', $bill_to_name);
     $stmt->bindParam(':bill_to_address', $bill_to_address);
    $stmt->bindParam(':bill_to_email', $bill_to_email);
        $stmt->bindParam(':bill_to_phone', $bill_to_phone);
    $stmt->bindParam(':ship_to_address', $ship_to_address);
     $stmt->bindParam(':subtotal', $subtotal);
     $stmt->bindParam(':tax', $tax);
     $stmt->bindParam(':discount', $discount);
          $stmt->bindParam(':additional_charges', $additional_charges);
    $stmt->bindParam(':total', $total);
     $stmt->bindParam(':payment_terms', $payment_terms);
      $stmt->bindParam(':notes', $notes);
         $stmt->bindParam(':footer', $footer);
    $stmt->bindParam(':invoice_id', $invoice_id);

    if ($stmt->execute()) {

            // Delete existing items
           $stmt = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = :invoice_id");
          $stmt->bindParam(':invoice_id', $invoice_id);
         $stmt->execute();

         // Insert new invoice items
         foreach ($items as $item) {
            $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_service, quantity, unit_price, tax, discount, subtotal) VALUES (:invoice_id, :product_service, :quantity, :unit_price, :tax, :discount, :subtotal)");
           $stmt->bindParam(':invoice_id', $invoice_id);
            $stmt->bindParam(':product_service', $item['product_service']);
             $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':unit_price', $item['unit_price']);
              $stmt->bindParam(':tax', $item['tax']);
               $stmt->bindParam(':discount', $item['discount']);
            $stmt->bindParam(':subtotal', $item['subtotal']);
              $stmt->execute();
         }
           $success = "Invoice updated successfully!";
           header("Location: view_invoice.php?id=$invoice_id&success=true");
            exit();
        } else {
           $error = "Error updating invoice.";
        }
}
// Fetch leads for the dropdown
$stmt = $conn->prepare("SELECT id, name, email, phone FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
 // Fetch customers for the dropdown
$stmt = $conn->prepare("SELECT id, name, email, phone, address FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Include header
require 'header.php';
?>

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Invoice</h1>
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
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
               <div class="mb-4">
                <label for="lead_customer_type" class="block text-gray-700">Invoice For</label>
                  <select name="lead_customer_type" id="lead_customer_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showCustomerLeadDetails(this.value)">
                        <option value="" <?php if($invoice['lead_id'] == null && $invoice['customer_id'] == null) echo 'selected';?>>Select</option>
                    <option value="lead" <?php if($invoice['lead_id'] != null ) echo 'selected';?>>Lead</option>
                   <option value="customer"  <?php if($invoice['customer_id'] != null ) echo 'selected';?>>Customer</option>
                </select>
              </div>
             <div class="mb-4 <?php if($invoice['lead_id'] == null) echo 'hidden'; ?>" id="lead_select_container">
                 <label for="lead_id" class="block text-gray-700">Select Lead</label>
                    <select name="lead_id" id="lead_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="updateLeadInfo(this)">
                          <option value="">Select Lead</option>
                        <?php foreach ($leads as $lead): ?>
                            <option value="<?php echo $lead['id']; ?>" data-name="<?php echo $lead['name']; ?>" data-email="<?php echo $lead['email']; ?>" data-phone="<?php echo $lead['phone']; ?>"  <?php if($lead['id'] == $invoice['lead_id']) echo 'selected';?>>
                                <?php echo htmlspecialchars($lead['name']); ?>
                            </option>
                        <?php endforeach; ?>
                     </select>
               </div>
                 <div class="mb-4 <?php if($invoice['customer_id'] == null) echo 'hidden'; ?>" id="customer_select_container">
                 <label for="customer_id" class="block text-gray-700">Select Customer</label>
                   <select name="customer_id" id="customer_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="updateCustomerInfo(this)">
                       <option value="">Select Customer</option>
                    <?php foreach ($customers as $customer): ?>
                         <option value="<?php echo $customer['id']; ?>" data-name="<?php echo $customer['name']; ?>" data-email="<?php echo $customer['email']; ?>" data-phone="<?php echo $customer['phone']; ?>" data-address="<?php echo $customer['address']; ?>" <?php if($customer['id'] == $invoice['customer_id']) echo 'selected';?>>
                            <?php echo htmlspecialchars($customer['name']); ?>
                         </option>
                    <?php endforeach; ?>
                   </select>
              </div>
            <div class="mb-4">
                <label for="invoice_number" class="block text-gray-700">Invoice Number</label>
                <input type="text" name="invoice_number" id="invoice_number" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo $invoice['invoice_number']; ?>" readonly>
            </div>
             <div class="mb-4">
                <label for="bill_to_name" class="block text-gray-700">Bill To Name</label>
                 <input type="text" name="bill_to_name" id="bill_to_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($invoice['bill_to_name']); ?>" required>
            </div>
            <div class="mb-4">
                <label for="bill_to_address" class="block text-gray-700">Bill To Address</label>
                <input type="text" name="bill_to_address" id="bill_to_address" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($invoice['bill_to_address']); ?>">
            </div>
            <div class="mb-4">
                <label for="bill_to_email" class="block text-gray-700">Bill To Email</label>
                 <input type="email" name="bill_to_email" id="bill_to_email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($invoice['bill_to_email']); ?>" >
            </div>
               <div class="mb-4">
                 <label for="bill_to_phone" class="block text-gray-700">Bill To Phone</label>
                <input type="text" name="bill_to_phone" id="bill_to_phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($invoice['bill_to_phone']); ?>" >
            </div>
              <div class="mb-4">
                <label for="ship_to_address" class="block text-gray-700">Ship To Address (Optional)</label>
                <input type="text" name="ship_to_address" id="ship_to_address" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($invoice['ship_to_address']); ?>">
             </div>
           <div class="mb-4">
                <label for="issue_date" class="block text-gray-700">Issue Date</label>
                <input type="date" name="issue_date" id="issue_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo $invoice['issue_date']; ?>" required>
            </div>
            <div class="mb-4">
                <label for="due_date" class="block text-gray-700">Due Date</label>
                <input type="date" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo $invoice['due_date']; ?>" required>
            </div>
               <!-- Items -->
            <div class="mb-4">
             <h3 class="text-xl font-bold text-gray-800 mb-4">Invoice Items</h3>
             <div id="invoice_items_container">
                 <?php if($invoice_items): ?>
                  <?php foreach ($invoice_items as $index => $item): ?>
                     <div class="flex gap-4 mb-4 border-b-2 border-gray-200 pb-4" data-item-id="<?php echo $index; ?>">
                       <div class="flex-1">
                        <label for="item_product_service_<?php echo $index; ?>" class="block text-gray-700">Product/Service</label>
                           <input type="text" name="items[<?php echo $index; ?>][product_service]" id="item_product_service_<?php echo $index; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($item['product_service']); ?>" required>
                     </div>
                       <div class="flex-1">
                          <label for="item_quantity_<?php echo $index; ?>" class="block text-gray-700">Quantity</label>
                           <input type="number" name="items[<?php echo $index; ?>][quantity]" id="item_quantity_<?php echo $index; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="calculateItemSubtotal(this)" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" required>
                     </div>
                     <div class="flex-1">
                       <label for="item_unit_price_<?php echo $index; ?>" class="block text-gray-700">Unit Price</label>
                      <input type="number" name="items[<?php echo $index; ?>][unit_price]" id="item_unit_price_<?php echo $index; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="calculateItemSubtotal(this)" value="<?php echo htmlspecialchars($item['unit_price']); ?>" required>
                    </div>
                        <div class="flex-1">
                          <label for="item_tax_<?php echo $index; ?>" class="block text-gray-700">Tax</label>
                             <input type="number" name="items[<?php echo $index; ?>][tax]" id="item_tax_<?php echo $index; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($item['tax']); ?>" onchange="calculateItemSubtotal(this)" >
                       </div>
                        <div class="flex-1">
                              <label for="item_discount_<?php echo $index; ?>" class="block text-gray-700">Discount</label>
                                <input type="number" name="items[<?php echo $index; ?>][discount]" id="item_discount_<?php echo $index; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($item['discount']); ?>"  onchange="calculateItemSubtotal(this)" >
                         </div>
                        <div class="flex-1">
                            <label for="item_subtotal_<?php echo $index; ?>" class="block text-gray-700">Subtotal</label>
                             <input type="number" name="items[<?php echo $index; ?>][subtotal]" id="item_subtotal_<?php echo $index; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($item['subtotal']); ?>"  readonly>
                       </div>
                  </div>
                   <?php endforeach; ?>
                 <?php endif; ?>
            </div>
            <button type="button" id="add_item" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Item</button>
        </div>
              <div class="mb-4">
                <label for="additional_charges" class="block text-gray-700">Additional Charges</label>
                <input type="number" name="additional_charges" id="additional_charges" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($invoice['additional_charges']); ?>" onchange="calculateTotal()">
            </div>
              <div class="mb-4">
                <label for="payment_terms" class="block text-gray-700">Payment Terms</label>
                   <select name="payment_terms" id="payment_terms" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="Net 15"  <?php if($invoice['payment_terms'] == 'Net 15') echo 'selected'; ?>>Net 15</option>
                             <option value="Net 30"  <?php if($invoice['payment_terms'] == 'Net 30') echo 'selected'; ?>>Net 30</option>
                             <option value="Due on Receipt" <?php if($invoice['payment_terms'] == 'Due on Receipt') echo 'selected'; ?>>Due on Receipt</option>
                                <option value="Custom" <?php if($invoice['payment_terms'] == 'Custom') echo 'selected'; ?>>Custom</option>
                    </select>
              </div>
              <div class="mb-4">
                 <label for="notes" class="block text-gray-700">Notes</label>
                <textarea name="notes" id="notes" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
             </div>
             <div class="mb-4">
               <label for="footer" class="block text-gray-700">Footer</label>
                <textarea name="footer" id="footer" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($invoice['footer']); ?></textarea>
             </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Invoice</button>
        </form>
    </div>
<script>
        let item_count = <?php echo count($invoice_items); ?>;
    function showCustomerLeadDetails(type){
            const leadSelectContainer = document.getElementById('lead_select_container');
            const customerSelectContainer = document.getElementById('customer_select_container');
             if(type == 'lead') {
                  leadSelectContainer.classList.remove('hidden');
                customerSelectContainer.classList.add('hidden');
            } else if (type == 'customer') {
                customerSelectContainer.classList.remove('hidden');
                leadSelectContainer.classList.add('hidden');
             }else {
                   leadSelectContainer.classList.add('hidden');
                customerSelectContainer.classList.add('hidden');
                  document.getElementById('bill_to_name').value = "";
                document.getElementById('bill_to_address').value = "";
                document.getElementById('bill_to_email').value = "";
                 document.getElementById('bill_to_phone').value = "";
             }
      }

    function updateLeadInfo(selectElement) {
         const selectedOption = selectElement.options[selectElement.selectedIndex];
       if(selectedOption.value != ""){
           const name = selectedOption.dataset.name;
            const email = selectedOption.dataset.email;
             const phone = selectedOption.dataset.phone;
           document.getElementById('bill_to_name').value = name;
           document.getElementById('bill_to_email').value = email;
            document.getElementById('bill_to_phone').value = phone;
        }else {
           document.getElementById('bill_to_name').value = "";
            document.getElementById('bill_to_email').value = "";
            document.getElementById('bill_to_phone').value = "";
        }
     }
   function updateCustomerInfo(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if(selectedOption.value != ""){
           const name = selectedOption.dataset.name;
            const email = selectedOption.dataset.email;
            const phone = selectedOption.dataset.phone;
              const address = selectedOption.dataset.address;
               document.getElementById('bill_to_name').value = name;
             document.getElementById('bill_to_email').value = email;
            document.getElementById('bill_to_phone').value = phone;
             document.getElementById('bill_to_address').value = address;
        }else {
             document.getElementById('bill_to_name').value = "";
              document.getElementById('bill_to_email').value = "";
            document.getElementById('bill_to_phone').value = "";
             document.getElementById('bill_to_address').value = "";
        }
    }
        document.getElementById('add_item').addEventListener('click', function () {
           const container = document.getElementById('invoice_items_container');
          const newItem = document.createElement('div');
        newItem.classList.add('flex', 'gap-4', 'mb-4', 'border-b-2', 'border-gray-200', 'pb-4');
         newItem.dataset.item_id = item_count;
          newItem.innerHTML = `
              <div class="flex-1">
                 <label for="item_product_service_${item_count}" class="block text-gray-700">Product/Service</label>
                     <input type="text" name="items[${item_count}][product_service]" id="item_product_service_${item_count}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                  <div class="flex-1">
                   <label for="item_quantity_${item_count}" class="block text-gray-700">Quantity</label>
                    <input type="number" name="items[${item_count}][quantity]" id="item_quantity_${item_count}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="calculateItemSubtotal(this)" value="1" min="1" required>
                  </div>
                 <div class="flex-1">
                     <label for="item_unit_price_${item_count}" class="block text-gray-700">Unit Price</label>
                     <input type="number" name="items[${item_count}][unit_price]" id="item_unit_price_${item_count}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="calculateItemSubtotal(this)" required>
                </div>
                 <div class="flex-1">
                        <label for="item_tax_${item_count}" class="block text-gray-700">Tax</label>
                        <input type="number" name="items[${item_count}][tax]" id="item_tax_${item_count}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="0" onchange="calculateItemSubtotal(this)">
                 </div>
                 <div class="flex-1">
                    <label for="item_discount_${item_count}" class="block text-gray-700">Discount</label>
                        <input type="number" name="items[${item_count}][discount]" id="item_discount_${item_count}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="0" onchange="calculateItemSubtotal(this)" >
                </div>
                 <div class="flex-1">
                     <label for="item_subtotal_${item_count}" class="block text-gray-700">Subtotal</label>
                    <input type="number" name="items[${item_count}][subtotal]" id="item_subtotal_${item_count}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="0"  readonly>
                </div>
            `;
           container.appendChild(newItem);
           item_count++;
        });
       function calculateItemSubtotal(input) {
          const itemId = input.closest('[data-item-id]').dataset.item_id;
            const quantity = document.getElementById(`item_quantity_${itemId}`).value;
            const unitPrice = document.getElementById(`item_unit_price_${itemId}`).value;
            const taxInput = document.getElementById(`item_tax_${itemId}`);
            const discountInput = document.getElementById(`item_discount_${itemId}`);

             const tax = taxInput.value ? parseFloat(taxInput.value) : 0;
           const discount = discountInput.value ? parseFloat(discountInput.value) : 0;
            const subtotalInput = document.getElementById(`item_subtotal_${itemId}`);
            const subtotal = (quantity * unitPrice) + tax - discount;

          subtotalInput.value =  subtotal.toFixed(2);
           calculateTotal();
        }
       function calculateTotal() {
           let subtotal = 0;
         let total_tax = 0;
          let total_discount = 0;
             const items = document.querySelectorAll('[data-item-id]');
           items.forEach(item => {
              const itemId = item.dataset.item_id;
               const subtotalValue = document.getElementById(`item_subtotal_${itemId}`).value;
               const taxInput = document.getElementById(`item_tax_${itemId}`);
              const discountInput = document.getElementById(`item_discount_${itemId}`);
               const tax = taxInput.value ? parseFloat(taxInput.value) : 0;
           const discount = discountInput.value ? parseFloat(discountInput.value) : 0;
                subtotal += parseFloat(subtotalValue);
               total_tax += tax;
                 total_discount += discount
             });
             const additionalChargesInput = document.getElementById('additional_charges');
              const additional_charges = additionalChargesInput.value ? parseFloat(additionalChargesInput.value) : 0;

          const total = (subtotal  + additional_charges ) - total_discount;
          document.getElementById('total').value = total.toFixed(2);
      }
         document.getElementById('payment_terms').addEventListener('change', function() {
        const issueDateInput = document.getElementById('issue_date');
         const dueDateInput = document.getElementById('due_date');
          const paymentTerms = this.value;
         if(paymentTerms != 'Custom'){
                const issueDate = new Date(issueDateInput.value);
                  let dueDate = null;
                if (paymentTerms === 'Net 15') {
                    dueDate = new Date(issueDate.getTime() + (15 * 24 * 60 * 60 * 1000));
                 } else if (paymentTerms === 'Net 30') {
                        dueDate = new Date(issueDate.getTime() + (30 * 24 * 60 * 60 * 1000));
                } else if (paymentTerms === 'Due on Receipt') {
                      dueDate = issueDate;
               }
               if (dueDate) {
                   const formattedDueDate = dueDate.toISOString().split('T')[0];
                      dueDateInput.value = formattedDueDate;
                }
          }
    });
  showCustomerLeadDetails(document.getElementById('lead_customer_type').value);
</script>
<?php
// Include footer
require 'footer.php';
?>