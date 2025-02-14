<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Handle project status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['project_id']) && isset($_POST['new_status'])) {
    $project_id = (int)$_POST['project_id'];
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

// Fetch all projects
$stmt = $conn->prepare("SELECT projects.*, project_categories.name as category_name, users.username as manager_name
                        FROM projects
                        LEFT JOIN project_categories ON projects.project_category_id = project_categories.id
                        LEFT JOIN users ON projects.project_manager_id = users.id 
                        ORDER BY created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
  <style>
    /* Global Styles */
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background-color: #f8f8f8;
      color: #333;
      margin: 0;
      padding: 1rem;
    }
    h1 {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: #111;
    }
    /* Header and Buttons */
    .header {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    .header-top {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
    }
    .apple-button {
      background-color: #007aff;
      color: #fff;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-size: 1rem;
      display: inline-flex;
      align-items: center;
      transition: background-color 0.3s ease;
    }
    .apple-button svg {
      margin-right: 0.5rem;
    }
    .apple-button:hover {
      background-color: #005bb5;
    }
    .view-toggle {
      display: flex;
      gap: 0.5rem;
    }
    .toggle-button {
      padding: 0.5rem 1rem;
      background: #e5e5ea;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 0.95rem;
    }
    .toggle-button.active,
    .toggle-button:hover {
      background: #007aff;
      color: #fff;
    }
    /* Feedback Messages */
    .feedback-message {
      border: 1px solid;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }
    .feedback-success {
      background-color: #d1fae5;
      border-color: #34c759;
      color: #34c759;
    }
    .feedback-error {
      background-color: #ffd1d1;
      border-color: #ff3b30;
      color: #ff3b30;
    }
    /* Projects Container */
    .projects-container {
      /* default to card view styles */
    }
    /* Card View */
    .projects-container.card-view .project-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      padding: 1rem;
      margin-bottom: 1rem;
    }
    .project-card .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #e5e5e5;
      padding-bottom: 0.5rem;
      margin-bottom: 0.5rem;
    }
    .project-card .card-header h2 {
      margin: 0;
      font-size: 1.5rem;
    }
    .project-card .card-header .project-id {
      font-size: 0.9rem;
      color: #888;
    }
    .project-card .card-body p {
      margin: 0.5rem 0;
      font-size: 0.95rem;
    }
    .project-card .card-footer {
      margin-top: 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }
    /* List View */
    .projects-container.list-view {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    .projects-container.list-view .project-card {
      display: flex;
      align-items: center;
      padding: 1rem;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 1px 5px rgba(0,0,0,0.05);
    }
    .projects-container.list-view .project-card > div {
      flex: 1;
    }
    /* Grid View */
    .projects-container.grid-view {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1rem;
    }
    .projects-container.grid-view .project-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      padding: 1rem;
    }
    /* Status Select & Colors */
    .status-select {
      border: none;
      background: none;
      font-weight: 500;
      font-size: 0.9rem;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.2s ease;
    }
    .status-select:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(0, 122, 255, 0.5);
    }
    .status-not-started {
      background-color: #d1d1d6;
      color: #1c1c1e;
    }
    .status-in-progress {
      background-color: #c7ebff;
      color: #007aff;
    }
    .status-completed {
      background-color: #d1fae5;
      color: #34c759;
    }
    .status-on-hold {
      background-color: #fff4ce;
      color: #ff9500;
    }
    .status-canceled {
      background-color: #ffd1d1;
      color: #ff3b30;
    }
    /* Card Actions */
    .card-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    .card-actions a {
      display: inline-block;
      background-color: #007aff;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: background-color 0.3s ease;
    }
    .card-actions a:hover {
      background-color: #005bb5;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-top">
        <h1>All Projects and Overview</h1>
        <a href="<?php echo BASE_URL; ?>projects/add" class="apple-button">
          <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add Project
        </a>
      </div>
      <div class="view-toggle">
        <button class="toggle-button active" data-view="card">Card View</button>
        <button class="toggle-button" data-view="list">List View</button>
        <button class="toggle-button" data-view="grid">Grid View</button>
      </div>
    </div>

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

    <div id="projectsContainer" class="projects-container card-view">
      <?php if ($projects): ?>
        <?php foreach ($projects as $project): ?>
          <div class="project-card">
            <div class="card-header">
              <h2><?php echo htmlspecialchars($project['name']); ?></h2>
              <span class="project-id">#<?php echo htmlspecialchars($project['project_id']); ?></span>
            </div>
            <div class="card-body">
              <p><strong>Manager:</strong> <?php echo htmlspecialchars($project['manager_name']); ?></p>
              <p><strong>Category:</strong> <?php echo htmlspecialchars($project['category_name']); ?></p>
              <p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
              <p><strong>Priority:</strong> <?php echo htmlspecialchars($project['priority']); ?></p>
            </div>
            <div class="card-footer">
              <form method="POST" action="">
                <?php echo csrfTokenInput(); ?>
                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
                <?php 
                  $statusClass = '';
                  switch ($project['status']) {
                    case 'Not Started':
                      $statusClass = 'status-not-started';
                      break;
                    case 'In Progress':
                      $statusClass = 'status-in-progress';
                      break;
                    case 'Completed':
                      $statusClass = 'status-completed';
                      break;
                    case 'On Hold':
                      $statusClass = 'status-on-hold';
                      break;
                    case 'Canceled':
                      $statusClass = 'status-canceled';
                      break;
                    default:
                      $statusClass = '';
                      break;
                  }
                ?>
                <select name="new_status" class="status-select <?php echo $statusClass; ?>" onchange="this.form.submit()">
                  <option value="Not Started" <?php echo ($project['status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                  <option value="In Progress" <?php echo ($project['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                  <option value="Completed" <?php echo ($project['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="On Hold" <?php echo ($project['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                  <option value="Canceled" <?php echo ($project['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                </select>
              </form>
              <div class="card-actions">
                <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>">View</a>
                <a href="<?php echo BASE_URL; ?>projects/gantt_chart?project_id=<?php echo $project['id']; ?>">Gantt</a>
                <a href="<?php echo BASE_URL; ?>projects/kanban_board?project_id=<?php echo $project['id']; ?>">Kanban</a>
                <a href="<?php echo BASE_URL; ?>projects/edit?id=<?php echo $project['id']; ?>">Edit</a>
                <a href="<?php echo BASE_URL; ?>projects/delete?id=<?php echo $project['id']; ?>">Delete</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; padding: 2rem; color: #777;">No projects found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleButtons = document.querySelectorAll('.toggle-button');
      const container = document.getElementById('projectsContainer');

      toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all buttons, then add to the clicked one
          toggleButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          // Set container class based on selected view (card, list, grid)
          const view = this.getAttribute('data-view');
          container.className = 'projects-container ' + view + '-view';
        });
      });
    });
  </script>