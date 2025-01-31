<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$show_success_toast = false;
$new_category_id = null;


if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
     if (isset($_POST['name']) && !isset($_POST['add_category_name'])) {
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
                $stmt = $conn->prepare("INSERT INTO expenses (name, category_id, amount, expense_date, project_id, user_id, invoice_id, payment_mode, transaction_nature, notes) VALUES (:name, :category_id, :amount, :expense_date, :project_id, :user_id, :invoice_id, :payment_mode, :transaction_nature, :notes)");
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
                    $expense_id = $conn->lastInsertId();
                    $success = "Expense recorded successfully!";
                    header("Location: view_expense.php?id=$expense_id&success=true");
                       exit();
                 } else {
                      $error = "Error recording expense.";
                  }
             }
         }
        if (isset($_POST['add_category_name'])) {
            $new_category_name = trim($_POST['add_category_name']);
                 if (empty($new_category_name)) {
                      $error = "Category name is required.";
                    } else {
                      // Check if the category already exists
                    $stmt = $conn->prepare("SELECT id FROM expense_categories WHERE name = :name");
                    $stmt->bindParam(':name', $new_category_name);
                      $stmt->execute();
                      if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                          $error = "A category with this name already exists.";
                        } else {
                         // Insert the category
                             $stmt = $conn->prepare("INSERT INTO expense_categories (name) VALUES (:name)");
                             $stmt->bindParam(':name', $new_category_name);

                             if ($stmt->execute()) {
                                    $success = "Category added successfully!";
                                   $show_success_toast = true;
                                  $new_category_id = $conn->lastInsertId();

                                 } else {
                                        $error = "Error adding category.";
                                     }
                           }
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

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Record Expense</h1>

    <!-- Display error message -->
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

       <!-- Record Expense Form -->
    <div class="bg-white p-6 rounded-2xl shadow-xl">
      <form method="POST" action="" enctype="multipart/form-data">
           <div class="mb-4">
             <label for="name" class="block text-gray-700">Expense Name</label>
              <input type="text" name="name" id="name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
           </div>
            <div class="mb-4">
              <label for="category_id" class="block text-gray-700">Expense Category</label>
                 <div class="relative">
                     <select name="category_id" id="category_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                        <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                  <option value="<?php echo $category['id']; ?>" <?php if($new_category_id == $category['id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                      </select>
                         <button type="button"  onclick="openCategoryModal()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 transition duration-200">
                            <i class="fas fa-plus-circle"></i>
                         </button>
                  </div>
             </div>
            <div class="mb-4">
                 <label for="amount" class="block text-gray-700">Amount</label>
                <input type="number" name="amount" id="amount" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" min="0" step="0.01" required>
           </div>
            <div class="mb-4">
              <label for="expense_date" class="block text-gray-700">Expense Date</label>
                 <input type="date" name="expense_date" id="expense_date" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
             </div>
               <div class="mb-4">
                <label for="project_id" class="block text-gray-700">Project Association (Optional)</label>
                   <select name="project_id" id="project_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                      <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                           <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
            </div>
            <div class="mb-4">
               <label for="user_id" class="block text-gray-700">User/Employee</label>
                   <select name="user_id" id="user_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                       <option value="">Select User</option>
                       <?php foreach ($users as $user): ?>
                          <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                       <?php endforeach; ?>
                  </select>
             </div>
            <div class="mb-4">
                <label for="invoice_id" class="block text-gray-700">Invoice Association (Optional)</label>
                <select name="invoice_id" id="invoice_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                     <option value="">Select Invoice</option>
                     <?php foreach ($invoices as $invoice): ?>
                        <option value="<?php echo $invoice['id']; ?>"><?php echo htmlspecialchars($invoice['invoice_number']); ?></option>
                     <?php endforeach; ?>
                </select>
             </div>
            <div class="mb-4">
                 <label for="payment_mode" class="block text-gray-700">Payment Mode</label>
                   <select name="payment_mode" id="payment_mode" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                          <option value="Cash">Cash</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Online Payment">Online Payment</option>
                        <option value="Check">Check</option>
                    </select>
              </div>
             <div class="mb-4">
                <label for="transaction_nature" class="block text-gray-700">Nature of Transaction</label>
               <select name="transaction_nature" id="transaction_nature" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                    <option value="Reimbursable">Reimbursable</option>
                     <option value="Business Expense">Business Expense</option>
                      <option value="Personal Expense">Personal Expense</option>
                </select>
            </div>
             <div class="mb-4">
               <label for="notes" class="block text-gray-700">Notes</label>
                <textarea name="notes" id="notes" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
           </div>
            <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">Record Expense</button>
        </form>
      <!-- Category Add Modal -->
           <div id="categoryModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
             <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
               <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                  <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                  <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                           <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-headline">
                             Add Expense Category
                           </h3>
                         <form method="POST" action="">
                             <div class="mb-4">
                                 <label for="add_category_name" class="block text-gray-700">Category Name</label>
                                <input type="text" name="add_category_name" id="add_category_name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                              </div>
                               <div class="flex justify-end">
                                   <button type="submit"  class="bg-blue-700 text-white px-4 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Category</button>
                                    <button type="button" onclick="closeCategoryModal()" class="bg-gray-700 text-white px-4 py-3 rounded-xl hover:bg-gray-900 transition duration-300 shadow-md ml-2">Cancel</button>
                               </div>
                         </form>
                     </div>
                 </div>
            </div>
        </div>
          <!-- Success Toast -->
          <div
          id="toast"
           class="fixed top-12 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 hidden"
             role="alert"
              >
              <div class="flex items-center">
                    <span class="mr-2"><i class="fas fa-check-circle"></i></span>
                    <span>Category added successfully!</span>
                       <button onclick="closeToast()" class="ml-2 text-gray-600 hover:text-gray-800" > <i class="fas fa-times"></i></button>
                 </div>
          </div>
    </div>
</div>
 <script>
      function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        }
        function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        }
     function closeToast(){
          document.getElementById('toast').classList.add('hidden');
      }
       <?php if ($show_success_toast): ?>
           document.addEventListener('DOMContentLoaded', function() {
                  document.getElementById('toast').classList.remove('hidden');
                  setTimeout(() => {
                         document.getElementById('toast').classList.add('hidden');
                   }, 3000);
           });
         <?php endif; ?>
 </script>
<?php
// Include footer
require 'footer.php';
?>