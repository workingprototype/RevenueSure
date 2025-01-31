<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$expense_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch expense details
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id = :id");
$stmt->bindParam(':id', $expense_id);
$stmt->execute();
$expense = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) {
    header("Location: manage_expenses.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $expense_date = $_POST['expense_date'];
    $project_id = $_POST['project_id'] ?? null;
    $user_id = $_POST['user_id'];
    $invoice_id = $_POST['invoice_id'] ?? null;
    $payment_mode = $_POST['payment_mode'];
    $transaction_nature = $_POST['transaction_nature'];
      $notes = $_POST['notes'];

        if (empty($name) || empty($amount) || empty($expense_date) || empty($category_id) || empty($user_id) || empty($payment_mode) || empty($transaction_nature)) {
            $error = "All fields are required.";
        } else {
            $stmt = $conn->prepare("UPDATE expenses SET name = :name, category_id = :category_id, amount = :amount, expense_date = :expense_date, project_id = :project_id, user_id = :user_id, invoice_id = :invoice_id, payment_mode = :payment_mode, transaction_nature = :transaction_nature, notes = :notes WHERE id = :id");
            $stmt->bindParam(':id', $expense_id);
           $stmt->bindParam(':name', $name);
           $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':expense_date', $expense_date);
            $stmt->bindParam(':project_id', $project_id);
            $stmt->bindParam(':user_id', $user_id);
           $stmt->bindParam(':invoice_id', $invoice_id);
           $stmt->bindParam(':payment_mode', $payment_mode);
            $stmt->bindParam(':transaction_nature', $transaction_nature);
            $stmt->bindParam(':notes', $notes);
             if ($stmt->execute()) {
                    $success = "Expense record updated successfully!";
                     header("Location: view_expense.php?id=$expense_id&success=true");
                         exit();
                    } else {
                         $error = "Error updating expense.";
                        }
        }
}

// Fetch categories for the dropdown
$stmt = $conn->prepare("SELECT * FROM expense_categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users for the dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects for dropdown
$stmt = $conn->prepare("SELECT id, name FROM projects");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch invoices for dropdown
$stmt = $conn->prepare("SELECT id, invoice_number FROM invoices");
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Expense</h1>

<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>
    <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
           Expense record updated successfully!
        </div>
    <?php endif; ?>
     <div class="bg-white p-6 rounded-lg shadow-md">
      <form method="POST" action="">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Expense Name</label>
                 <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($expense['name']); ?>" required>
            </div>
           <div class="mb-4">
                 <label for="category_id" class="block text-gray-700">Expense Category</label>
                  <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                      <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                             <option value="<?php echo $category['id']; ?>" <?php if ($expense['category_id'] == $category['id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                         <?php endforeach; ?>
                  </select>
             </div>
            <div class="mb-4">
                <label for="amount" class="block text-gray-700">Amount</label>
                  <input type="number" name="amount" id="amount" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($expense['amount']); ?>" min="0" step="0.01" required>
             </div>
            <div class="mb-4">
                  <label for="expense_date" class="block text-gray-700">Expense Date</label>
                <input type="date" name="expense_date" id="expense_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($expense['expense_date']); ?>" required>
             </div>
                <div class="mb-4">
                      <label for="project_id" class="block text-gray-700">Project Association (Optional)</label>
                         <select name="project_id" id="project_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select Project</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>" <?php if ($expense['project_id'] === $project['id']) echo 'selected'; ?>><?php echo htmlspecialchars($project['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                </div>
            <div class="mb-4">
                  <label for="user_id" class="block text-gray-700">User/Employee</label>
                 <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                     <option value="">Select User</option>
                     <?php foreach ($users as $user): ?>
                          <option value="<?php echo $user['id']; ?>" <?php if ($expense['user_id'] === $user['id']) echo 'selected'; ?>><?php echo htmlspecialchars($user['username']); ?></option>
                      <?php endforeach; ?>
                </select>
             </div>
            <div class="mb-4">
                <label for="invoice_id" class="block text-gray-700">Invoice Association (Optional)</label>
                   <select name="invoice_id" id="invoice_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                      <option value="">Select Invoice</option>
                        <?php foreach ($invoices as $invoice): ?>
                            <option value="<?php echo $invoice['id']; ?>" <?php if ($expense['invoice_id'] === $invoice['id']) echo 'selected'; ?>><?php echo htmlspecialchars($invoice['invoice_number']); ?></option>
                       <?php endforeach; ?>
                 </select>
            </div>
           <div class="mb-4">
                <label for="payment_mode" class="block text-gray-700">Payment Mode</label>
                 <select name="payment_mode" id="payment_mode" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                       <option value="Cash" <?php if ($expense['payment_mode'] == 'Cash') echo 'selected'; ?>>Cash</option>
                      <option value="Credit Card" <?php if ($expense['payment_mode'] == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                       <option value="Bank Transfer" <?php if ($expense['payment_mode'] == 'Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
                      <option value="Online Payment" <?php if ($expense['payment_mode'] == 'Online Payment') echo 'selected'; ?>>Online Payment</option>
                      <option value="Check" <?php if ($expense['payment_mode'] == 'Check') echo 'selected'; ?>>Check</option>
                </select>
            </div>
           <div class="mb-4">
              <label for="transaction_nature" class="block text-gray-700">Nature of Transaction</label>
                <select name="transaction_nature" id="transaction_nature" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                    <option value="Reimbursable" <?php if ($expense['transaction_nature'] == 'Reimbursable') echo 'selected'; ?>>Reimbursable</option>
                    <option value="Business Expense" <?php if ($expense['transaction_nature'] == 'Business Expense') echo 'selected'; ?>>Business Expense</option>
                     <option value="Personal Expense" <?php if ($expense['transaction_nature'] == 'Personal Expense') echo 'selected'; ?>>Personal Expense</option>
                  </select>
             </div>
              <div class="mb-4">
                <label for="notes" class="block text-gray-700">Notes</label>
                <textarea name="notes" id="notes" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($expense['notes']); ?></textarea>
             </div>
             <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Expense</button>
            <div class="mt-4">
               <a href="view_expense.php?id=<?php echo $expense['id']; ?>"  class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Expense</a>
           </div>
      </form>
    </div>
<?php
// Include footer
require 'footer.php';
?>