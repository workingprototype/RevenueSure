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

// Fetch features for the project
$stmt = $conn->prepare("SELECT * FROM project_features WHERE project_id = :project_id ORDER BY created_date DESC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$features = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch issues for the project
$stmt = $conn->prepare("SELECT project_issues.*, users.username AS reported_by_name FROM project_issues JOIN users ON project_issues.reported_by = users.id WHERE project_id = :project_id ORDER BY date_reported DESC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are any related notes
$stmt = $conn->prepare("SELECT COUNT(*) FROM notes WHERE related_type = 'project' AND related_id = :project_id");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$note_count = $stmt->fetchColumn();

// Fetch notes for the project (always fetch)
$stmt = $conn->prepare("SELECT * FROM notes WHERE related_type = 'project' AND related_id = :project_id");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['project_id'])) {
    $project_id = (int)$_POST['project_id'];

    // Update status if provided
    if (isset($_POST['new_status'])) {
        // Status update code here...
    }
    // Update priority if provided
    if (isset($_POST['new_priority'])) {
        $new_priority = $_POST['new_priority'];
        $allowed_priorities = ['Low', 'Medium', 'High'];
        if (in_array($new_priority, $allowed_priorities)) {
            try {
                $stmt = $conn->prepare("UPDATE projects SET priority = :priority WHERE id = :project_id");
                $stmt->bindParam(':priority', $new_priority);
                $stmt->bindParam(':project_id', $project_id);
                if ($stmt->execute()) {
                    $success = "Project priority updated successfully!";
                    header("Location: " . BASE_URL . "projects/view?id=$project_id&success=true");
                    exit();
                } else {
                    $error = "Error updating project priority.";
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                $error = "Database error. Please try again later.";
            }
        } else {
            $error = "Invalid project priority.";
        }
    }
}

// Calculate percentage completed for tasks
$total_tasks = count($tasks);
$completed_tasks = 0;
foreach ($tasks as $task) {
    if ($task['status'] === 'Completed') {
        $completed_tasks++;
    }
}
$percentage_completed = ($total_tasks > 0) ? round(($completed_tasks / $total_tasks) * 100, 2) : 0;
?>
<div class="container mx-auto p-6 fade-in">
  <h1 class="text-4xl font-bold text-gray-900 mb-4">Project Details: <?php echo htmlspecialchars($project['name']); ?></h1>
  
  <?php if(isset($_GET['success']) && $_GET['success'] == 'true'): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
      Project updated successfully!
    </div>
  <?php endif; ?>

  <!-- Project Stats Section with badges and progress bar -->
  <div class="mb-6">
      <div class="flex flex-wrap items-center gap-4">
          <!-- Project Status Badge -->
          <span class="px-3 py-1 rounded-full text-sm font-medium <?php 
              switch ($project['status']) {
                  case 'In Progress': echo 'bg-blue-100 text-blue-800'; break;
                  case 'Completed': echo 'bg-green-100 text-green-800'; break;
                  case 'On Hold': echo 'bg-yellow-100 text-yellow-800'; break;
                  case 'Canceled': echo 'bg-red-100 text-red-800'; break;
                  default: echo 'bg-gray-100 text-gray-800'; break;
              }
          ?>">
              <?php echo htmlspecialchars($project['status']); ?>
          </span>
          
          <!-- Project Priority Badge -->
          <span class="px-3 py-1 rounded-full text-sm font-medium <?php 
              switch ($project['priority']) {
                  case 'High': echo 'bg-red-100 text-red-800'; break;
                  case 'Medium': echo 'bg-yellow-100 text-yellow-800'; break;
                  case 'Low': echo 'bg-green-100 text-green-800'; break;
                  default: echo 'bg-gray-100 text-gray-800'; break;
              }
          ?>">
              Priority: <?php echo htmlspecialchars($project['priority']); ?>
          </span>
          
          <!-- Project Category Tag -->
          <span class="px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
              <?php echo htmlspecialchars($project['category_name'] ? $project['category_name'] : 'Uncategorized'); ?>
          </span>
          
          <!-- Billing Type Tag -->
          <span class="px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
              Billing: <?php echo htmlspecialchars($project['billing_type'] ? $project['billing_type'] : 'N/A'); ?>
          </span>
      </div>
      
      <!-- Progress Bar -->
      <div class="mt-4">
          <div class="flex justify-between mb-1">
              <span class="text-sm font-medium text-blue-700">Project Progress</span>
              <span class="text-sm font-medium text-blue-700"><?php echo $percentage_completed; ?>%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div class="bg-green-500 h-2.5 rounded-full" style="width: <?php echo $percentage_completed; ?>%"></div>
          </div>
          <div class="mt-2 text-sm text-gray-600">
              <?php echo $completed_tasks; ?> of <?php echo $total_tasks; ?> tasks completed.
          </div>
      </div>
  </div>

  <!-- Project Information Section -->
  <div class="bg-white p-6 rounded-2xl shadow-xl mb-8 border-l-4 border-blue-600 transition hover:shadow-2xl">
      <h2 class="text-2xl font-semibold text-gray-900 mb-4 relative">
          <i class="fas fa-project-diagram absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Project Information
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Project ID:</span> <?php echo htmlspecialchars($project['project_id']); ?></p>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Name:</span> <?php echo htmlspecialchars($project['name']); ?></p>
              <p class="text-gray-700 mb-2">
                <span class="font-semibold text-gray-800">Assigned To:</span> <?php echo htmlspecialchars($lead_customer_name); ?>
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
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Status:</span> 
                  <span class="px-2 py-1 rounded-full text-xs font-medium <?php 
                      switch ($project['status']) {
                          case 'In Progress': echo 'bg-blue-200 text-blue-800'; break;
                          case 'Completed': echo 'bg-green-200 text-green-800'; break;
                          case 'On Hold': echo 'bg-yellow-200 text-yellow-800'; break;
                          case 'Canceled': echo 'bg-red-200 text-red-800'; break;
                          default: echo 'bg-gray-200 text-gray-800'; break;
                      }
                  ?>"><?php echo htmlspecialchars($project['status']); ?></span>
              </p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <form method="POST" action="">
                      <?php echo csrfTokenInput(); ?>
                      <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
                      <label for="new_priority" class="block text-gray-700 font-semibold mb-2">Update Priority</label>
                      <select name="new_priority" id="new_priority" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="this.form.submit()">
                          <option value="Low" <?php if ($project['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                          <option value="Medium" <?php if ($project['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                          <option value="High" <?php if ($project['priority'] === 'High') echo 'selected'; ?>>High</option>
                      </select>
                  </form>
              </div>
              <p class="text-gray-700 mb-2"><span class="font-semibold text-gray-800">Category:</span>
                  <a href="<?php echo BASE_URL; ?>projects/categories/view?id=<?php echo $project['project_category_id']; ?>" class="text-blue-600 hover:underline">
                    <?php echo htmlspecialchars($project['category_name'] ? $project['category_name'] : 'Uncategorized'); ?></a>
              </p>
          </div>
      </div>
      <div class="mt-4 flex flex-wrap gap-4">
          <?php if($project['budget']): ?>
              <p class="text-gray-700 text-md mb-2">
                  <span class="font-semibold text-gray-800">Budget:</span> 
                  <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded"><?php echo '$' . htmlspecialchars($project['budget']); ?></span>
              </p>
          <?php endif; ?>
      </div>
      <?php if ($note_count > 0): ?>
<div class="relative inline-block">
  <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>&tab=notes" 
     class="block bg-yellow-500 text-white font-bold py-2 px-6 rounded-tl-lg rounded-br-lg shadow-lg hover:bg-yellow-600 transition">
    Check Notes
  </a>
  <div class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
    <?php echo $note_count; ?>
  </div>
</div>

      <?php endif; ?>
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

  <!-- Project Tasks Section -->
  <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
          <i class="fas fa-list-check absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> Project Tasks
      </h2>
      <div class="mb-4 text-sm text-gray-600">
          <span class="mr-4">Total Tasks: <strong><?php echo $total_tasks; ?></strong></span>
          <span class="mr-4">Completed: <strong><?php echo $completed_tasks; ?></strong></span>
          <span>Pending: <strong><?php echo $total_tasks - $completed_tasks; ?></strong></span>
      </div>
      <table class="w-full text-left">
          <thead>
              <tr class="bg-gray-50">
                  <th class="px-4 py-3">Task Name</th>
                  <th class="px-4 py-3">Due Date</th>
                  <th class="px-4 py-3">Assigned To</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3">Actions</th>
              </tr>
          </thead>
          <tbody>
              <?php if($tasks): ?>
                  <?php foreach ($tasks as $task): ?>
                      <tr class="border-b transition hover:bg-gray-100">
                          <td class="px-4 py-3">
                              <a href="<?php echo BASE_URL; ?>tasks/view?id=<?php echo $task['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-gray-800 hover:underline">
                                  <?php echo htmlspecialchars($task['task_name']); ?>
                              </a>
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
                              <a href="<?php echo BASE_URL; ?>tasks/edit?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline">
                                  <i class="fas fa-edit"></i>
                              </a>
                              <a href="<?php echo BASE_URL; ?>tasks/delete?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline ml-2">
                                  <i class="fas fa-trash-alt"></i>
                              </a>
                              <a href="<?php echo BASE_URL; ?>tasks/toggle_status?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline ml-2">
                                  <?php
                                      if ($task['status'] === 'To Do') {
                                          echo '<i class="fas fa-play"></i>';
                                      } else if ($task['status'] === 'In Progress') {
                                          echo '<i class="fas fa-check-double"></i>';
                                      } else {
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

  <!-- Project Notes Section Styled as a Sticky Note -->

  <div id="notes-section" class="relative border-4 border-yellow-600 border-dashed p-6 mb-8 rounded-lg font-serif">  <!-- Optional Sticky Note Pin -->
  <div class="absolute top-0 right-0 mt-[-10px] mr-[-10px]">
    <i class="fas fa-thumbtack text-yellow-600 text-2xl transform rotate-45"></i>
  </div>
  <h2 class="text-xl font-bold text-yellow-800 mb-4">
    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i> Project Notes
  </h2>
  <?php if ($notes): ?>
    <ul class="list-disc pl-5 space-y-2">
      <?php foreach ($notes as $note): ?>
        <li class="border-b border-yellow-200 pb-2">
          <a href="<?php echo BASE_URL; ?>notes/view?id=<?php echo $note['id']; ?>" class="text-yellow-800 hover:underline">
            <?php echo htmlspecialchars($note['title']); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="text-yellow-700">No notes found for this project.</p>
  <?php endif; ?>
</div>


  <!-- Feature Tracker Section -->
  <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Feature Tracker</h2>
      <?php if ($features): ?>
          <table class="w-full text-left">
              <thead>
                  <tr>
                      <th class="px-4 py-2">Feature Title</th>
                      <th class="px-4 py-2">Priority</th>
                      <th class="px-4 py-2">Status</th>
                      <th class="px-4 py-2">Actions</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($features as $feature): ?>
                      <tr class="border-b">
                          <td class="px-4 py-2"><?php echo htmlspecialchars($feature['feature_title']); ?></td>
                          <td class="px-4 py-2">
                              <span class="px-2 py-1 rounded-full text-xs font-medium <?php 
                                  switch ($feature['priority']) {
                                      case 'High': echo 'bg-red-200 text-red-800'; break;
                                      case 'Medium': echo 'bg-yellow-200 text-yellow-800'; break;
                                      case 'Low': echo 'bg-green-200 text-green-800'; break;
                                      default: echo 'bg-gray-200 text-gray-800'; break;
                                  }
                              ?>">
                                  <?php echo htmlspecialchars($feature['priority']); ?>
                              </span>
                          </td>
                          <td class="px-4 py-2">
                              <span class="px-2 py-1 rounded-full text-xs font-medium <?php 
                                  switch ($feature['status']) {
                                      case 'Completed': echo 'bg-green-200 text-green-800'; break;
                                      case 'In Progress': echo 'bg-blue-200 text-blue-800'; break;
                                      case 'Pending': echo 'bg-gray-200 text-gray-800'; break;
                                      default: echo 'bg-gray-200 text-gray-800'; break;
                                  }
                              ?>">
                                  <?php echo htmlspecialchars($feature['status']); ?>
                              </span>
                          </td>
                          <td class="px-4 py-2">
                              <a href="<?php echo BASE_URL; ?>projects/features/view?id=<?php echo $feature['id']; ?>" class="text-blue-600 hover:underline">View</a>
                              <a href="<?php echo BASE_URL; ?>projects/features/edit?id=<?php echo $feature['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-blue-600 hover:underline ml-2">Edit</a>
                              <a href="<?php echo BASE_URL; ?>projects/features/delete?id=<?php echo $feature['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-red-600 hover:underline ml-2" onclick="return confirm('Are you sure you want to delete this feature?')">Delete</a>
                          </td>
                      </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
      <?php else: ?>
          <p class="text-gray-600">No Features added yet to the project.</p>
      <?php endif; ?>
      <div class="flex justify-between items-center mt-4">
          <a href="<?php echo BASE_URL; ?>projects/features/add?project_id=<?php echo $project_id; ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 inline-block">Add Feature</a>
          <a href="<?php echo BASE_URL; ?>projects/features/manage?project_id=<?php echo $project_id; ?>" class="text-blue-600 hover:underline">Manage All Features</a>
      </div>
  </div>

  <!-- Issue Tracker Section -->
  <div class="bg-white p-6 rounded-2xl shadow-xl">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Issue Tracker</h2>
      <?php if ($issues): ?>
          <table class="w-full text-left">
              <thead>
                  <tr>
                      <th class="px-4 py-2">Issue ID</th>
                      <th class="px-4 py-2">Title</th>
                      <th class="px-4 py-2">Reported By</th>
                      <th class="px-4 py-2">Status</th>
                      <th class="px-4 py-2">Actions</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($issues as $issue): ?>
                      <tr class="border-b">
                          <td class="px-4 py-2"><?php echo htmlspecialchars($issue['issue_id']); ?></td>
                          <td class="px-4 py-2"><?php echo htmlspecialchars($issue['issue_title']); ?></td>
                          <td class="px-4 py-2"><?php echo htmlspecialchars($issue['reported_by_name']); ?></td>
                          <td class="px-4 py-2">
                              <span class="px-2 py-1 rounded-full text-xs font-medium <?php 
                                  switch ($issue['status']) {
                                      case 'Resolved': echo 'bg-green-200 text-green-800'; break;
                                      case 'Open': echo 'bg-red-200 text-red-800'; break;
                                      case 'In Progress': echo 'bg-blue-200 text-blue-800'; break;
                                      default: echo 'bg-gray-200 text-gray-800'; break;
                                  }
                              ?>">
                                  <?php echo htmlspecialchars($issue['status']); ?>
                              </span>
                          </td>
                          <td class="px-4 py-2">
                              <a href="<?php echo BASE_URL; ?>projects/issues/view?id=<?php echo $issue['id']; ?>" class="text-blue-600 hover:underline">View</a>
                              <a href="<?php echo BASE_URL; ?>projects/issues/edit?id=<?php echo $issue['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-blue-600 hover:underline ml-2">Edit</a>
                              <a href="<?php echo BASE_URL; ?>projects/issues/delete?id=<?php echo $issue['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-red-600 hover:underline ml-2" onclick="return confirm('Are you sure you want to delete this issue?')">Delete</a>
                          </td>
                      </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
      <?php else: ?>
          <p class="text-gray-600">No Issues reported yet for the project.</p>
      <?php endif; ?>
      <div class="flex justify-between items-center mt-4">
          <a href="<?php echo BASE_URL; ?>projects/issues/add?project_id=<?php echo $project_id; ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 inline-block">Report New Issue</a>
          <a href="<?php echo BASE_URL; ?>projects/issues/manage?project_id=<?php echo $project_id; ?>" class="text-blue-600 hover:underline">Manage All Issues</a>
      </div>
  </div>

  <div class="mt-4">
      <a href="<?php echo BASE_URL; ?>projects/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Projects</a>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to notes section if the URL tab parameter is set to notes
    <?php if (isset($_GET['tab']) && $_GET['tab'] === 'notes'): ?>
        const notesSection = document.getElementById('notes-section');
        if (notesSection) {
            notesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    <?php endif; ?>
});
</script>
