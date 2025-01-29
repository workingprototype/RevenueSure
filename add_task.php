<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : null;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
function generateTaskId($conn) {
    $stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM tasks");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_id = ($result['max_id'] ?? 0) + 1;
    return 'TASK-' . date('Ymd') . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = $_POST['task_name'];
    $task_id = generateTaskId($conn);
     $task_type = $_POST['task_type'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
      $estimated_hours = $_POST['estimated_hours'];
      $billable = isset($_POST['billable']) ? 1 : 0;
      $status = $_POST['status'];
       $priority = $_POST['priority'];
       $user_id = $_SESSION['user_id'];
       $selected_project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : null;
       $depends_on_task_ids = $_POST['depends_on_task'] ?? [];


    if($selected_project_id){
          $stmt = $conn->prepare("INSERT INTO tasks (task_id, project_id, user_id, task_name, task_type, description, due_date, estimated_hours, billable, status, priority) VALUES (:task_id, :project_id, :user_id, :task_name, :task_type, :description, :due_date, :estimated_hours, :billable, :status, :priority)");
           $stmt->bindParam(':project_id', $selected_project_id);
    }else if($lead_id) {
          $stmt = $conn->prepare("INSERT INTO tasks (task_id, lead_id, user_id, task_name, task_type, description, due_date, estimated_hours, billable, status, priority) VALUES (:task_id, :lead_id, :user_id, :task_name, :task_type, :description, :due_date, :estimated_hours, :billable, :status, :priority)");
          $stmt->bindParam(':lead_id', $lead_id);
    }else {
         $stmt = $conn->prepare("INSERT INTO tasks (task_id, user_id, task_name, task_type, description, due_date, estimated_hours, billable, status, priority) VALUES (:task_id, :user_id, :task_name, :task_type, :description, :due_date, :estimated_hours, :billable, :status, :priority)");
    }
    $stmt->bindParam(':task_id', $task_id);
     $stmt->bindParam(':user_id', $user_id);
     $stmt->bindParam(':task_name', $task_name);
    $stmt->bindParam(':task_type', $task_type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':due_date', $due_date);
      $stmt->bindParam(':estimated_hours', $estimated_hours);
    $stmt->bindParam(':billable', $billable, PDO::PARAM_BOOL);
     $stmt->bindParam(':status', $status);
       $stmt->bindParam(':priority', $priority);

    if ($stmt->execute()) {
         $task_insert_id = $conn->lastInsertId();
           // Handle task dependencies
            foreach($depends_on_task_ids as $dependency_id){
                $stmt = $conn->prepare("INSERT INTO task_dependencies (task_id, depends_on_task_id) VALUES (:task_id, :depends_on_task_id)");
                 $stmt->bindParam(':task_id', $task_insert_id);
                $stmt->bindParam(':depends_on_task_id', $dependency_id);
                $stmt->execute();
            }
      if($lead_id){
            header("Location: view_tasks.php?lead_id=$lead_id");
      } else if($selected_project_id){
            header("Location: view_tasks.php?project_id=$selected_project_id");
      } else {
           header("Location: view_tasks.php");
       }
         exit();
    } else {
        echo "<script>alert('Error adding task.');</script>";
    }
     // Handle reminder
    if (!empty($_POST['reminder'])) {
        $reminder_time = $_POST['reminder'];
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, related_id, type, created_at) VALUES (:user_id, :message, :related_id, 'task_reminder', :created_at)");
        $message = "Reminder: Task '{$task_name}' is due on {$due_date}.";
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':related_id', $task_id);
        $stmt->bindParam(':created_at', $reminder_time);
        $stmt->execute();
    }
}

// Fetch projects for the dropdown
$stmt = $conn->prepare("SELECT id, name FROM projects");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tasks = [];
if($project_id){
    $stmt = $conn->prepare("SELECT id, task_name as name FROM tasks WHERE project_id = :project_id");
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Add Task</h1>
  <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
<!-- Add Task Form -->
<form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md">
    <?php if($lead_id): ?>
        <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
    <?php endif; ?>
       <?php if($project_id): ?>
      <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
  <?php endif; ?>
       <div class="mb-4">
        <label for="task_name" class="block text-gray-700">Task Name</label>
        <input type="text" name="task_name" id="task_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
    </div>
    <div class="mb-4">
        <label for="task_type" class="block text-gray-700">Task Type</label>
        <select name="task_type" id="task_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            <option value="Follow-Up">Follow-Up</option>
            <option value="Meeting">Meeting</option>
            <option value="Deadline">Deadline</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="description" class="block text-gray-700">Description</label>
        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required></textarea>
    </div>

    <div class="mb-4">
        <label for="due_date" class="block text-gray-700">Due Date</label>
        <input type="datetime-local" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
    </div>
     <div class="mb-4">
         <label for="estimated_hours" class="block text-gray-700">Estimated Hours</label>
           <input type="number" name="estimated_hours" id="estimated_hours" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" min="0"  step="0.01" >
     </div>
    <div class="mb-4">
         <label class="inline-flex items-center">
             <input type="checkbox" name="billable" id="billable" class="mr-2">
               <span class="text-gray-700">Billable</span>
            </label>
     </div>
       <div class="mb-4">
        <label for="status" class="block text-gray-700">Status</label>
        <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
           <option value="To Do">To Do</option>
             <option value="In Progress">In Progress</option>
               <option value="Completed">Completed</option>
                 <option value="Blocked">Blocked</option>
                   <option value="Canceled">Canceled</option>
          </select>
    </div>
      <div class="mb-4">
        <label for="priority" class="block text-gray-700">Priority</label>
            <select name="priority" id="priority" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                   <option value="Low">Low</option>
                   <option value="Medium">Medium</option>
                    <option value="High">High</option>
            </select>
    </div>
       <?php if(!$lead_id && !$project_id) : ?>
         <div class="mb-4">
           <label for="project_id" class="block text-gray-700">Related To Project</label>
                <select name="project_id" id="project_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">No Project</option>
                    <?php foreach ($projects as $project): ?>
                       <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                    <?php endforeach; ?>
                </select>
          </div>
        <?php endif; ?>
       <?php if($project_id): ?>
           <div class="mb-4">
                <label for="depends_on_task" class="block text-gray-700">Depends On Task(s)</label>
                <select name="depends_on_task[]" id="depends_on_task" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" multiple>
                    <?php if($tasks): ?>
                        <?php foreach ($tasks as $task): ?>
                           <option value="<?php echo $task['id']; ?>"><?php echo htmlspecialchars($task['name']); ?></option>
                        <?php endforeach; ?>
                   <?php endif; ?>
                </select>
          </div>
    <?php endif; ?>
    <div class="mb-4">
    <label for="reminder" class="block text-gray-700">Set Reminder</label>
    <input type="datetime-local" name="reminder" id="reminder" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
</div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Task</button>
</form>
 <script>
    const estimatedHoursInput = document.getElementById('estimated_hours');
       estimatedHoursInput.addEventListener('input', function() {
          if (parseFloat(this.value) > 9999.99){
                this.value = '9999.99';
             }
        });
 </script>
<?php
// Include footer
require 'footer.php';
?>