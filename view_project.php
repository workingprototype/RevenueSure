<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch project details
$stmt = $conn->prepare("SELECT projects.*, project_categories.name as category_name, users.username as manager_name, leads.name as lead_name, customers.name as customer_name, customers.email as customer_email, customers.phone as customer_phone
                        FROM projects
                        LEFT JOIN project_categories ON projects.project_category_id = project_categories.id
                        LEFT JOIN users ON projects.project_manager_id = users.id 
                        LEFT JOIN leads ON projects.assigned_lead_customer_id = leads.id AND projects.assigned_lead_customer_type = 'lead'
                         LEFT JOIN customers ON projects.assigned_lead_customer_id = customers.id AND projects.assigned_lead_customer_type = 'customer'
                        WHERE projects.id = :id");
$stmt->bindParam(':id', $project_id);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: manage_projects.php");
    exit();
}

$lead_customer_name = '';
if ($project['assigned_lead_customer_type'] === 'lead') {
    $lead_customer_name = $project['lead_name'];
} elseif ($project['assigned_lead_customer_type'] === 'customer') {
    $lead_customer_name = $project['customer_name'];
}


// Fetch tasks for the project
$stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE project_id = :project_id ORDER BY due_date ASC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Project Details: <?php echo htmlspecialchars($project['name']); ?></h1>
    <?php if(isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
              Project updated successfully!
        </div>
     <?php endif; ?>

   <div class="bg-white p-6 rounded-lg shadow-md mb-8">
     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         <div>
              <p><strong>Project ID:</strong> <?php echo htmlspecialchars($project['project_id']); ?></p>
              <p><strong>Name:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
             <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($lead_customer_name); ?>
            <?php if($project['assigned_lead_customer_type'] === 'customer'): ?>
                  (<?php echo htmlspecialchars($project['customer_email']); ?> - <?php echo htmlspecialchars($project['customer_phone']); ?>)
              <?php endif; ?>
            </p>
             <p><strong>Project Manager:</strong> <?php echo htmlspecialchars($project['manager_name']); ?></p>
         </div>
         <div>
               <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
             <?php if($project['end_date']): ?>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
            <?php endif; ?>
               <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
               <p><strong>Priority:</strong> <?php echo htmlspecialchars($project['priority']); ?></p>
              <p><strong>Category:</strong> <?php echo htmlspecialchars($project['category_name'] ? $project['category_name'] : 'Uncategorized'); ?></p>
         </div>
      </div>
       <div class="mt-4">
         <p><strong>Billing Type:</strong> <?php echo htmlspecialchars($project['billing_type'] ? $project['billing_type'] : 'N/A'); ?></p>
          <?php if($project['budget']): ?>
               <p><strong>Budget:</strong> $<?php echo htmlspecialchars($project['budget']); ?></p>
            <?php endif; ?>
     </div>
          <?php if ($project['description']) : ?>
                <div class="mt-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Description</h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <?php echo $project['description']; ?>
                      </div>
                 </div>
            <?php endif; ?>
    </div>
     <div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Project Tasks</h2>
        <table class="w-full text-left">
             <thead>
            <tr>
                 <th class="px-4 py-2">Task Name</th>
                <th class="px-4 py-2">Task Type</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Due Date</th>
                 <th class="px-4 py-2">Assigned To</th>
                <th class="px-4 py-2">Status</th>
                 <th class="px-4 py-2">Priority</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tasks): ?>
                <?php foreach ($tasks as $task): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['task_name']); ?></td>
                         <td class="px-4 py-2"><?php echo htmlspecialchars($task['task_type']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['description']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($task['due_date']); ?></td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($task['username']); ?></td>
                        <td class="px-4 py-2">
                             <span class="px-2 py-1 rounded-full <?php
                                switch ($task['status']) {
                                    case 'To Do':
                                        echo 'bg-gray-200 text-gray-800';
                                            break;
                                       case 'In Progress':
                                            echo 'bg-blue-200 text-blue-800';
                                            break;
                                        case 'Completed':
                                            echo 'bg-green-200 text-green-800';
                                              break;
                                       case 'Blocked':
                                             echo 'bg-yellow-200 text-yellow-800';
                                                break;
                                        case 'Canceled':
                                            echo 'bg-red-200 text-red-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                             break;
                                    }
                            ?>">
                                   <?php echo htmlspecialchars($task['status']); ?>
                              </span>
                        </td>
                         <td class="px-4 py-2"><?php echo htmlspecialchars($task['priority']); ?></td>
                         <td class="px-4 py-2">
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                             <a href="toggle_task_status.php?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline ml-2">
                                 <?php
                                     if ($task['status'] === 'To Do') {
                                        echo 'Start Progress';
                                     } else if ($task['status'] === 'In Progress') {
                                        echo 'Mark Complete';
                                     }
                                     else {
                                        echo 'Mark To Do';
                                     }
                                ?>
                             </a>
                                <a href="add_reminder.php?id=<?php echo $task['id']; ?>&due_date=<?php echo urlencode($task['due_date']); ?>" class="text-gray-600 hover:underline ml-2">
                                   <i class="fas fa-bell"></i>
                             </a>
                         </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="px-4 py-2 text-center text-gray-600">No tasks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
   </div>   <div class="mt-4">
         <a href="kanban_board.php?project_id=<?php echo $project_id; ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 inline-block">View Kanban Board</a>
         <a href="gantt_chart.php?project_id=<?php echo $project_id; ?>" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300 inline-block">View Gantt Chart</a>
       <a href="manage_projects.php"  class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Projects</a>
   </div>
    <div class="mt-4 flex gap-2">
        <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Edit Project</a>
       <a href="manage_projects.php"  class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back To Projects</a>
    </div>
<?php
// Include footer
require 'footer.php';
?>