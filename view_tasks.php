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

<h1 class="text-3xl font-bold text-gray-800 mb-6">Tasks <?php if($lead_id) echo 'for Lead #' . $lead_id; else if ($project_id) echo 'for Project #' . $project_id; else echo "All" ?></h1>

 <?php if ($project && $project_id): ?>
      <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Project Details</h2>
           <p><strong>Project Name:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
           <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
              <?php if($project['end_date']): ?>
                 <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
                <?php endif; ?>
           <p><strong>Project Manager:</strong>
                <?php
                    $stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
                    $stmt->bindParam(':id', $project['project_manager_id']);
                     $stmt->execute();
                     $manager = $stmt->fetch(PDO::FETCH_ASSOC);
                      echo $manager ? htmlspecialchars($manager['username']) : 'N/A';
                ?>
           </p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
           <p><strong>Priority:</strong> <?php echo htmlspecialchars($project['priority']); ?></p>
    </div>
 <?php endif; ?>

<!-- Add Task Button -->
<a href="add_task.php<?php if($lead_id) echo "?lead_id=$lead_id"; ?><?php if($project_id) echo "?project_id=$project_id"; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Task</a>

<!-- Tasks Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
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
                                     }else if ($task['status'] === 'In Progress') {
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
                    <td colspan="7" class="px-4 py-2 text-center text-gray-600">No tasks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require 'footer.php';
?>