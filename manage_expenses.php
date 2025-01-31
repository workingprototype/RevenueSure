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


// Fetch Expense Trends (Line Chart)
$stmt = $conn->prepare("SELECT DATE(expense_date) as date, category_id, COUNT(*) as expense_count FROM expenses GROUP BY DATE(expense_date), category_id ORDER BY date, category_id");
$stmt->execute();
$expense_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process expense trend data for chart js
$processed_trends = [];
foreach ($expense_trends as $trend) {
    $date = $trend['date'];
     $category = $trend['category_id'];
        if(!isset($processed_trends[$date])){
           $processed_trends[$date] = [];
        }
       $processed_trends[$date][$category] = $trend['expense_count'];
    }
// Include header
require 'header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Expenses</h1>
          <!-- Filter and Add Button -->
         <div class="flex flex-wrap justify-between items-center mb-8">
             <div class="flex flex-wrap gap-2">
                 <?php include 'expenses_filters.php'; ?>
             </div> </br>
             <a href="add_expense.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
                  <i class="fas fa-plus-circle mr-2"></i> Record Expense
               </a>
          </div>
          <?php
     $hasFilters = !empty($_GET['search']) || !empty($_GET['category_id']) || !empty($_GET['user_id']) || !empty($_GET['project_id']) || !empty($_GET['invoice_id']) || !empty($_GET['payment_mode']) || !empty($_GET['transaction_nature']) || !empty($_GET['start_date']) || !empty($_GET['end_date']);
      if ($hasFilters):
       ?>
        <div class="mb-8 border border-gray-400 bg-gray-100 rounded-lg p-6">
             <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
             <div class="relative flex items-center">
    <i class="fas fa-filter text-gray-500 text-sm mr-2"></i>
    <span>Applied Filters</span>
</div>
                 </h2>
                <ul class="list-disc ml-6 text-sm mb-4">
                    <?php if(!empty($_GET['search'])): ?>
                        <li class="text-gray-700">Search : <?php echo htmlspecialchars($_GET['search']); ?></li>
                    <?php endif; ?>
                    <?php if(!empty($_GET['category_id'])): ?>
                        <li class="text-gray-700">Category:
                                <?php
                                    $stmt = $conn->prepare("SELECT name FROM expense_categories WHERE id = :id");
                                    $stmt->bindParam(':id', $_GET['category_id']);
                                    $stmt->execute();
                                    $category_name = $stmt->fetch(PDO::FETCH_ASSOC);
                                      echo $category_name ? htmlspecialchars($category_name['name']) : 'N/A';
                                  ?>
                           </li>
                 <?php endif; ?>
                     <?php if(!empty($_GET['user_id'])): ?>
                         <li class="text-gray-700"> User:
                           <?php
                               $stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
                             $stmt->bindParam(':id', $_GET['user_id']);
                             $stmt->execute();
                              $user_name = $stmt->fetch(PDO::FETCH_ASSOC);
                              echo $user_name ? htmlspecialchars($user_name['username']) : 'N/A';
                          ?>
                     </li>
                <?php endif; ?>
                 <?php if(!empty($_GET['project_id'])): ?>
                    <li class="text-gray-700"> Project:
                      <?php
                       $stmt = $conn->prepare("SELECT name FROM projects WHERE id = :id");
                        $stmt->bindParam(':id', $_GET['project_id']);
                         $stmt->execute();
                          $project_name = $stmt->fetch(PDO::FETCH_ASSOC);
                           echo $project_name ? htmlspecialchars($project_name['name']) : 'N/A';
                       ?>
                   </li>
               <?php endif; ?>
                <?php if(!empty($_GET['invoice_id'])): ?>
                  <li class="text-gray-700"> Invoice Number:
                     <?php
                        $stmt = $conn->prepare("SELECT invoice_number FROM invoices WHERE id = :id");
                        $stmt->bindParam(':id', $_GET['invoice_id']);
                         $stmt->execute();
                        $invoice_number = $stmt->fetch(PDO::FETCH_ASSOC);
                         echo $invoice_number ? htmlspecialchars($invoice_number['invoice_number']) : 'N/A';
                       ?>
                </li>
            <?php endif; ?>
              <?php if(!empty($_GET['payment_mode'])): ?>
                  <li class="text-gray-700">Payment Mode: <?php echo htmlspecialchars($_GET['payment_mode']); ?></li>
             <?php endif; ?>
            <?php if(!empty($_GET['transaction_nature'])): ?>
                 <li class="text-gray-700">Nature: <?php echo htmlspecialchars($_GET['transaction_nature']); ?></li>
            <?php endif; ?>
            <?php if(!empty($_GET['start_date'])): ?>
                 <li class="text-gray-700">Start Date: <?php echo htmlspecialchars($_GET['start_date']); ?></li>
           <?php endif; ?>
            <?php if(!empty($_GET['end_date'])): ?>
                <li class="text-gray-700">End Date: <?php echo htmlspecialchars($_GET['end_date']); ?></li>
           <?php endif; ?>
         </ul>
         </div>
        <?php endif; ?>
           <?php
              //Passing $expenses, $categories, $users, $projects and $invoices to all other included files
              include 'expenses_metrics.php';
         ?>
         <div class="mt-8">
           <?php include 'expenses_table.php'; ?>
        </div>
        
        <div class="mt-8">
            <?php include 'expenses_charts.php'; ?>
        </div>
         
    </div>
<?php
// Include footer
require 'footer.php';
?>