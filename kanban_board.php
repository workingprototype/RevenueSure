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
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Kanban Board: <?php echo htmlspecialchars($project['name']); ?></h1>
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Project Details</h2>
         <p><strong>Project Name:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
           <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
              <?php if($project['end_date']): ?>
                 <p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
                <?php endif; ?>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
           <p><strong>Priority:</strong> <?php echo htmlspecialchars($project['priority']); ?></p>
    </div>

    <div class="flex overflow-x-auto gap-4 p-4">
        <?php
            $statuses = ['To Do', 'In Progress', 'Completed', 'Blocked', 'Canceled'];
            foreach ($statuses as $status):
                $filtered_tasks = array_filter($tasks, function ($task) use ($status) {
                 return $task['status'] === $status;
              });
            ?>
            <div class="kanban-column w-72 min-w-72 bg-gray-100 rounded-lg p-4 shadow-md" data-status="<?php echo $status; ?>">
                <h2 class="text-xl font-bold text-gray-800 mb-4"><?php echo $status; ?></h2>
                  <ul class="kanban-items space-y-4">
                    <?php if($filtered_tasks) :
                      foreach ($filtered_tasks as $task):
                        ?>
                        <li
                            class="kanban-item bg-white p-4 rounded-lg shadow-sm flex flex-col justify-between"
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
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                    <a href="view_tasks.php?project_id=<?php echo $project_id ?>&task_id=<?php echo $task['id'] ?>" class="text-purple-600 hover:underline">View</a>
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