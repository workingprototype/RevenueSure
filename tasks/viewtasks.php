<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

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
$view = isset($_GET['view']) ? $_GET['view'] : 'list';

?>
<div class="container mx-auto p-6 fade-in">
<h1 class="text-4xl font-bold text-gray-900 mb-8">Tasks <?php if($lead_id) echo 'for Lead #' . $lead_id; else if ($project_id) echo 'for Project #' . $project_id; else echo "All" ?></h1>
 <?php if ($project_id):  //Only call and use if project ID:?>
    <div class="bg-white p-6 rounded-2xl shadow-xl mb-8 overflow-hidden border-l-4 border-blue-600 transition hover:shadow-2xl">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 relative">
         <i class="fas fa-project-diagram absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i>  Project Details
        </h2>
           <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Project Name:</span> <?php echo htmlspecialchars($project['name'] ?? " "); ?></p>
           <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Start Date:</span> <?php echo htmlspecialchars($project['start_date'] ?? " "); ?></p>
              <?php if(isset($project['end_date'])): ?>
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
        
            <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project_id; ?>" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 inline-block mt-4">View Project</a>
            <a href="<?php echo BASE_URL; ?>projects/gantt_chart?project_id=<?php echo $project_id; ?>" class="bg-purple-700 text-white px-4 py-2 rounded-lg hover:bg-purple-900 transition duration-300 inline-block">View Gantt Chart</a>
             <a href="<?php echo BASE_URL; ?>projects/kanban_board?project_id=<?php echo $project_id; ?>" class="bg-yellow-700 text-white px-4 py-2 rounded-lg hover:bg-yellow-900 transition duration-300 inline-block">View Kanban Board</a>
           </div>
 <?php endif; ?>

<!-- Add Task Button -->
<div class="flex gap-4 mb-8 items-center">
<a href="<?php echo BASE_URL; ?>tasks/add?<?php if($lead_id) echo "lead_id=$lead_id"; ?><?php if($project_id) echo "&project_id=$project_id"; ?>" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus mr-2"></i>Add Task</a>
    <?php if($project_id): ?>
     <a href="<?php echo BASE_URL; ?>tasks/add?project_id=<?php echo $project_id; ?>" class="bg-green-700 text-white px-6 py-3 rounded-xl hover:bg-green-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Dependent Task</a>
        
     <?php endif; ?>
         <div class="flex gap-2">
                <a href="<?php echo BASE_URL; ?>tasks/viewtasks?view=list<?php if($lead_id) echo '&lead_id='.$lead_id; ?><?php if($project_id) echo '&project_id='.$project_id; ?>" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'list') echo "bg-gray-200"; ?>"><i class="fas fa-list"></i> List</a>
                  <a href="<?php echo BASE_URL; ?>tasks/viewtasks?view=grid<?php if($lead_id) echo '&lead_id='.$lead_id; ?><?php if($project_id) echo '&project_id='.$project_id; ?>" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'grid') echo "bg-gray-200"; ?>"><i class="fas fa-th-large"></i> Grid</a>
                  <a href="<?php echo BASE_URL; ?>tasks/viewtasks?view=card<?php if($lead_id) echo '&lead_id='.$lead_id; ?><?php if($project_id) echo '&project_id='.$project_id; ?>" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'card') echo "bg-gray-200"; ?>"><i class="fas fa-credit-card"></i> Cards</a>
                 <?php if ($project_id): ?>
                       <!-- <a href="<?php echo BASE_URL; ?>?view=kanban&project_id=<?php echo $project_id; ?>" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'kanban') echo "bg-gray-200"; ?>"><i class="fas fa-columns"></i> Kanban</a> -->
                       <a href="<?php echo BASE_URL; ?>tasks/viewtasks?view=calendar&project_id=<?php echo $project_id; ?>" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'calendar') echo "bg-gray-200"; ?>"><i class="fas fa-calendar-alt"></i> Calendar</a>
                 <?php endif; ?>
            </div>
</div>
<div class="mb-8">
<?php
    switch ($view) {
      case 'grid':
         include ROOT_PATH. 'tasks/grid.php';
        break;
      case 'card':
            include ROOT_PATH .'tasks/card.php';
         break;
       case 'kanban':
                include ROOT_PATH .'projects/kanban_board.php';
                break;
        case 'calendar':
                include ROOT_PATH .'tasks/calendar.php';
                break;
       default:
                include ROOT_PATH .'tasks/list.php';
          break;
    }
 ?>
</div>
<script>
   document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
        if(calendarEl){
             const tasks = <?php echo json_encode($tasks); ?>;
              const calendar = new FullCalendar.Calendar(calendarEl, {
                  initialView: 'dayGridMonth',
                    events: tasks.map(task => ({
                     title: task.task_name,
                     start: task.due_date,
                       end:  task.due_date,
                           url:  `tasks/view?id=${task.id}<?php if($project_id) echo "&project_id=" . $project_id ; ?><?php if($lead_id) echo "&lead_id=" . $lead_id ; ?>`,
                          backgroundColor:  task.priority === 'High' ? '#ef4444' : ( task.priority === 'Medium' ? '#facc15' : '#22c55e'),
                        borderColor:  task.priority === 'High' ? '#ef4444' : ( task.priority === 'Medium' ? '#facc15' : '#22c55e')
                    })) ,
              });
                calendar.render();
            }
    });
</script>