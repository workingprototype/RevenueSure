<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all parameters from URL to make the filtering reusable.
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_category = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';
$filter_priority = isset($_GET['filter_priority']) ? $_GET['filter_priority'] : '';
$filter_assigned_to = isset($_GET['filter_assigned_to']) ? $_GET['filter_assigned_to'] : '';
$filter_start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filter_end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build the base query for tickets with JOIN
$query = "SELECT support_tickets.*, users.username as assigned_username, users2.username as created_username
          FROM support_tickets
          LEFT JOIN users ON support_tickets.assigned_to = users.id
          LEFT JOIN users as users2 ON support_tickets.user_id = users2.id";

$where_conditions = [];
$params = [];

// Add filters to the query
if (!empty($filter_status)) {
     $where_conditions[] = "support_tickets.status = :filter_status";
     $params[':filter_status'] = $filter_status;

}
if (!empty($filter_category)) {
      $where_conditions[] = "support_tickets.category = :filter_category";
     $params[':filter_category'] = $filter_category;
}

if (!empty($filter_priority)) {
      $where_conditions[] = "support_tickets.priority = :filter_priority";
    $params[':filter_priority'] = $filter_priority;
}
if (!empty($filter_assigned_to)) {
    $where_conditions[] = "support_tickets.assigned_to = :filter_assigned_to";
     $params[':filter_assigned_to'] = $filter_assigned_to;
}

if (!empty($filter_start_date)) {
     $where_conditions[] = "support_tickets.created_at >= :start_date";
     $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
     $where_conditions[] = "support_tickets.created_at <= :end_date";
     $params[':end_date'] = $_GET['end_date'] . ' 23:59:59';
}

if(!empty($where_conditions)){
  $query .= " WHERE " . implode(" AND ", $where_conditions);
}
 $query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);


$view = isset($_GET['view']) ? $_GET['view'] : 'list';

// Calculate overview metrics
$total_tickets = count($tickets);
$tickets_by_status = [];
$tickets_by_priority = [];
$unassigned_tickets = 0;
$total_resolution_time = 0;
$resolved_tickets = 0;
$pending_tickets = 0;


if ($tickets) {
    foreach ($tickets as $ticket) {
        // Tickets by Status
        $status = $ticket['status'] ?: 'Unknown';
        $tickets_by_status[$status] = ($tickets_by_status[$status] ?? 0) + 1;

        // Tickets by Priority
        $priority = $ticket['priority'] ?: 'Unknown';
        $tickets_by_priority[$priority] = ($tickets_by_priority[$priority] ?? 0) + 1;

        // Unassigned Tickets
        if (empty($ticket['assigned_username'])) {
            $unassigned_tickets++;
        }
          // Pending tickets
          if($ticket['status'] !== 'Resolved' && $ticket['status'] !== 'Closed'){
              $pending_tickets++;
            }
           // Calculate Total Resolution Time (only for resolved tickets)
          if ($ticket['status'] === 'Resolved' && $ticket['created_at'] && $ticket['expected_resolution_date']) {
            $created_at = new DateTime($ticket['created_at']);
            $expected_resolution_date = new DateTime($ticket['expected_resolution_date']);
              $interval = $created_at->diff($expected_resolution_date);
             $total_resolution_time += $interval->days;
              $resolved_tickets++;
         }
    }
}
$average_resolution_time = $resolved_tickets > 0 ? round($total_resolution_time / $resolved_tickets, 2) : 0;


// Fetch all users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all project categories
$stmt = $conn->prepare("SELECT DISTINCT category FROM support_tickets ORDER BY category ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);


// Fetch pending tickets by user (Bar Chart)
$stmt = $conn->prepare("SELECT users.username, COUNT(support_tickets.id) as pending_count
                        FROM support_tickets
                        INNER JOIN users ON support_tickets.assigned_to = users.id
                        WHERE support_tickets.status != 'Resolved' AND support_tickets.status != 'Closed'
                          GROUP BY users.username ORDER BY pending_count DESC");
$stmt->execute();
$pending_tickets_by_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch pending tickets by category (Pie Chart)
$stmt = $conn->prepare("SELECT category, COUNT(id) as pending_count
                        FROM support_tickets
                        WHERE status != 'Resolved' AND status != 'Closed'
                        GROUP BY category");
$stmt->execute();
$pending_tickets_by_category = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch Ticket Trends (Line Chart)
$stmt = $conn->prepare("SELECT DATE(created_at) as date, category, COUNT(*) as ticket_count
                     FROM support_tickets 
                   GROUP BY DATE(created_at), category
                   ORDER BY date,category");

$stmt->execute();
$ticket_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process ticket trends data for chart js
$processed_trends = [];
foreach($ticket_trends as $trend){
    $date = $trend['date'];
    $category = $trend['category'] ? $trend['category'] : 'Uncategorized';
      if(!isset($processed_trends[$date])){
         $processed_trends[$date] = [];
         }
    $processed_trends[$date][$category] = $trend['ticket_count'];
}

// Include header
require 'header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Support Tickets</h1>

   <!-- Overview Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
             <!-- Total Tickets Card -->
            <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <h3 class="text-xl font-semibold">Total Tickets</h3>
                    <p class="text-3xl font-bold"><?php echo htmlspecialchars($total_tickets); ?></p>
               </div>
                <i class="fas fa-ticket-alt text-4xl opacity-70"></i>
             </div>
             <!-- Tickets by Status Card -->
            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
               <div class="flex flex-col gap-1">
                    <h3 class="text-xl font-semibold">Tickets By Status</h3>
                      <?php if ($tickets_by_status): ?>
                      <ul style="list-style: inside; padding-left: 0.8em;">
                        <?php foreach ($tickets_by_status as $status => $count): ?>
                            <li class="text-sm"><span class="font-medium"><?php echo htmlspecialchars($status); ?></span>: <span class="font-semibold"><?php echo htmlspecialchars($count); ?></span></li>
                         <?php endforeach; ?>
                         </ul>
                        <?php else: ?>
                        <p>No Tickets found.</p>
                       <?php endif; ?>
                 </div>
                  <i class="fas fa-list-ul text-4xl opacity-70"></i>
            </div>
              <!-- Tickets by Priority Card -->
              <div class="bg-gradient-to-r from-purple-400 to-purple-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                 <div class="flex flex-col gap-1">
                   <h3 class="text-xl font-semibold">Tickets By Priority</h3>
                        <?php if ($tickets_by_priority): ?>
                            <ul style="list-style: inside; padding-left: 0.8em;">
                                <?php foreach ($tickets_by_priority as $priority => $count): ?>
                                    <li class="text-sm"><span class="font-medium"><?php echo htmlspecialchars($priority); ?></span>: <span class="font-semibold"><?php echo htmlspecialchars($count); ?></span></li>
                                 <?php endforeach; ?>
                           </ul>
                       <?php else: ?>
                         <p>No Tickets found.</p>
                         <?php endif; ?>
                </div>
                   <i class="fas fa-exclamation-triangle text-4xl opacity-70"></i>
             </div>
            <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                 <div class="flex flex-col gap-1">
                     <h3 class="text-xl font-semibold">Unassigned Tickets</h3>
                      <p class="text-3xl font-bold"><?php echo htmlspecialchars($unassigned_tickets); ?></p>
                 </div>
                  <i class="fas fa-user-slash text-4xl opacity-70"></i>
             </div>
            <!-- Average Resolution Time Card -->
            <div class="bg-gradient-to-r from-orange-400 to-orange-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <h3 class="text-xl font-semibold">Average Resolution Time</h3>
                    <p class="text-2xl font-bold"><?php echo htmlspecialchars($average_resolution_time); ?> Days</p>
                </div>
               <i class="fas fa-hourglass-half text-4xl opacity-70"></i>
             </div>
            <!-- Pending Tickets Card -->
             <div class="bg-gradient-to-r from-red-400 to-red-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                 <div class="flex flex-col gap-1">
                        <h3 class="text-xl font-semibold">Pending Tickets</h3>
                        <p class="text-3xl font-bold"><?php echo htmlspecialchars($pending_tickets); ?></p>
                     </div>
                       <i class="fas fa-exclamation-circle text-4xl opacity-70"></i>
                </div>
                <div class="bg-gradient-to-r from-gray-400 to-gray-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                   <div class="flex flex-col gap-1">
                         <h3 class="text-xl font-semibold">Resolved Tickets</h3>
                           <p class="text-3xl font-bold"><?php echo htmlspecialchars($resolved_tickets); ?></p>
                      </div>
                        <i class="fas fa-check-double text-4xl opacity-70"></i>
              </div>

        </div>
      <!-- Add Ticket Button -->
    <div class="flex justify-between items-center mb-8">
         <a href="add_ticket.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
            <i class="fas fa-plus-circle mr-2"></i> Add Ticket
         </a>
         <div class="flex flex-wrap gap-2">
              <form method="GET" action="" class="flex gap-2 flex-wrap">
                  <select name="filter_status" id="filter_status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="">Select Status</option>
                       <option value="New" <?php if(isset($_GET['status']) && $_GET['status'] == 'New') echo 'selected'; ?>>New</option>
                      <option value="In Progress" <?php if(isset($_GET['status']) && $_GET['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                     <option value="Resolved" <?php if(isset($_GET['status']) && $_GET['status'] == 'Resolved') echo 'selected'; ?>>Resolved</option>
                     <option value="Closed" <?php if(isset($_GET['status']) && $_GET['status'] == 'Closed') echo 'selected'; ?>>Closed</option>
                  </select>
                   <select name="filter_category" id="filter_category" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                      <option value="">Select Category</option>
                    <?php if($categories) :
                          foreach ($categories as $category): ?>
                             <option value="<?php echo htmlspecialchars($category); ?>" <?php if(isset($_GET['filter_category']) && $_GET['filter_category'] == $category) echo 'selected'; ?>><?php echo htmlspecialchars($category); ?></option>
                         <?php endforeach; endif; ?>
                   </select>
                    <select name="filter_priority" id="filter_priority" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="">Select Priority</option>
                        <option value="Low" <?php if(isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'Low') echo 'selected'; ?>>Low</option>
                         <option value="Medium" <?php if(isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'Medium') echo 'selected'; ?>>Medium</option>
                         <option value="High" <?php if(isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'High') echo 'selected'; ?>>High</option>
                    </select>
                      <select name="filter_assigned_to" id="filter_assigned_to" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="">Select User</option>
                           <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php if(isset($_GET['filter_assigned_to']) && $_GET['filter_assigned_to'] == $user['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($user['username']); ?></option>
                           <?php endforeach; ?>
                   </select>
                   <input type="date" name="start_date" id="start_date" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    <input type="date" name="end_date" id="end_date" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">Filter</button>
                 </form>
               <a href="manage_tickets.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">Clear Filter</a>
         </div>
    </div>
    <div class="mb-8 border border-gray-400 bg-gray-100 rounded-lg p-6">
      <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
           Applied Filters
      </h2>
    <ul class="list-disc ml-6 text-sm mb-4">
        <?php if(!empty($filter_status)): ?>
            <li class="text-gray-700">Status: <?php echo htmlspecialchars($filter_status); ?></li>
        <?php endif; ?>
         <?php if(!empty($filter_category)): ?>
            <li class="text-gray-700">Category: <?php echo htmlspecialchars($filter_category); ?></li>
        <?php endif; ?>
          <?php if(!empty($filter_priority)): ?>
              <li class="text-gray-700">Priority: <?php echo htmlspecialchars($filter_priority); ?></li>
           <?php endif; ?>
         <?php if(!empty($filter_assigned_to)): ?>
            <li class="text-gray-700"> Assigned To:
             <?php
               $stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
               $stmt->bindParam(':id', $filter_assigned_to);
               $stmt->execute();
                $name = $stmt->fetch(PDO::FETCH_ASSOC);
                echo $name ? htmlspecialchars($name['username']) : 'N/A';
           ?> </li>
       <?php endif; ?>
         <?php if(!empty($filter_start_date)): ?>
            <li class="text-gray-700">Start Date: <?php echo htmlspecialchars($filter_start_date); ?></li>
           <?php endif; ?>
          <?php if(!empty($filter_end_date)): ?>
               <li class="text-gray-700">End Date: <?php echo htmlspecialchars($filter_end_date); ?></li>
          <?php endif; ?>
           <?php if(empty($filter_status) && empty($filter_category) && empty($filter_priority) && empty($filter_assigned_to)  && empty($filter_start_date) && empty($filter_end_date)): ?>
           <li class="text-gray-700"> No filters applied!</li>
        <?php endif; ?>
    </ul>
    </div>
        <div class="mb-8 border border-gray-400 bg-gray-100 rounded-lg p-6">
          <?php
            switch ($view) {
              case 'grid':
                 include 'view_tickets_grid.php';
                break;
              case 'card':
                  include 'view_tickets_card.php';
                 break;
                case 'kanban':
                         include 'view_tickets_kanban.php';
                        break;
                case 'calendar':
                         include 'view_tickets_calendar.php';
                         break;
             default:
                     include 'view_tickets_list.php';
                 break;
           }
         ?>
   </div>
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Statistics</h2>
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-xl p-6">
               <canvas id="pendingTicketsByUserChart"></canvas>
            </div>
             <div class="bg-white rounded-2xl shadow-xl p-6">
              <canvas id="pendingTicketsByCategoryChart"></canvas>
           </div>
        </div>
       <div class="bg-white p-6 rounded-lg shadow-md mt-8">
            <canvas id="ticketTrendsChart"></canvas>
       </div>
    </div>
</div>
<script>
 document.addEventListener('DOMContentLoaded', function() {
     const pendingTicketsByUserCtx = document.getElementById('pendingTicketsByUserChart').getContext('2d');
      const pendingTicketsByCategoryCtx = document.getElementById('pendingTicketsByCategoryChart').getContext('2d');
    const ticketTrendsCtx = document.getElementById('ticketTrendsChart').getContext('2d');
    const pendingTicketsByUser = <?php echo json_encode($pending_tickets_by_user); ?>;
     const pendingTicketsByCategory = <?php echo json_encode($pending_tickets_by_category); ?>;
    const ticketTrends = <?php echo json_encode($processed_trends); ?>;

     // Bar Chart: Tickets Pending by User
      new Chart(pendingTicketsByUserCtx, {
        type: 'bar',
          data: {
            labels: pendingTicketsByUser.map(item => item.username),
             datasets: [{
                   label: 'Pending Tickets',
                  data: pendingTicketsByUser.map(item => item.pending_count),
                 backgroundColor: 'rgba(54, 162, 235, 0.6)',
                 borderColor: 'rgba(54, 162, 235, 1)',
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
                             text: 'Number of Tickets'
                        }
                   }
               }
           }
    });
   // Pie Chart: Pending Tickets By Category
        new Chart(pendingTicketsByCategoryCtx, {
            type: 'pie',
           data: {
                labels: pendingTicketsByCategory.map(item => item.category ? item.category : "Uncategorized" ),
               datasets: [{
                 label: 'Pending Tickets by Category',
                    data: pendingTicketsByCategory.map(item => item.pending_count),
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
           maintainAspectRatio: true,
          }
        });


        // Line Chart: Ticket Trends
        const labels = Object.keys(ticketTrends);
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
         const categories =  [...new Set(Object.values(ticketTrends).flatMap(obj => Object.keys(obj)))]
        
          categories.forEach(category =>{
              const data = [];
                for (const date of labels){
                       const ticket_count = ticketTrends[date][category] || 0;
                         data.push(ticket_count);
                }
                  const color = colors[colorIndex % colors.length];
                   datasets.push({
                        label: category,
                           data: data,
                           borderColor:  color.replace('0.8', '1'),
                            backgroundColor: color,
                            borderWidth: 1,
                             fill: false,
                            tension: 0.3,
                    });
                   colorIndex++;
          });

        new Chart(ticketTrendsCtx, {
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
                           text: 'Number of Tickets'
                           }
                    }
              }
           }
      });
    });
</script>
<?php
// Include footer
require 'footer.php';
?>