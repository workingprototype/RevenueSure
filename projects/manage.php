<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Mapping for status to colors and icons
$statusData = [
    'Not Started' => ['color' => 'gray',   'icon' => 'fas fa-hourglass-start'],
    'In Progress' => ['color' => 'blue',   'icon' => 'fas fa-spinner'],
    'Completed'   => ['color' => 'green',  'icon' => 'fas fa-check-circle'],
    'On Hold'     => ['color' => 'yellow', 'icon' => 'fas fa-pause-circle'],
    'Canceled'    => ['color' => 'red',    'icon' => 'fas fa-times-circle'],
];

// Mapping for priority to colors and icons
$priorityData = [
    'High'   => ['color' => 'red',    'icon' => 'fas fa-arrow-up'],
    'Medium' => ['color' => 'yellow', 'icon' => 'fas fa-arrow-right'],
    'Low'    => ['color' => 'green',  'icon' => 'fas fa-arrow-down'],
];

// Handle project update actions (status and/or priority)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['project_id']) && isset($_POST['new_status'])) {
    $project_id = (int) $_POST['project_id'];
    $new_status = $_POST['new_status'];

    // Validate status
    $allowed_statuses = ['Not Started', 'In Progress', 'Completed', 'On Hold', 'Canceled'];
    if (in_array($new_status, $allowed_statuses)) {
        try {
            $stmt = $conn->prepare("UPDATE projects SET status = :status WHERE id = :project_id");
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':project_id', $project_id);

            if ($stmt->execute()) {
                $success = "Project status updated successfully!";
                header("Location: " . BASE_URL . "projects/manage?success=true");
                exit();
            } else {
                $error = "Error updating project status.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "Database error. Please try again later.";
        }
    } else {
        $error = "Invalid project status.";
    }
}

// Fetch all projects along with category and manager details
$stmt = $conn->prepare("SELECT projects.*, project_categories.name as category_name, users.username as manager_name
                        FROM projects
                        LEFT JOIN project_categories ON projects.project_category_id = project_categories.id
                        LEFT JOIN users ON projects.project_manager_id = users.id 
                        ORDER BY created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalProjects = count($projects);

?>

<div class="container mx-auto p-6 fade-in">
  <!-- Header with Dashboard Title and Total Projects Badge -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
      <h1 class="text-4xl font-bold text-gray-900 mb-2">All Projects and Overview</h1>
      <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full font-semibold">
        Total Projects: <?php echo $totalProjects; ?>
      </span>
    </div>
    <a href="<?php echo BASE_URL; ?>projects/add" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-md flex items-center transition">
      <i class="fas fa-plus-circle mr-2"></i> Add Project
    </a>
  </div>

  <!-- Alert Messages -->
  <?php if ($success): ?>
    <div class="feedback-message feedback-success">
      <?php echo $success; ?>
    </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="feedback-message feedback-error">
      <?php echo $error; ?>
    </div>
  <?php endif; ?>

  <!-- Projects Grid -->
  <?php if ($projects): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($projects as $project): ?>
        <?php 
          $currentStatus = $project['status'];
          $badge = isset($statusData[$currentStatus]) ? $statusData[$currentStatus] : ['color' => 'gray', 'icon' => 'fas fa-info-circle'];
          $currentPriority = $project['priority'];
          $pBadge = isset($priorityData[$currentPriority]) ? $priorityData[$currentPriority] : null;

          // Check if there are any related notes
          $stmt = $conn->prepare("SELECT COUNT(*) FROM notes WHERE related_type = 'project' AND related_id = :project_id");
           $stmt->bindParam(':project_id', $project['id'], PDO::PARAM_INT);
            $stmt->execute();
            $note_count = $stmt->fetchColumn();
        ?>
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
          <!-- Card Header: Project Name & Status Badge -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2">
            <h2 class="text-xl font-bold text-gray-800">
              <?php echo htmlspecialchars($project['name']); ?>
            </h2>
            <span class="mt-2 sm:mt-0 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-<?php echo $badge['color']; ?>-100 text-<?php echo $badge['color']; ?>-800 ring-2 ring-<?php echo $badge['color']; ?>-300">
              <i class="<?php echo $badge['icon']; ?> mr-1"></i>
              <?php echo htmlspecialchars($currentStatus); ?>
            </span>
          </div>
          <!-- Project Details with Icons and Clickable Manager & Category -->
          <div class="space-y-1 text-gray-600 mb-4">
            <p>
              <span class="font-semibold">
                <i class="fas fa-user mr-1"></i> Manager:
              </span>
              <a href="<?php echo BASE_URL; ?>profile/unified_view?id=<?php echo $project['project_manager_id']; ?>" class="text-blue-600 hover:underline">
                <?php echo htmlspecialchars($project['manager_name']); ?>
              </a>
            </p>
            <p>
              <span class="font-semibold">
                <i class="fas fa-tag mr-1"></i> Category:
              </span>
              <a href="<?php echo BASE_URL; ?>projects/categories/view?id=<?php echo $project['project_category_id']; ?>" class="text-blue-600 hover:underline">
                <?php echo htmlspecialchars($project['category_name']); ?>
              </a>
            </p>
            <p>
              <span class="font-semibold">
                <i class="fas fa-calendar-alt mr-1"></i> Start Date:
              </span>
              <?php echo htmlspecialchars($project['start_date']); ?>
            </p>
            <p>
              <span class="font-semibold">
                <i class="fas fa-star mr-1"></i> Priority:
              </span>
              <?php 
                if ($pBadge) {
                  echo '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-' . $pBadge['color'] . '-100 text-' . $pBadge['color'] . '-800 ring-2 ring-' . $pBadge['color'] . '-300">';
                  echo '<i class="' . $pBadge['icon'] . ' mr-1"></i>' . htmlspecialchars($currentPriority);
                  echo '</span>';
                } else {
                  echo htmlspecialchars($project['priority']);
                }
              ?>
            </p>
          </div>
          <!-- Status Update Form -->
          <div class="mb-4">
            <form method="POST" action="">
              <?php echo csrfTokenInput(); ?>
              <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
              <label class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
              <select name="new_status" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-2 px-4 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" onchange="this.form.submit()">
                <option value="Not Started" <?php echo ($project['status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                <option value="In Progress" <?php echo ($project['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                <option value="Completed" <?php echo ($project['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="On Hold" <?php echo ($project['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                <option value="Canceled" <?php echo ($project['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
              </select>
            </form>
          </div>
          <!-- Priority Update Form -->
          <div class="mb-4">
            <form method="POST" action="">
              <?php echo csrfTokenInput(); ?>
              <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
              <label class="block text-sm font-medium text-gray-700 mb-1">Change Priority</label>
              <select name="new_priority" class="block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-<?php echo ($pBadge ? $pBadge['color'] : 'blue'); ?>-500 focus:border-<?php echo ($pBadge ? $pBadge['color'] : 'blue'); ?>-500" onchange="this.form.submit()">
                <option value="Low" <?php echo ($project['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                <option value="Medium" <?php echo ($project['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                <option value="High" <?php echo ($project['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
              </select>
            </form>
          </div>
          <!-- Action Links -->
          <div class="flex flex-wrap gap-3">
            <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>" class="text-purple-600 text-sm flex items-center">
              <i class="fas fa-eye mr-1"></i> View
            </a>
            <a href="<?php echo BASE_URL; ?>projects/gantt_chart?project_id=<?php echo $project['id']; ?>" class="text-blue-600 text-sm flex items-center">
              <i class="fas fa-chart-bar mr-1"></i> Gantt
            </a>
            <a href="<?php echo BASE_URL; ?>projects/kanban_board?project_id=<?php echo $project['id']; ?>" class="text-green-600 text-sm flex items-center">
              <i class="fas fa-columns mr-1"></i> Kanban
            </a>
            <a href="<?php echo BASE_URL; ?>projects/edit?id=<?php echo $project['id']; ?>" class="text-blue-600 text-sm flex items-center">
              <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="<?php echo BASE_URL; ?>projects/delete?id=<?php echo $project['id']; ?>" class="text-red-600 text-sm flex items-center" onclick="return confirm('Are you sure you want to delete this project?');">
              <i class="fas fa-trash-alt mr-1"></i> Delete
            </a>
             <?php if ($note_count > 0): ?>
                 <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>&tab=notes" class="text-gray-600 text-sm flex items-center">
                    <i class="fas fa-sticky-note mr-1"></i> View Linked Notes (<?php echo $note_count ?>)
                  </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center text-gray-600 py-10">
      No projects found.
    </div>
  <?php endif; ?>
</div>