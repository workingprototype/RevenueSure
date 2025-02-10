<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

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
    header("Location: " . BASE_URL . "projects/manage");
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


?>
<div class="container mx-auto p-6 fade-in">
<h1 class="text-4xl font-bold text-gray-900 mb-8">Project Details: <?php echo htmlspecialchars($project['name']); ?></h1>
    <?php if(isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
              Project updated successfully!
        </div>
    <?php endif; ?>
   <div class="bg-white p-6 rounded-2xl shadow-xl mb-8 overflow-hidden border-l-4 border-blue-600 transition hover:shadow-2xl">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 relative">
         <i class="fas fa-project-diagram absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Project Information
        </h2>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         <div>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Project ID:</span> <?php echo htmlspecialchars($project['project_id']); ?></p>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Name:</span> <?php echo htmlspecialchars($project['name']); ?></p>
             <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Assigned To:</span> <?php echo htmlspecialchars($lead_customer_name); ?>
            <?php if($project['assigned_lead_customer_type'] === 'customer'): ?>
                  (<span class="text-gray-800"><?php echo htmlspecialchars($project['customer_email']); ?> - <?php echo htmlspecialchars($project['customer_phone']); ?>)</span>
              <?php endif; ?>
            </p>
             <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Project Manager:</span> <?php echo htmlspecialchars($project['manager_name']); ?></p>
         </div>
         <div>
               <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Start Date:</span> <?php echo htmlspecialchars($project['start_date']); ?></p>
             <?php if($project['end_date']): ?>
                <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">End Date:</span> <?php echo htmlspecialchars($project['end_date']); ?></p>
            <?php endif; ?>
               <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Status:</span> <?php echo htmlspecialchars($project['status']); ?></p>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Priority:</span> <?php echo htmlspecialchars($project['priority']); ?></p>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Category:</span> <?php echo htmlspecialchars($project['category_name'] ? $project['category_name'] : 'Uncategorized'); ?></p>
         </div>
      </div>
       <div class="mt-4 flex flex-wrap gap-4">
         <p class="text-gray-700 text-md mb-2">
                <span class="font-semibold text-gray-800">Billing Type:</span> <?php echo htmlspecialchars($project['billing_type'] ? $project['billing_type'] : 'N/A'); ?>
           </p>
          <?php if($project['budget']): ?>
                <p class="text-gray-700 text-md mb-2"><span class="font-semibold text-gray-800">Budget:</span> $<?php echo htmlspecialchars($project['budget']); ?></p>
            <?php endif; ?>
     </div>
          <?php if ($project['description']) : ?>
                <div class="mt-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                              <i class="fas fa-file-alt absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Description
                         </h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <?php echo $project['description']; ?>
                      </div>
                 </div>
            <?php endif; ?>
    </div>
     <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
         <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
           <i class="fas fa-list-check absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Project Tasks
       </h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50">
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Task Name</th>
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Due Date</th>
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Assigned To</th>
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($tasks): ?>
                         <?php foreach ($tasks as $task): ?>
                            <tr class="border-b transition hover:bg-gray-100">
                                  <td class="px-4 py-3">
                                   <a href="<?php echo BASE_URL; ?>tasks/viewtask?id=<?php echo $task['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-gray-800 hover:underline"><?php echo htmlspecialchars($task['task_name']); ?></a>
                                 </td>
                                 <td class="px-4 py-3"><?php echo htmlspecialchars($task['due_date']); ?></td>
                                 <td class="px-4 py-3"><?php echo htmlspecialchars($task['username']); ?></td>
                                  <td class="px-4 py-3">
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
                            ?>"><?php echo htmlspecialchars($task['status']); ?></span>
                               </td>
                                <td class="px-4 py-3 flex gap-2">
                                        <a href="<?php echo BASE_URL; ?>tasks/edit?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i></a>
                                         <a href="<?php echo BASE_URL; ?>tasks/delete?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i></a>
                                           <a href="<?php echo BASE_URL; ?>tasks/toggle_status?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline ml-2">
                                          <?php
                                                if ($task['status'] === 'To Do') {
                                                     echo '<i class="fas fa-play"></i>';
                                                }else if ($task['status'] === 'In Progress') {
                                                     echo '<i class="fas fa-check-double"></i>';
                                                }
                                                 else {
                                                      echo '<i class="fas fa-history"></i>';
                                                  }
                                           ?>
                                     </a>
                                    <a href="<?php echo BASE_URL; ?>reminders/add?id=<?php echo $task['id']; ?>&due_date=<?php echo urlencode($task['due_date']); ?>" class="text-gray-600 hover:underline ml-2">
                                        <i class="fas fa-bell"></i>
                                     </a>
                             </td>
                            </tr>
                        <?php endforeach; ?>
                     <?php else: ?>
                          <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-600">No tasks found.</td>
                           </tr>
                    <?php endif; ?>
                </tbody>
           </table>
    </div>
    <div class="mt-4">
         <a href="<?php echo BASE_URL; ?>projects/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Projects</a>
    </div>
</div>
