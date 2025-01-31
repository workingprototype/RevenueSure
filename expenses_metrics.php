<?php
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
?>
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
                                 <li class="text-sm"><span class="font-medium"><?php echo htmlspecialchars($category); ?></span>: <span class="font-semibold">$<?php echo htmlspecialchars(number_format($amount, 2)); ?></span></li>
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