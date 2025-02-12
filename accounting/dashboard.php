<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$current_year = date('Y');

try {
    // --- Income and Expense Metrics ---

    // 1. Total Income
    $stmt = $conn->prepare("SELECT SUM(total) AS total_income FROM invoices WHERE status = 'Paid'");
    $stmt->execute();
    $total_income = $stmt->fetch(PDO::FETCH_ASSOC)['total_income'] ?: 0;

    // 2. Total Expenses
    $stmt = $conn->prepare("SELECT SUM(amount) AS total_expenses FROM expenses");
    $stmt->execute();
    $total_expenses = $stmt->fetch(PDO::FETCH_ASSOC)['total_expenses'] ?: 0;

    // 3. Net Profit/Loss
    $net_profit_loss = $total_income - $total_expenses;

    // --- Trend Data (Monthly for the current year) ---
    $stmt = $conn->prepare("
        SELECT
            MONTH(issue_date) as month,
            SUM(total) as monthly_income
        FROM invoices
        WHERE YEAR(issue_date) = :year AND status = 'Paid'
        GROUP BY MONTH(issue_date)
        ORDER BY MONTH(issue_date)
    ");
    $stmt->bindParam(':year', $current_year, PDO::PARAM_INT);
    $stmt->execute();
    $monthly_income_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        SELECT
            MONTH(expense_date) as month,
            SUM(amount) as monthly_expenses
        FROM expenses
        WHERE YEAR(expense_date) = :year
        GROUP BY MONTH(expense_date)
        ORDER BY MONTH(expense_date)
    ");
    $stmt->bindParam(':year', $current_year, PDO::PARAM_INT);
    $stmt->execute();
    $monthly_expense_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Top 5 Expense Categories ---
    $stmt = $conn->prepare("
        SELECT ec.name as category_name, SUM(e.amount) AS total_spent
        FROM expenses e
        JOIN expense_categories ec ON e.category_id = ec.id
        GROUP BY ec.name
        ORDER BY total_spent DESC
        LIMIT 5");
    $stmt->execute();
    $top_expense_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Currency Breakdown  ----
    $stmt = $conn->prepare("
    SELECT currency, SUM(amount) AS total_amount FROM (
        SELECT billing_country as currency, total as amount FROM invoices WHERE status = 'Paid'
        UNION ALL
        SELECT 'USD' as currency, amount FROM expenses  -- expenses assumed to be in usd .
    ) AS combined_data
    GROUP BY currency");
    $stmt->execute();
    $currency_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // 6. Recent Ledger Entries (From Invoices and Expenses)
    $stmt = $conn->prepare("
      SELECT transaction_date, description, debit_amount, credit_amount, currency, category, reconciliation_status FROM (
            SELECT issue_date as transaction_date, concat('Invoice #', invoice_number, ' Payment') as description, 0 as debit_amount, total as credit_amount, billing_country as currency, 'Revenue' as category, 'Unreconciled' as reconciliation_status FROM invoices  WHERE status = 'Paid'
            UNION ALL
              SELECT expense_date as transaction_date, name as description, amount as debit_amount, 0 as credit_amount, 'USD' as currency, transaction_nature as category, 'Unreconciled' as reconciliation_status FROM expenses
           
        ) AS combined_ledger
        ORDER BY transaction_date DESC LIMIT 5");
    $stmt->execute();
    $recent_ledger_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 7. Monthly Expense Trend
$stmt = $conn->prepare("SELECT DATE_FORMAT(expense_date, '%Y-%m') AS month, SUM(amount) AS total_expenses FROM expenses GROUP BY month ORDER BY month");
$stmt->execute();
$monthly_expense_trend = $stmt->fetchAll(PDO::FETCH_ASSOC);
 //8. Top 5 Customers by Revenue
    $stmt = $conn->prepare("SELECT c.name, SUM(i.total) as total_revenue
                            FROM invoices i
                            JOIN customers c ON i.customer_id = c.id
                            WHERE i.status = 'Paid'
                           GROUP BY c.name ORDER BY total_revenue DESC LIMIT 5");
   $stmt->execute();
   $top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //9. Expense Categories Breakdown
    $stmt = $conn->prepare("SELECT ec.name AS category_name, SUM(e.amount) AS total_amount
                            FROM expenses e
                            JOIN expense_categories ec ON e.category_id = ec.id
                            GROUP BY ec.name ORDER BY total_amount DESC");
   $stmt->execute();
    $expense_category_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //10. Budget Variance (Example: if you have a budget for a specific period)
    $budget = 100000; // Total monthly budget (hardcoded for example) Todo: Need to build the ability to add budget later.
     $budgetVariance = $budget - $total_expenses;

    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Database error. Please try again later.";
}

?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-8">Accounting Dashboard</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700">Total Income</h2>
            <p class="text-2xl font-bold text-green-600">$<?php echo htmlspecialchars(number_format($total_income, 2)); ?></p>
            <p class="text-gray-600 mt-2">Sum of all invoice amounts (paid invoices).</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700">Total Expenses</h2>
            <p class="text-2xl font-bold text-red-600">$<?php echo htmlspecialchars(number_format($total_expenses, 2)); ?></p>
            <p class="text-gray-600 mt-2">Sum of all expenses.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700">Net Profit/Loss</h2>
            <p class="text-2xl font-bold <?php echo ($net_profit_loss >= 0) ? 'text-green-600' : 'text-red-600'; ?>">$<?php echo htmlspecialchars(number_format($net_profit_loss, 2)); ?></p>
            <p class="text-gray-600 mt-2">Total Income - Total Expenses.</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
             <h2 class="text-lg font-semibold text-gray-700">Budget Variance</h2>
               <p class="text-xl font-bold text-blue-600"><?php echo htmlspecialchars(number_format($budgetVariance, 2)); ?></p>
            <p class="text-gray-600 mt-2">Budget  - Total Expenses.</p>
      </div>
    </div>

    <!-- Additional Dashboard Charts and Data -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Monthly Income vs Expenses Chart -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Monthly Income vs Expenses (<?php echo htmlspecialchars($current_year); ?>)</h2>
             <canvas id="monthlyChart"></canvas>
          </div>
          <!-- Top 5 Expense Categories -->
          <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Top 5 Expense Categories</h2>
             <?php if ($top_expense_categories): ?>
                <ul class="list-decimal list-inside">
                    <?php foreach ($top_expense_categories as $category): ?>
                        <li>
                            <?php echo htmlspecialchars($category['category_name']); ?>:
                            <span class="font-bold">$<?php echo htmlspecialchars(number_format($category['total_spent'], 2)); ?></span>
                        </li>
                    <?php endforeach; ?>
                 </ul>
                <?php else: ?>
                    <p>No expense data found.</p>
                <?php endif; ?>
        </div>
        <!-- Currency Breakdown Chart -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Currency Breakdown</h2>
            <?php if ($currency_breakdown): ?>
                <canvas id="currencyChart"></canvas>
            <?php else: ?>
                <p>No currency data to display.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Ledger Entries -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Ledger Entries</h2>
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Debit</th>
                        <th class="px-4 py-2">Credit</th>
                        <th class="px-4 py-2">Currency</th>
                        <th class="px-4 py-2">Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_ledger_entries): ?>
                        <?php foreach ($recent_ledger_entries as $entry): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['transaction_date']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['description']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['debit_amount']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['credit_amount']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['currency']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($entry['category']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center">No ledger entries found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
         <!-- Left or top for Currency Chart (Implement chart.js) -->
         <div class="bg-white p-4 rounded-lg shadow">
             <h2 class="text-lg font-semibold text-gray-700 mb-4">Top 5 Customers by Revenue</h2>
                     <table class="table-auto w-full">
                <thead>
                   <tr>
                       <th class="px-4 py-2">Customer Name</th>
                         <th class="px-4 py-2">Total Revenue</th>

                        </tr>
                 </thead>
                <tbody>
                      <?php if($top_customers): ?>
                         <?php foreach($top_customers as $customer) : ?>
                               <tr>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($customer['name']); ?></td>
                                     <td class="border px-4 py-2"><?php echo htmlspecialchars($customer['total_revenue']); ?></td>
                               </tr>
                             <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-600">No ledger entries found.</td>
                          </tr>
                       <?php endif; ?>
                  </tbody>
              </table>
         </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const monthlyChartCtx = document.getElementById('monthlyChart').getContext('2d');
    const currencyChartCtx = document.getElementById('currencyChart').getContext('2d');

        const monthlyIncomeData = <?php echo json_encode($monthly_income_data); ?>;
        const monthlyExpenseData = <?php echo json_encode($monthly_expense_data); ?>;
        const currencyData = <?php echo json_encode($currency_breakdown); ?>;
  
 const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
       const monthlyIncomeValues = new Array(12).fill(0);
       monthlyIncomeData.forEach(item => {
         monthlyIncomeValues[item.month - 1] = item.monthly_income;
       });

      const monthlyExpenseValues = new Array(12).fill(0);
       monthlyExpenseData.forEach(item => {
          monthlyExpenseValues[item.month - 1] = item.monthly_expenses;
      });
    new Chart(monthlyChartCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                        label: 'Income',
                        data: monthlyIncomeValues,
                         backgroundColor: 'rgba(75, 192, 192, 0.6)',
                         borderColor: 'rgba(75, 192, 192, 1)',
                         borderWidth: 1
                     },
                    {
                        label: 'Expenses',
                           data: monthlyExpenseValues,
                         backgroundColor: 'rgba(255, 99, 132, 0.6)',
                         borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount ($)'
                             }
                         },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                         }
                   }
                }
            });
        const labels = currencyData.map(item => item.currency);
        const data = currencyData.map(item => item.total_amount);
          const chart = new Chart(currencyChartCtx, {
                type: 'pie',
                data: {
                      labels: labels,
                      datasets: [{
                            label: 'Currency Breakdown',
                             data: data,
                                backgroundColor: [
                                  'rgba(255, 99, 132, 0.8)',
                                    'rgba(54, 162, 235, 0.8)',
                                 'rgba(255, 206, 86, 0.8)',
                                   'rgba(75, 192, 192, 0.8)',
                                 'rgba(153, 102, 255, 0.8)',
                                 'rgba(255, 159, 64, 0.8)'
                                  ],
                                   borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                     'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                      'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                    ],
                                   borderWidth: 1
                            }]
                      },
               options: {
                  responsive: true,
                      maintainAspectRatio: true
                }
            });
   });
</script>
<?php
 /*
  -Chart,js chart data for the Monthly Chart.
- Display the top 5 expense categories.
 */
?>