<div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Statistics</h2>
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
             <div class="bg-white rounded-2xl shadow-xl p-6">
                 <canvas id="expensesByCategoryChart"></canvas>
             </div>
               <div class="bg-white rounded-2xl shadow-xl p-6">
                    <canvas id="expensesByPaymentModeChart"></canvas>
               </div>
             <div class="bg-white rounded-2xl shadow-xl p-6 col-span-2 md:col-span-2 lg:col-span-3">
                    <canvas id="expenseTrendsChart"></canvas>
             </div>
        </div>
    </div>
<script>
 document.addEventListener('DOMContentLoaded', function() {
    const expensesByCategoryCtx = document.getElementById('expensesByCategoryChart').getContext('2d');
    const expensesByPaymentModeCtx = document.getElementById('expensesByPaymentModeChart').getContext('2d');
     const expenseTrendsCtx = document.getElementById('expenseTrendsChart').getContext('2d');

        const expensesByCategory = <?php echo json_encode($expenses_by_category); ?>;
        const expenseBreakdownByMode = <?php echo json_encode($expense_breakdown_by_mode); ?>;
        const expenseTrends = <?php echo json_encode($processed_trends); ?>;

    // Pie Chart: Expenses by Category
    new Chart(expensesByCategoryCtx, {
         type: 'pie',
         data: {
            labels: Object.keys(expensesByCategory),
              datasets: [{
                    label: 'Expense by Category',
                   data: Object.values(expensesByCategory),
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
        // Pie Chart: Expenses by Payment Mode
        new Chart(expensesByPaymentModeCtx, {
            type: 'pie',
           data: {
             labels: Object.keys(expenseBreakdownByMode),
             datasets: [{
               label: 'Expenses by Payment Mode',
                data: Object.values(expenseBreakdownByMode),
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
        // Line Chart: Expense Trends
            const labels = Object.keys(expenseTrends);
              const datasets = [];
            const colors = [
                    'rgba(255, 99, 132, 0.8)',
                      'rgba(54, 162, 235, 0.8)',
                     'rgba(255, 206, 86, 0.8)',
                     'rgba(75, 192, 192, 0.8)',
                      'rgba(153, 102, 255, 0.8)',
                     'rgba(255, 159, 64, 0.8)'
               ];
            let colorIndex = 0;
            const categories =  [...new Set(Object.values(expenseTrends).flatMap(obj => Object.keys(obj)))]
                categories.forEach(category =>{
                    const data = [];
                       for (const date of labels){
                            const expense_count = expenseTrends[date][category] || 0;
                             data.push(expense_count);
                     }
                        const color = colors[colorIndex % colors.length];
                           datasets.push({
                            label: category,
                               data: data,
                              borderColor:  color.replace('0.8', '1'),
                                backgroundColor: color,
                                borderWidth: 1,
                                fill: false,
                                  tension: 0.3
                      });
                    colorIndex++;
              });
       new Chart(expenseTrendsCtx, {
            type: 'line',
               data: {
                 labels: labels,
                 datasets: datasets
                },
              options: {
                  responsive: true,
                 maintainAspectRatio: true,
                    scales: {
                      y: {
                         beginAtZero: true,
                        title: {
                          display: true,
                          text: 'Number of Expenses'
                           }
                        }
                     }
               }
           });
     });
</script>