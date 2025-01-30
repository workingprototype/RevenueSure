<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;
if (!$project_id) {
  header("Location: manage_projects.php");
    exit();
}
// Fetch project details
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
     header("Location: manage_projects.php");
    exit();
}
// Fetch tasks for the project
$stmt = $conn->prepare("SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.user_id = users.id WHERE project_id = :project_id ORDER BY due_date ASC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
  <h1 class="text-4xl font-bold text-gray-900 mb-6">Kanban Board: <?php echo htmlspecialchars($project['name']); ?></h1>
    <div class="bg-white p-6 rounded-2xl shadow-xl mb-8 overflow-hidden border-l-4 border-blue-500 transition hover:shadow-2xl">
        <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
           <i class="fas fa-project-diagram absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Project Details
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
    </div>

    <div class="flex overflow-x-auto gap-4 p-4">
        <?php
            $statuses = ['To Do', 'In Progress', 'Completed', 'Blocked', 'Canceled'];
            foreach ($statuses as $status):
                $filtered_tasks = array_filter($tasks, function ($task) use ($status) {
                 return $task['status'] === $status;
              });
            ?>
            <div class="kanban-column w-72 min-w-72 bg-gray-100 rounded-2xl p-4 shadow-xl border-l-4" data-status="<?php echo $status; ?>"
             style="border-left-color:
               <?php
                switch ($status) {
                    case 'To Do':
                        echo '#94a3b8';
                         break;
                   case 'In Progress':
                     echo '#007aff';
                        break;
                   case 'Completed':
                     echo '#22c55e';
                       break;
                     case 'Blocked':
                            echo '#facc15';
                            break;
                       case 'Canceled':
                              echo '#ef4444';
                            break;
                    default:
                       echo '#94a3b8';
                          break;
                    }
                 ?>;">
                <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                  <?php echo $status; ?>
                   <span class="absolute top-[3px] left-[-22px] text-sm">
                  <?php
                    switch ($status) {
                         case 'To Do':
                              echo '<i class="fas fa-list-check text-gray-500"></i>';
                             break;
                           case 'In Progress':
                              echo '<i class="fas fa-spinner text-blue-500"></i>';
                              break;
                          case 'Completed':
                            echo '<i class="fas fa-check-circle text-green-500"></i>';
                              break;
                           case 'Blocked':
                                 echo '<i class="fas fa-ban text-yellow-500"></i>';
                             break;
                        case 'Canceled':
                           echo '<i class="fas fa-times-circle text-red-500"></i>';
                              break;
                           default:
                              echo '<i class="fas fa-tasks text-gray-500"></i>';
                                 break;
                        }
                    ?>
                </span>
                </h2>
                  <ul class="kanban-items space-y-4">
                    <?php if($filtered_tasks) :
                      foreach ($filtered_tasks as $task):
                        ?>
                        <li
                            class="kanban-item bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between  transition hover:shadow-2xl"
                            data-task-id="<?php echo $task['id']; ?>" draggable="true"
                             style="border-left: 4px solid <?php
                                  switch ($task['priority']) {
                                     case 'High':
                                         echo 'red';
                                          break;
                                       case 'Medium':
                                         echo 'yellow';
                                           break;
                                       case 'Low':
                                         echo 'green';
                                            break;
                                    default:
                                     echo 'gray';
                                       break;
                                }
                             ?>;"
                        >
                           <div>
                             <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($task['task_name']); ?></p>
                               <p class="text-gray-600 text-sm mt-1">
                                    <strong>Due:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($task['due_date']))); ?>
                                 </p>
                           </div>
                            <div class="flex justify-end gap-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i></a>
                                    <a href="view_tasks.php?project_id=<?php echo $project_id ?>&task_id=<?php echo $task['id'] ?>" class="text-purple-600 hover:underline"><i class="fas fa-eye"></i></a>
                                </div>
                        </li>
                    <?php  endforeach; else: ?>
                         <p class="text-gray-600 text-center">No task in this category</p>
                    <?php endif; ?>
                  </ul>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-4">
        <a href="view_project.php?id=<?php echo $project_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Project</a>
  </div>
<script>
 document.addEventListener('DOMContentLoaded', function() {
    const kanbanColumns = document.querySelectorAll('.kanban-column');
     const kanbanItems = document.querySelectorAll('.kanban-item');

        kanbanItems.forEach(item => {
            item.addEventListener('dragstart', dragStart);
        });

    kanbanColumns.forEach(column => {
        column.addEventListener('dragover', dragOver);
        column.addEventListener('dragenter', dragEnter);
        column.addEventListener('dragleave', dragLeave);
        column.addEventListener('drop', dragDrop);
    });

      let draggedItem = null;

    function dragStart(event) {
        draggedItem = event.target;
        event.dataTransfer.effectAllowed = 'move';
         event.dataTransfer.setData('text/html', this.innerHTML); // required for firefox
       setTimeout(() => {
            this.classList.add('invisible'); // to hide
        }, 0);
       
    }
    function dragOver(event) {
        event.preventDefault();
    }
        function dragEnter(event) {
            event.preventDefault();
            this.classList.add('bg-gray-300');
         }
           function dragLeave(event) {
                this.classList.remove('bg-gray-300');
            }

     function dragDrop(event) {
         event.preventDefault();
         this.classList.remove('bg-gray-300');
         if(draggedItem){
               const taskId = draggedItem.dataset.taskId;
              const newStatus = this.dataset.status;

            fetch('update_task_status.php', {
                method: 'POST',
                headers: {
                   'Content-Type': 'application/x-www-form-urlencoded',
                  },
                body: `task_id=${taskId}&status=${newStatus}`
                 })
                     .then(response => {
                        if(!response.ok){
                             throw new Error('Network response was not ok');
                           }
                         return response.text()
                     })
                    .then(data => {
                        this.querySelector('.kanban-items').appendChild(draggedItem);
                      draggedItem.classList.remove('invisible');
                       draggedItem = null
                        })
                     .catch(error => {
                         console.error('Error updating task status', error);
                       draggedItem.classList.remove('invisible');
                        draggedItem = null
                     });
        }
    }
});
</script>
<?php
// Include footer
require 'footer.php';
?>