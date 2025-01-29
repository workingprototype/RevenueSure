<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : null;

// Fetch task details
$stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE tasks.id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: view_tasks.php");
    exit();
}

// Fetch project details if a project is associated
if ($task['project_id']) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
    $stmt->bindParam(':project_id', $task['project_id']);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
}

  // Fetch task dependencies
$stmt = $conn->prepare("SELECT tasks.task_name FROM task_dependencies INNER JOIN tasks ON task_dependencies.depends_on_task_id = tasks.id WHERE task_dependencies.task_id = :task_id");
$stmt->bindParam(':task_id', $task_id);
$stmt->execute();
$dependencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Task Details</h1>
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
     <div>
         <p><strong>Task Name:</strong> <?php echo htmlspecialchars($task['task_name']); ?></p>
            <p><strong>Task Type:</strong> <?php echo htmlspecialchars($task['task_type']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
            <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($task['username']); ?></p>
       </div>
        <div>
             <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date']); ?></p>
            <p><strong>Estimated Hours:</strong> <?php echo htmlspecialchars($task['estimated_hours']); ?></p>
             <p><strong>Billable:</strong> <?php echo $task['billable'] ? 'Yes' : 'No'; ?></p>
            <p><strong>Status:</strong> <span class="px-2 py-1 rounded-full <?php
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
                            ?>"><?php echo htmlspecialchars($task['status']); ?></span></p>
            <p><strong>Priority:</strong> <?php echo htmlspecialchars($task['priority']); ?></p>
     </div>
    </div>
      <?php if ($project): ?>
           <div class="mt-6">
              <h2 class="text-xl font-bold text-gray-800 mb-4">Related Project</h2>
             <p><strong>Project Name:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
                  <p><strong>Project Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
             <?php if($project['end_date']): ?>
                    <p><strong>Project End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
                 <?php endif; ?>
              <p>
                    <a href="view_project.php?id=<?php echo $project['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">View Project</a>
                </p>
           </div>
        <?php endif; ?>
         <?php if (!empty($dependencies)): ?>
            <div class="mt-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Dependencies</h2>
                 <ul class="ml-6 list-disc">
                    <?php foreach ($dependencies as $dependency): ?>
                        <li><?php echo htmlspecialchars($dependency['task_name']) ?></li>
                    <?php endforeach; ?>
                 </ul>
             </div>
         <?php endif; ?>
         <div class="mt-4">
            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Edit Task</a>
              <?php
                if($lead_id){
                    echo ' <a href="view_tasks.php?lead_id='.$lead_id.'" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Tasks</a>';
                 }else if($project_id){
                   echo'   <a href="view_tasks.php?project_id='.$project_id.'" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Tasks</a>';
                }else {
                      echo ' <a href="view_tasks.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Tasks</a>';
                   }

              ?>
        </div>
</div>
<?php
// Include footer
require 'footer.php';
?>