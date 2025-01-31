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
if (isset($_GET['search']) && !empty($_GET['search'])) {
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

// Calculate overview metrics
$total_expenses = 0;
$expenses_by_category = [];
$monthly_expenses = 0;
$pending_reimbursements = 0;
$top_spending_entity = null;
$expense_breakdown_by_mode = [];

if ($expenses) {
    foreach ($expenses as $expense) {
        // Total Expenses
        $total_expenses += $expense['amount'];

        // Expenses by Category
        $category = $expense['category_name'] ?: 'Uncategorized';
        $expenses_by_category[$category] = ($expenses_by_category[$category] ?? 0) + $expense['amount'];

        // Monthly Expenses (only if in the current month)
        if (date('Y-m', strtotime($expense['expense_date'])) === date('Y-m')) {
            $monthly_expenses += $expense['amount'];
        }
        // Pending Reimbursements
       if($expense['transaction_nature'] === 'Reimbursable' && $expense['receipt_path'] === null){
            $pending_reimbursements += $expense['amount'];
       }
         // Expense breakdown by payment mode
          $mode = $expense['payment_mode'] ?: 'Unknown';
           $expense_breakdown_by_mode[$mode] = ($expense_breakdown_by_mode[$mode] ?? 0) + $expense['amount'];
        }
            // Find top spending employee or project
        if(empty($_GET['project_id']) && empty($_GET['user_id'])){
            $stmt = $conn->prepare("SELECT user_id, SUM(amount) AS total_spent FROM expenses GROUP BY user_id ORDER BY total_spent DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
          if($result){
                $top_spending_entity = [
                  'type' => "employee",
                  'id' => $result['user_id'],
                  'amount' => $result['total_spent']
              ];
              $stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
              $stmt->bindParam(':user_id', $top_spending_entity['id']);
               $stmt->execute();
                $top_spending_entity['name'] = $stmt->fetch(PDO::FETCH_ASSOC)['username'] ;
          }else {
             $stmt = $conn->prepare("SELECT project_id, SUM(amount) AS total_spent FROM expenses GROUP BY project_id ORDER BY total_spent DESC LIMIT 1");
              $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if($result){
                      $top_spending_entity = [
                        'type' => "project",
                        'id' => $result['project_id'],
                         'amount' => $result['total_spent']
                     ];
                       $stmt = $conn->prepare("SELECT name FROM projects WHERE id = :project_id");
                      $stmt->bindParam(':project_id', $top_spending_entity['id']);
                        $stmt->execute();
                        $top_spending_entity['name'] = $stmt->fetch(PDO::FETCH_ASSOC)['name'];
                   }
             }
         } else {
            if(!empty($_GET['user_id'])){
                  $stmt = $conn->prepare("SELECT user_id, SUM(amount) AS total_spent FROM expenses WHERE user_id = :user_id GROUP BY user_id ORDER BY total_spent DESC LIMIT 1");
                  $stmt->bindParam(':user_id', $_GET['user_id']);
                 $stmt->execute();
                  $result = $stmt->fetch(PDO::FETCH_ASSOC);
                  if($result){
                    $top_spending_entity = [
                      'type' => "employee",
                      'id' => $result['user_id'],
                       'amount' => $result['total_spent']
                     ];
                     $stmt = $conn->prepare("SELECT username FROM users WHERE id = :user_id");
                    $stmt->bindParam(':user_id', $top_spending_entity['id']);
                    $stmt->execute();
                     $top_spending_entity['name'] = $stmt->fetch(PDO::FETCH_ASSOC)['username'];
                  }
            }elseif(!empty($_GET['project_id'])){
                 $stmt = $conn->prepare("SELECT project_id, SUM(amount) AS total_spent FROM expenses WHERE project_id = :project_id GROUP BY project_id ORDER BY total_spent DESC LIMIT 1");
                $stmt->bindParam(':project_id', $_GET['project_id']);
                  $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                      if($result){
                         $top_spending_entity = [
                           'type' => "project",
                           'id' => $result['project_id'],
                           'amount' => $result['total_spent']
                          ];
                          $stmt = $conn->prepare("SELECT name FROM projects WHERE id = :project_id");
                            $stmt->bindParam(':project_id', $top_spending_entity['id']);
                            $stmt->execute();
                           $top_spending_entity['name'] = $stmt->fetch(PDO::FETCH_ASSOC)['name'];
                       }
            }
         }
}
// Include header
require 'header.php';
?>
    <div class="container mx-auto p-6 fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Expenses</h1>

         <!-- Filter and Add Button -->
         <div class="flex flex-wrap justify-between items-center mb-8">
             <div class="flex flex-wrap gap-2">
                 <form method="GET" action="" class="flex gap-2 flex-wrap">
                    <input type="text" name="search" id="search" placeholder="Search" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 flex-1" value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
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
                  </div> <br>
             <a href="add_expense.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
                  <i class="fas fa-plus-circle mr-2"></i> Record Expense
             </a>
         </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Expenses Card -->
            <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                 <div class="flex flex-col gap-1">
                      <h3 class="text-xl font-semibold">Total Expenses</h3>
                      <p class="text-3xl font-bold">$<?php echo htmlspecialchars(number_format($total_expenses, 2)); ?></p>
                  </div>
                  <i class="fas fa-dollar-sign text-4xl opacity-70"></i>
            </div>

              <!-- Expense by Category Card -->
            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                <div class="flex flex-col gap-1">
                     <h3 class="text-xl font-semibold">Expenses by Category</h3>
                       <?php if($expenses_by_category) : ?>
                         <ul style="list-style: inside; padding-left: 0.8em;">
                             <?php foreach ($expenses_by_category as $category => $amount): ?>
                                 <li class="text-sm">
                                      <span class="font-medium"><?php echo htmlspecialchars($category); ?></span>: <span class="font-semibold">$<?php echo htmlspecialchars(number_format($amount, 2)); ?></span>
                                   </li>
                               <?php endforeach; ?>
                           </ul>
                      <?php else: ?>
                      <p> No expenses yet!</p>
                      <?php endif; ?>
                </div>
                <i class="fas fa-list-alt text-4xl opacity-70"></i>
             </div>

            <!-- Monthly Expenses Card -->
            <div class="bg-gradient-to-r from-purple-400 to-purple-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
              <div class="flex flex-col gap-1">
                    <h3 class="text-xl font-semibold">Monthly Expenses</h3>
                  <p class="text-3xl font-bold">$<?php echo htmlspecialchars(number_format($monthly_expenses, 2)); ?></p>
             </div>
              <i class="fas fa-calendar-alt text-4xl opacity-70"></i>
            </div>

              <!-- Pending Reimbursements Card -->
              <div class="bg-gradient-to-r from-red-400 to-red-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                    <div class="flex flex-col gap-1">
                       <h3 class="text-xl font-semibold">Pending Reimbursements</h3>
                        <p class="text-3xl font-bold">$<?php echo htmlspecialchars(number_format($pending_reimbursements, 2)); ?></p>
                    </div>
                    <i class="fas fa-file-invoice-dollar text-4xl opacity-70"></i>
              </div>
            <!-- Top Spending Entity Card -->
          <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
              <div class="flex flex-col gap-1">
                  <h3 class="text-xl font-semibold">Top Spender</h3>
                  <?php if ($top_spending_entity): ?>
                       <p class="text-2xl font-bold">
                            <?php echo htmlspecialchars($top_spending_entity['name']); ?>
                       </p>
                       <p class="text-sm">
                              <span class="opacity-80">Type:</span> <?php echo htmlspecialchars(ucfirst($top_spending_entity['type'])); ?> , <span class="opacity-80">Amount:</span> $<?php echo htmlspecialchars(number_format($top_spending_entity['amount'],2)); ?>
                            </p>
                   <?php else: ?>
                       <p class="text-gray-200 opacity-70">N/A</p>
                  <?php endif; ?>
                </div>
                <i class="fas fa-trophy text-4xl opacity-70"></i>
            </div>
              <!-- Expense Breakdown By Payment Card -->
            <div class="bg-gradient-to-r from-gray-400 to-gray-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
               <div class="flex flex-col gap-1">
                   <h3 class="text-xl font-semibold">By Payment Mode</h3>
                      <?php if ($expense_breakdown_by_mode) : ?>
                          <ul style="list-style: inside; padding-left: 0.8em;">
                            <?php foreach ($expense_breakdown_by_mode as $mode => $amount): ?>
                                <li class="text-sm"><span class="font-medium"><?php echo htmlspecialchars($mode); ?></span>: <span class="font-semibold">$<?php echo htmlspecialchars(number_format($amount, 2)); ?></span></li>
                               <?php endforeach; ?>
                          </ul>
                      <?php else : ?>
                          <p class="text-gray-200 opacity-70">N/A</p>
                      <?php endif; ?>
               </div>
              <i class="fas fa-credit-card text-4xl opacity-70"></i>
           </div>
    </div>

    <!-- Expenses Table -->
      <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Name</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Category</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Date</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">User</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Payment Mode</th>
                       <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Nature</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Amount</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                 <?php if ($expenses): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr class="border-b transition hover:bg-gray-100">
                             <td class="px-4 py-3"><?php echo htmlspecialchars($expense['name']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($expense['category_name'] ? $expense['category_name'] : 'Uncategorized'); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($expense['user_name']); ?></td>
                           <td class="px-4 py-3"> <span class="px-2 py-1 rounded-full <?php
                                    switch ($expense['payment_mode']) {
                                      case 'Cash':
                                            echo 'bg-green-100 text-green-800';
                                               break;
                                        case 'Credit Card':
                                            echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'Bank Transfer':
                                           echo 'bg-purple-100 text-purple-800';
                                              break;
                                        case 'Online Payment':
                                            echo 'bg-blue-100 text-blue-800';
                                              break;
                                      case 'Check':
                                            echo 'bg-gray-100 text-gray-800';
                                                break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                               break;
                                    }
                            ?>"><?php echo htmlspecialchars($expense['payment_mode']); ?></span></td>
                              <td class="px-4 py-3"><span class="px-2 py-1 rounded-full <?php
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
                             ?>"><?php echo htmlspecialchars($expense['transaction_nature']); ?></span></td>
                            <td class="px-4 py-3">$<?php echo htmlspecialchars($expense['amount']); ?></td>
                            <td class="px-4 py-3 flex gap-2">
                                 <a href="view_expense.php?id=<?php echo $expense['id']; ?>" class="text-purple-600 hover:underline"> <i class="fas fa-eye"></i></a>
                                <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="text-blue-600 hover:underline">  <i class="fas fa-edit"></i></a>
                                <a href="delete_expense.php?id=<?php echo $expense['id']; ?>" class="text-red-600 hover:underline ml-2"> <i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                         <td colspan="7" class="px-4 py-2 text-center text-gray-600">No expenses found.</td>
                    </tr>
                 <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>