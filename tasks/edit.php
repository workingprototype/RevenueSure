<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: " . BASE_URL . "tasks/viewtasks");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $task_name = $_POST['task_name'];
    $task_type = $_POST['task_type'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
     $estimated_hours = $_POST['estimated_hours'];
       $billable = isset($_POST['billable']) ? 1 : 0;
    $status = $_POST['status'];
     $priority = $_POST['priority'];
     $project_id = $task['project_id'];
     $lead_id = $task['lead_id'];
       $depends_on_task_ids = $_POST['depends_on_task'] ?? [];
    
    if($estimated_hours > 9999.99){
            $error = "Estimated hours must be less than 9999.99";
      }else {
              $stmt = $conn->prepare("UPDATE tasks SET task_name = :task_name, task_type = :task_type, description = :description, due_date = :due_date, estimated_hours = :estimated_hours, billable = :billable, status = :status, priority = :priority WHERE id = :id");
                $stmt->bindParam(':task_name', $task_name);
            $stmt->bindParam(':task_type', $task_type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':due_date', $due_date);
              $stmt->bindParam(':estimated_hours', $estimated_hours);
            $stmt->bindParam(':billable', $billable, PDO::PARAM_BOOL);
              $stmt->bindParam(':status', $status);
              $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':id', $task_id);

        if ($stmt->execute()) {
                  // delete existing dependencies.
                  $stmt = $conn->prepare("DELETE FROM task_dependencies WHERE task_id = :task_id");
                  $stmt->bindParam(':task_id', $task_id);
                  $stmt->execute();

                      foreach($depends_on_task_ids as $dependency_id){
                         $stmt = $conn->prepare("INSERT INTO task_dependencies (task_id, depends_on_task_id) VALUES (:task_id, :depends_on_task_id)");
                         $stmt->bindParam(':task_id', $task_id);
                        $stmt->bindParam(':depends_on_task_id', $dependency_id);
                        $stmt->execute();
                     }

                 if($lead_id){
                    header("Location: " . BASE_URL . "tasks/viewtasks?lead_id={$task['lead_id']}");
                      exit();
                  }else if($project_id){
                      header("Location: " . BASE_URL . "tasks/viewtasks?project_id={$task['project_id']}");
                    exit();
                  }
                header("Location: " . BASE_URL . "tasks/viewtasks");
                exit();
            } else {
                $error = "Error updating task.";
            }
            // Handle reminder
        if (!empty($_POST['reminder'])) {
            $reminder_time = $_POST['reminder'];
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, related_id, type, created_at) VALUES (:user_id, :message, :related_id, 'task_reminder', :created_at)");
            $message = "Reminder: Task '{$task['description']}' is due on {$task['due_date']}.";
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':related_id', $task_id);
            $stmt->bindParam(':created_at', $reminder_time);
            $stmt->execute();
        }
    }
}
//Fetch project tasks if project id is available
$tasks = [];
if($project_id){
    $stmt = $conn->prepare("SELECT id, task_name as name FROM tasks WHERE project_id = :project_id");
    $stmt->bindParam(':project_id', $project_id);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//Fetch task dependencies
$stmt = $conn->prepare("SELECT depends_on_task_id FROM task_dependencies WHERE task_id = :task_id");
$stmt->bindParam(':task_id', $task_id);
$stmt->execute();
$dependencies = $stmt->fetchAll(PDO::FETCH_COLUMN);


?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Task</h1>
   <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
<!-- Edit Task Form -->
<form method="POST" action="" class="bg-white p-6 rounded-2xl shadow-xl">
<?php echo csrfTokenInput(); ?>
     <div class="mb-4">
        <label for="task_name" class="block text-gray-700">Task Name</label>
        <input type="text" name="task_name" id="task_name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>
    </div>
    <div class="mb-4">
        <label for="task_type" class="block text-gray-700">Task Type</label>
        <select name="task_type" id="task_type" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
            <option value="Follow-Up" <?php echo $task['task_type'] === 'Follow-Up' ? 'selected' : ''; ?>>Follow-Up</option>
            <option value="Meeting" <?php echo $task['task_type'] === 'Meeting' ? 'selected' : ''; ?>>Meeting</option>
            <option value="Deadline" <?php echo $task['task_type'] === 'Deadline' ? 'selected' : ''; ?>>Deadline</option>
        </select>
    </div>

    <div class="mb-4">
        <label for="description" class="block text-gray-700">Description</label>
        <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($task['description']); ?></textarea>
    </div>

   <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label for="due_date" class="block text-gray-700">Due Date</label>
           <input type="datetime-local" name="due_date" id="due_date" value="<?php echo date('Y-m-d\TH:i', strtotime($task['due_date'])); ?>" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
        </div>
        <div>
           <label for="estimated_hours" class="block text-gray-700">Estimated Hours</label>
              <input type="number" name="estimated_hours" id="estimated_hours" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($task['estimated_hours']); ?>" min="0"  step="0.01" max="9999.99">
        </div>
   </div>
    <div class="mb-4">
         <label class="inline-flex items-center">
             <input type="checkbox" name="billable" id="billable" class="mr-2" <?php if ($task['billable'] == 1) echo 'checked'; ?>>
               <span class="text-gray-700">Billable</span>
          </label>
     </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
              <label for="status" class="block text-gray-700">Status</label>
                  <select name="status" id="status" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                      <option value="To Do" <?php if ($task['status'] === 'To Do') echo 'selected'; ?>>To Do</option>
                      <option value="In Progress" <?php if ($task['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                      <option value="Completed" <?php if ($task['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
                         <option value="Blocked" <?php if ($task['status'] === 'Blocked') echo 'selected'; ?>>Blocked</option>
                        <option value="Canceled" <?php if ($task['status'] === 'Canceled') echo 'selected'; ?>>Canceled</option>
                  </select>
            </div>
             <div>
               <label for="priority" class="block text-gray-700">Priority</label>
                 <select name="priority" id="priority" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                       <option value="Low" <?php if ($task['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                       <option value="Medium" <?php if ($task['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                       <option value="High" <?php if ($task['priority'] === 'High') echo 'selected'; ?>>High</option>
                 </select>
          </div>
      </div>
       <?php if($project_id): ?>
          <div class="mb-4">
              <label for="depends_on_task" class="block text-gray-700">Depends On Task(s)</label>
               <select name="depends_on_task[]" id="depends_on_task" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" multiple>
                      <?php if($tasks): ?>
                       <?php foreach ($tasks as $related_task): ?>
                                <option value="<?php echo $related_task['id']; ?>" <?php if(in_array($related_task['id'], $dependencies)) echo 'selected'; ?>><?php echo htmlspecialchars($related_task['name']); ?></option>
                           <?php endforeach; ?>
                       <?php endif; ?>
                 </select>
           </div>
        <?php endif; ?>
   <div class="mb-4">
    <label for="reminder" class="block text-gray-700">Set Reminder</label>
       <input type="datetime-local" name="reminder" id="reminder" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
   </div>
    <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Update Task</button>
</form>
 <script>
    const estimatedHoursInput = document.getElementById('estimated_hours');
       estimatedHoursInput.addEventListener('input', function() {
           if (parseFloat(this.value) > 9999.99){
                this.value = '9999.99';
            }
        });
 </script>
