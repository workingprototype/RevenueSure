<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : null;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
$user_id = $_SESSION['user_id'];

// Fetch tasks for the lead
if($lead_id){
    $stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE lead_id = :lead_id ORDER BY due_date ASC");
    $stmt->bindParam(':lead_id', $lead_id);
     $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else if ($project_id) {
      $stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE project_id = :project_id ORDER BY due_date ASC");
     $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
     // Fetch project details
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
}else {
      $stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE user_id = :user_id ORDER BY due_date ASC");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
<h1 class="text-4xl font-bold text-gray-900 mb-8">Tasks <?php if($lead_id) echo 'for Lead #' . $lead_id; else if ($project_id) echo 'for Project #' . $project_id; else echo "All" ?></h1>

 <?php if ($project && $project_id): ?>
     <div class="bg-white p-6 rounded-2xl shadow-xl mb-8 overflow-hidden border-l-4 border-blue-600 transition hover:shadow-2xl">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 relative">
         <i class="fas fa-project-diagram absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i>  Project Details
        </h2>
           <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Project Name:</span> <?php echo htmlspecialchars($project['name']); ?></p>
           <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Start Date:</span> <?php echo htmlspecialchars($project['start_date']); ?></p>
              <?php if($project['end_date']): ?>
                 <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">End Date:</span> <?php echo htmlspecialchars($project['end_date']); ?></p>
                <?php endif; ?>
           <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Project Manager:</span>
                <?php
                    $stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
                    $stmt->bindParam(':id', $project['project_manager_id']);
                     $stmt->execute();
                     $manager = $stmt->fetch(PDO::FETCH_ASSOC);
                      echo $manager ? htmlspecialchars($manager['username']) : 'N/A';
                ?>
           </p>
            <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Status:</span> <?php echo htmlspecialchars($project['status']); ?></p>
           <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Priority:</span> <?php echo htmlspecialchars($project['priority']); ?></p>
                <a href="view_project.php?id=<?php echo $project_id; ?>" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 inline-block mt-4">View Project</a>
    </div>
 <?php endif; ?>

<!-- Add Task Button -->
<div class="flex gap-4 mb-8 items-center">
<a href="add_task.php<?php if($lead_id) echo "?lead_id=$lead_id"; ?><?php if($project_id) echo "?project_id=$project_id"; ?>" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus mr-2"></i>Add Task</a>
    <?php if($project_id): ?>
     <a href="add_task.php?project_id=<?php echo $project_id; ?>" class="bg-green-700 text-white px-6 py-3 rounded-xl hover:bg-green-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Dependent Task</a>
     <a href="gantt_chart.php?project_id=<?php echo $project_id; ?>" class="bg-purple-700 text-white px-6 py-3 rounded-xl hover:bg-purple-900 transition duration-300 inline-block shadow-md"><i class="fas fa-chart-gantt mr-2"></i>View Gantt</a>
       <a href="kanban_board.php?project_id=<?php echo $project_id; ?>" class="bg-yellow-700 text-white px-6 py-3 rounded-xl hover:bg-yellow-900 transition duration-300 inline-block shadow-md"> <i class="fas fa-columns mr-2"></i>View Kanban Board</a>
   <?php endif; ?>
</div>
<!-- Tasks Table -->
  <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left ">
            <thead class="bg-gray-50">
                <tr>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Task Name</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Type</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Description</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Due Date</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Assigned To</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Priority</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tasks): ?>
                    <?php foreach ($tasks as $task): ?>
                         <tr class="border-b transition hover:bg-gray-100">
                                 <td class="px-4 py-3"> <?php if($project_id) : ?> <a href="view_task.php?id=<?php echo $task['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                           <?php elseif($lead_id): ?>
                                 <a href="view_task.php?id=<?php echo $task['id']; ?>&lead_id=<?php echo $lead_id; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                             <?php else :?>
                               <a href="view_task.php?id=<?php echo $task['id']; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                            <?php endif; ?>
                                </td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($task['task_type']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($task['description']); ?></td>
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
                            ?>">
                                   <?php echo htmlspecialchars($task['status']); ?>
                              </span>
                         </td>
                           <td class="px-4 py-3"><?php echo htmlspecialchars($task['priority']); ?></td>
                           <td class="px-4 py-3 flex gap-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i></a>
                                 <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i></a>
                                <a href="toggle_task_status.php?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline ml-2">
                                      <?php
                                        if ($task['status'] === 'To Do') {
                                               echo '<i class="fas fa-play"></i>';
                                         } else if ($task['status'] === 'In Progress') {
                                              echo '<i class="fas fa-check-double"></i>';
                                           }
                                            else {
                                                 echo '<i class="fas fa-history"></i>';
                                              }
                                    ?>
                                  </a>
                                   <a href="add_reminder.php?id=<?php echo $task['id']; ?>&due_date=<?php echo urlencode($task['due_date']); ?>" class="text-gray-600 hover:underline ml-2">
                                    <i class="fas fa-bell"></i>
                                </a>
                                 <?php if ($task['project_id']): ?>
                                    <a href="view_project.php?id=<?php echo $task['project_id']; ?>" class="text-purple-600 hover:underline"><i class="fas fa-project-diagram"></i></a>
                                  <?php endif; ?>
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
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>