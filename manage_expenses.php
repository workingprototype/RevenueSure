<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$query = "SELECT expenses.*, expense_categories.name as category_name, users.username as user_name
          FROM expenses
          LEFT JOIN expense_categories ON expenses.category_id = expense_categories.id
          LEFT JOIN users ON expenses.user_id = users.id";

$where_conditions = [];
$params = [];

// Search by name, category or employee
if(isset($_GET['search']) && !empty($_GET['search'])){
     $search_term = trim($_GET['search']);
      $where_conditions[] = "(expenses.name LIKE :search OR expense_categories.name LIKE :search OR users.username LIKE :search)";
      $params[':search'] = "%$search_term%";
}
 // Category
if(isset($_GET['category_id']) && !empty($_GET['category_id'])){
     $where_conditions[] = "expenses.category_id = :category_id";
      $params[':category_id'] = $_GET['category_id'];
}
 // User
 if(isset($_GET['user_id']) && !empty($_GET['user_id'])){
    $where_conditions[] = "expenses.user_id = :user_id";
     $params[':user_id'] = $_GET['user_id'];
}
  // Project
if(isset($_GET['project_id']) && !empty($_GET['project_id'])){
    $where_conditions[] = "expenses.project_id = :project_id";
     $params[':project_id'] = $_GET['project_id'];
}

 if(isset($_GET['invoice_id']) && !empty($_GET['invoice_id'])){
        $where_conditions[] = "expenses.invoice_id = :invoice_id";
     $params[':invoice_id'] = $_GET['invoice_id'];
}
  // payment_mode
if(isset($_GET['payment_mode']) && !empty($_GET['payment_mode'])){
      $where_conditions[] = "expenses.payment_mode = :payment_mode";
       $params[':payment_mode'] = $_GET['payment_mode'];
}
 // transaction_nature
if(isset($_GET['transaction_nature']) && !empty($_GET['transaction_nature'])){
        $where_conditions[] = "expenses.transaction_nature = :transaction_nature";
       $params[':transaction_nature'] = $_GET['transaction_nature'];
}
if (!empty($_GET['start_date'])) {
    $where_conditions[] = "expenses.expense_date >= :start_date";
     $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $where_conditions[] = "expenses.expense_date <= :end_date";
     $params[':end_date'] = $_GET['end_date'] . ' 23:59:59';
}

if(!empty($where_conditions)){
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}
  $query .= " ORDER BY expenses.created_at DESC";

// Fetch all expenses
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories
$stmt = $conn->prepare("SELECT * FROM expense_categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users
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
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Expenses</h1>
    <div class="flex justify-between items-center mb-8">
            <a href="add_expense.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
                 <i class="fas fa-plus-circle mr-2"></i> Record Expense
            </a>
               <div class="flex flex-wrap gap-2">
                  <form method="GET" action="" class="flex gap-2">
                      <input type="text" name="search" id="search" placeholder="Search" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
                      <select name="category_id" id="category_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                        <option value="">Select Category</option>
                           <?php foreach ($categories as $category): ?>
                             <option value="<?php echo $category['id']; ?>" <?php if(isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                     </select>
                      <select name="user_id" id="user_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                        <option value="">Select User</option>
                          <?php foreach ($users as $user): ?>
                               <option value="<?php echo $user['id']; ?>" <?php if(isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($user['username']); ?></option>
                           <?php endforeach; ?>
                     </select>
                      <select name="project_id" id="project_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                             <option value="">Select Project</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>" <?php if(isset($_GET['project_id']) && $_GET['project_id'] == $project['id']) echo 'selected'; ?>><?php echo htmlspecialchars($project['name']); ?></option>
                             <?php endforeach; ?>
                       </select>
                        <select name="invoice_id" id="invoice_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                             <option value="">Select Invoice</option>
                            <?php foreach ($invoices as $invoice): ?>
                                <option value="<?php echo $invoice['id']; ?>"  <?php if(isset($_GET['invoice_id']) && $_GET['invoice_id'] == $invoice['id']) echo 'selected'; ?>><?php echo htmlspecialchars($invoice['invoice_number']); ?></option>
                            <?php endforeach; ?>
                       </select>
                       <select name="payment_mode" id="payment_mode" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select Payment Mode</option>
                            <option value="Cash" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Cash') echo 'selected'; ?>>Cash</option>
                            <option value="Credit Card" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                            <option value="Bank Transfer" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
                            <option value="Online Payment" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Online Payment') echo 'selected'; ?>>Online Payment</option>
                            <option value="Check" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Check') echo 'selected'; ?>>Check</option>
                        </select>
                         <select name="transaction_nature" id="transaction_nature" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                           <option value="">Select Transaction</option>
                             <option value="Reimbursable" <?php if(isset($_GET['transaction_nature']) && $_GET['transaction_nature'] == 'Reimbursable') echo 'selected'; ?>>Reimbursable</option>
                             <option value="Business Expense" <?php if(isset($_GET['transaction_nature']) && $_GET['transaction_nature'] == 'Business Expense') echo 'selected'; ?>>Business Expense</option>
                             <option value="Personal Expense" <?php if(isset($_GET['transaction_nature']) && $_GET['transaction_nature'] == 'Personal Expense') echo 'selected'; ?>>Personal Expense</option>
                       </select>
                       <input type="date" name="start_date" id="start_date" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Start Date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                       <input type="date" name="end_date" id="end_date" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="End Date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                      <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">Filter</button>
                  </form>
                     <a href="manage_expenses.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">Clear Filter</a>
              </div>
    </div>
         <div class="flex flex-wrap -mx-4 justify-center">
             <?php if ($expenses): ?>
                <?php foreach ($expenses as $expense): ?>
                    <div class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4 px-4 mb-6 relative">
                       <div class="bg-white border-2 border-gray-100 p-6 rounded-xl shadow-md h-full transform hover:scale-105 transition-transform duration-200 relative" style="background-image: url('assets/bill-paper-bg.png'); background-size: cover;">
                                <div class="bg-white shadow-sm p-4 absolute top-[-10px] left-[-10px] w-full h-full rounded-xl border-2 border-gray-100" style=" transform: rotate(0.9deg);">
                              </div>
                                 <div class="relative">
                                      <div class="flex justify-between items-center">
                                          <h2 class="text-xl font-bold text-gray-800 mb-2  uppercase tracking-wide">
                                               <a href="view_expense.php?id=<?php echo $expense['id']; ?>" class="hover:underline" ><?php echo htmlspecialchars($expense['name']); ?></a>
                                           </h2>
                                               <span class="px-2 py-1 rounded-full text-sm font-medium <?php
                                                   switch ($expense['transaction_nature']) {
                                                         case 'Reimbursable':
                                                           echo 'bg-blue-100 text-blue-800';
                                                            break;
                                                       case 'Business Expense':
                                                          echo 'bg-green-100 text-green-800';
                                                            break;
                                                      case 'Personal Expense':
                                                            echo 'bg-yellow-100 text-yellow-800';
                                                             break;
                                                       default:
                                                        echo 'bg-gray-100 text-gray-800';
                                                            break;
                                                   }
                                              ?>"><?php echo htmlspecialchars($expense['transaction_nature']); ?></span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-2"><i class="fas fa-tag text-gray-500 mr-1"></i> <?php echo htmlspecialchars($expense['category_name'] ? $expense['category_name'] : 'Uncategorized'); ?></p>
                                        <div class="text-center">
                                           <p class="text-2xl font-bold text-blue-700 mb-2">$<?php echo htmlspecialchars($expense['amount']); ?></p>
                                         </div>
                                         <p class="text-gray-600 text-sm">
                                            <i class="fas fa-calendar-day text-gray-500 mr-1"></i> <?php echo htmlspecialchars($expense['expense_date']); ?>
                                        </p>
                                         <p class="text-gray-600 text-sm"><i class="fas fa-user text-gray-500 mr-1"></i><?php echo htmlspecialchars($expense['user_name'] ? $expense['user_name'] : 'N/A'); ?></p>
                                    <div class="flex justify-end items-center">
                                        <a href="view_expense.php?id=<?php echo $expense['id']; ?>" class="text-purple-600 hover:underline ml-2"><i class="fas fa-eye"></i></a>
                                         <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="text-blue-600 hover:underline ml-2"><i class="fas fa-edit"></i></a>
                                            <a href="delete_expense.php?id=<?php echo $expense['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </div>
                         </div>
                     </div>
                <?php endforeach; ?>
            <?php else : ?>
                 <p class="text-gray-600 text-center">No expenses found.</p>
            <?php endif; ?>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>