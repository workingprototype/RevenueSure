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

// Fetch task dependencies
$stmt = $conn->prepare("SELECT * FROM task_dependencies");
$stmt->execute();
$dependencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">


<h1 class="text-3xl font-bold text-gray-800 mb-6">Gantt Chart: <?php echo htmlspecialchars($project['name']); ?></h1>
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
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div id="gantt" style="width:100%; height: 500px;"></div>
    </div>
    <div class="mt-4">
         <a href="view_project.php?id=<?php echo $project_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Project</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tasks = <?php echo json_encode($tasks); ?>;
            const dependencies = <?php echo json_encode($dependencies); ?>;
            const ganttTasks = tasks.map(task => ({
                id: task.id,
                name: task.task_name,
                 start:  new Date(task.due_date).toISOString().slice(0, 10),
                   end: new Date(task.due_date).toISOString().slice(0, 10),
                progress: task.status === 'Completed' ? 100 : (task.status === 'In Progress' ? 50 : 0),
                dependencies: dependencies
                    .filter(dep => dep.task_id === task.id)
                    .map(dep => dep.depends_on_task_id)
            }));

            new Gantt("#gantt", ganttTasks, {
                on_click: function (task) {
                    window.location.href = 'view_tasks.php?project_id=<?php echo $project_id; ?>&task_id='+task.id;
                   console.log(task);
                 },
                  on_date_change: (task, start, end) => {
                    console.log(task, start, end);
                     // You can use this event to save the updated dates to the DB if needed
                    },
                    on_progress_change: (task, progress) => {
                          console.log(task, progress);
                     },
                header_height: 50,
                column_width: 30,
                step: 24,
                view_mode: 'Day',
                 bar_height: 25,
                 bar_corner_radius: 4,
                 padding: 18,
                  on_view_change: (mode) => {
                   console.log(mode)
                    },
            });
         });
    </script>
<?php
// Include footer
require 'footer.php';
?>