<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// 1. Fetch Unreconciled Ledger Entries
$stmt = $conn->prepare("SELECT id, transaction_date, description, debit_amount, credit_amount, currency FROM ledger_entries WHERE reconciliation_status = 'Unreconciled'");
$stmt->execute();
$unreconciled_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Process Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reconciliation_date = $_POST['reconciliation_date'];
    $bank_statement_reference = $_POST['bank_statement_reference'];
    $ledger_entries = $_POST['ledger_entries'] ?? [];
    $total_difference_amount = $_POST['total_difference_amount'] ?? 0.00;

    // Validate Inputs
    if (empty($reconciliation_date) || empty($ledger_entries)) {
        $error = "Reconciliation Date and Ledger Entries are required.";
    }  else {
          // Begin transaction
        $conn->beginTransaction();
         try {
             // 3. Create Reconciliation Record
               $stmt = $conn->prepare("INSERT INTO reconciliation_records (reconciliation_date, bank_statement_reference, total_difference_amount) VALUES (:reconciliation_date, :bank_statement_reference, :total_difference_amount)");
                $stmt->bindParam(':reconciliation_date', $reconciliation_date);
                $stmt->bindParam(':bank_statement_reference', $bank_statement_reference);
                $stmt->bindParam(':total_difference_amount', $total_difference_amount);
               $stmt->execute();
            $reconciliation_id = $conn->lastInsertId();
               // 4. Update Ledger Entries and Reconciliation Junction Table
                  $stmt = $conn->prepare("UPDATE ledger_entries SET reconciliation_status = 'Matched' WHERE id = :ledger_entry_id");
                  $stmt_junction = $conn->prepare("INSERT INTO reconciliation_ledger_entries (reconciliation_id, ledger_entry_id, difference_amount) VALUES (:reconciliation_id, :ledger_entry_id, :difference_amount)");
                     foreach ($ledger_entries as $ledger_entry_id => $difference_amount) {
                               //Update ledeger entries reconcilliation status.
                              $stmt->bindParam(':ledger_entry_id', $ledger_entry_id);
                              $stmt->execute();
                                //Record junction.
                              $stmt_junction->bindParam(':reconciliation_id', $reconciliation_id);
                               $stmt_junction->bindParam(':ledger_entry_id', $ledger_entry_id);
                               $stmt_junction->bindParam(':difference_amount', $difference_amount);
                               $stmt_junction->execute();
                        }

                       // Commit transaction
                       $conn->commit();
                       $success = "Reconciliation complete!";
                       header("Location: " . BASE_URL . "accounting/reconciliation?success=true");
                          exit();
            } catch (Exception $e) {
                     // Rollback transaction on error
                    $conn->rollBack();
                   $error = "Error during reconciliation. Please try again.";
            }
         }
}

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Reconciliation</h1>

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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Unreconciled Ledger Entries -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Unreconciled Ledger Entries</h2>
            <form method="POST" action="">
            <?php echo csrfTokenInput(); ?>
                <div class="mb-4">
                    <label for="reconciliation_date" class="block text-gray-700">Reconciliation Date:</label>
                    <input type="date" name="reconciliation_date" id="reconciliation_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label for="bank_statement_reference" class="block text-gray-700">Bank Statement Reference:</label>
                    <input type="text" name="bank_statement_reference" id="bank_statement_reference" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                  <!--  <input type="file" name="bank_statement" id="bank_statement" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">  Alternative file upload -->
               </div>
                 <div class="overflow-x-auto">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Select</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Debit</th>
                                 <th class="px-4 py-2">Credit</th>
                                <th class="px-4 py-2">Currency</th>
                                <th class="px-4 py-2">Difference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($unreconciled_entries): ?>
                                <?php foreach ($unreconciled_entries as $entry): ?>
                                    <tr>
                                        <td class="border px-4 py-2">
                                          <input type="checkbox"
                                             name="ledger_entries[<?php echo htmlspecialchars($entry['id']); ?>]"
                                              value="0"
                                              data-debit="<?php echo htmlspecialchars($entry['debit_amount']); ?>"
                                              data-credit="<?php echo htmlspecialchars($entry['credit_amount']); ?>"
                                             class="ledger-entry-checkbox">

                                        </td>
                                         <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['transaction_date']); ?></td>
                                         <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['description']); ?></td>
                                         <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['debit_amount']); ?></td>
                                            <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['credit_amount']); ?></td>
                                         <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['currency']); ?></td>
                                         <td class="border px-4 py-2">
                                         <input type="number" name="difference_amount[]" data-ledger-id="<?php echo htmlspecialchars($entry['id']); ?>" class="difference-amount" value="0">
                                         </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-4 py-2 text-center">No unreconciled ledger entries found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                 </div>
           

        </div>

        <!-- Bank Statement Details -->
       

    </div>
     <div class="text-right flex justify-end">
       <p>Total difference amount: <span id="total-difference-amount">0</span></p>
     </div>
        <button type="submit" name="add_comment" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4 flex justify-end">Reconcile</button>
    </form>
</div>

<script>
 //   document.addEventListener('DOMContentLoaded', function() {
   // const items = document.querySelectorAll('table tbody tr');
const reconciliationForm = document.querySelector('form');
const checkboxes = document.querySelectorAll('.ledger-entry-checkbox');

checkboxes.forEach(function(checkbox) {
  checkbox.addEventListener('change', function() {
    let totalDifferenceAmount = 0;

    checkboxes.forEach(function(item) {
        let debit = parseFloat(item.dataset.debit) || 0;
        let credit = parseFloat(item.dataset.credit) || 0;
        if(item.checked){
            totalDifferenceAmount +=  credit - debit;
        }

    });
     document.getElementById('total-difference-amount').textContent = totalDifferenceAmount.toFixed(2)
  });
});

        reconciliationForm.addEventListener('submit', function(event) {
             event.preventDefault();

           checkboxes.forEach(function(checkbox) {
               if(!checkbox.checked){
                checkbox.value = "0";
                 }else {
                     let debit = parseFloat(checkbox.dataset.debit) || 0;
                      let credit = parseFloat(checkbox.dataset.credit) || 0;
                    let diff = credit-debit;
                    checkbox.value = diff;

                }
           });
         this.submit();
        });
    </script>

<?php
/*
<!-- Implementation Notes:
- Implement file upload functionality using a library or custom script.
- Use javascript to dynamically calculate the difference amount and total at client side before submission.
- SQL queries need to be adjusted based on your database schema (relationships, data types).
 -->
 */
?>