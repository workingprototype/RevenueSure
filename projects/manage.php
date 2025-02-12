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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Project Dashboard</title>
  <!-- Apple-inspired custom styles -->
  <style>
    /* Global Styles */
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      background-color: #f8f8f8;
      color: #333;
      margin: 0;
      padding: 0;
    }
    
    h1 {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: #111;
    }
    /* Header Buttons */
    .header-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1.5rem;
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
    /* Table Styles */
    .apple-table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-top: 1.5rem;
    }
    .apple-table thead {
      background-color: #f0f0f5;
    }
    .apple-table th, .apple-table td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #e5e5e5;
      font-size: 0.95rem;
    }
    .apple-table th {
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #555;
    }
    /* Status Select */
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
    /* Status Backgrounds */
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
    /* Action Links */
    .action-links a {
      color: #007aff;
      text-decoration: none;
      margin-right: 1rem;
      transition: color 0.2s ease;
    }
    .action-links a:hover {
      color: #005bb5;
    }
    .card-actions a {
      display: inline-block;
      background-color: #007aff;
      color: #fff;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      margin-right: 0.5rem;
      transition: background-color 0.3s ease;
    }
    .card-actions a:last-child {
      background-color: #ff3b30;
    }
    .card-actions a:last-child:hover {
      background-color: #d32f2f;
    }
    .card-actions a:hover {
      background-color: #005bb5;
    }
  </style>
  <div class="container">
    <h1>All Projects and Overview</h1>

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

    <div class="header-buttons">
      <a href="<?php echo BASE_URL; ?>projects/add" class="apple-button">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Project
      </a>
      <a href="<?php echo BASE_URL; ?>features/add" class="apple-button">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg> <i class="fas fa-magic mr-2"></i>
        Features Tracker
      </a>
      <a href="<?php echo BASE_URL; ?>issues/add" class="apple-button">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg> <i class="fas fa-bug mr-2"></i>
        Issue Tracker
      </a>
    </div>

    <table class="apple-table">
      <thead>
        <tr>
          <th>Project ID</th>
          <th>Name</th>
          <th>Manager</th>
          <th>Category</th>
          <th>Start Date</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($projects): ?>
          <?php foreach ($projects as $project): ?>
            <tr>
              <td><?php echo htmlspecialchars($project['project_id']); ?></td>
              <td><?php echo htmlspecialchars($project['name']); ?></td>
              <td><?php echo htmlspecialchars($project['manager_name']); ?></td>
              <td><?php echo htmlspecialchars($project['category_name']); ?></td>
              <td><?php echo htmlspecialchars($project['start_date']); ?></td>
              <td>
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
              </td>
              <td><?php echo htmlspecialchars($project['priority']); ?></td>
              <td>
                <div class="action-links">
                  <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>">View Project</a>
                  <a href="<?php echo BASE_URL; ?>projects/edit?id=<?php echo $project['id']; ?>">Edit Project</a>
                  <a href="<?php echo BASE_URL; ?>projects/delete?id=<?php echo $project['id']; ?>">Delete Project</a> <!-- Todo: Add discard/ move to bin -->
                </div>
                <div class="card-actions" style="margin-top: 0.75rem;">
                  <a href="<?php echo BASE_URL; ?>projects/features/manage?project_id=<?php echo $project['id']; ?>">
                  <i class="fas fa-magic mr-2"></i> Manage Features
                  </a>
                  <a href="<?php echo BASE_URL; ?>projects/issues/manage?project_id=<?php echo $project['id']; ?>">
                  <i class="fas fa-bug mr-2"></i> Manage Issues
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 2rem; color: #777;">
              No projects found.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    function confirmDelete(invoiceId) {
      if (confirm('Are you sure you want to delete this invoice?')) {
        window.location.href = 'invoices/delete?id=' + invoiceId;
      }
    }
  </script>