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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Dashboard</title>
   <script src="https://cdn.tailwindcss.com"></script>
   <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">
  <div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">All Projects and Overview</h1>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <div class="flex flex-wrap justify-between items-center mb-8">
         <a href="<?php echo BASE_URL; ?>projects/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Add Project
         </a>
    </div>
     <!-- Projects Table -->
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
         <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Project ID</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Name</th>
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Manager</th>
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Category</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Start Date</th>
                         <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                           <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Priority</th>
                           <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                    </tr>
                </thead>
                 <tbody class="text-gray-600">
                        <?php if ($projects): ?>
                            <?php foreach ($projects as $project): ?>
                                <tr class="border-b transition hover:bg-gray-100">
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($project['project_id']); ?></td>
                                   <td class="px-4 py-3"><?php echo htmlspecialchars($project['name']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($project['manager_name']); ?></td>
                                   <td class="px-4 py-3"><?php echo htmlspecialchars($project['category_name']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($project['start_date']); ?></td>
                                   <td class="px-4 py-3">
                                        <form method="POST" action="">
                                           <?php echo csrfTokenInput(); ?>
                                                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['id']); ?>">
                                                  <select name="new_status" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-2 px-4 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" onchange="this.form.submit()">
                                                       <option value="Not Started" <?php echo ($project['status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                                                        <option value="In Progress" <?php echo ($project['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                                       <option value="Completed" <?php echo ($project['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                        <option value="On Hold" <?php echo ($project['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                                        <option value="Canceled" <?php echo ($project['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                                                  </select>
                                        </form>
                                   </td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($project['priority']); ?></td>
                                    <td class="px-4 py-3 flex gap-2">
                                          <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>" class="text-purple-600 hover:underline"><i class="fas fa-eye"></i> View</a>
                                         <a href="<?php echo BASE_URL; ?>projects/gantt_chart?project_id=<?php echo $project['id']; ?>" class="text-blue-600 hover:underline">
                                          <i class="fas fa-chart-bar"></i> Gantt</a>
                                           <a href="<?php echo BASE_URL; ?>projects/kanban_board?project_id=<?php echo $project['id']; ?>" class="text-green-600 hover:underline">
                                        <i class="fas fa-columns"></i> Kanban</a>
                                           <a href="<?php echo BASE_URL; ?>projects/edit?id=<?php echo $project['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i>Edit</a>
                                              <a href="<?php echo BASE_URL; ?>projects/delete?id=<?php echo $project['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i> Delete</a>
                                 </td>
                            </tr>
                       <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                         <td colspan="8" class="px-4 py-2 text-center text-gray-600">No projects found.</td>
                         </tr>
                     <?php endif; ?>
                </tbody>
          </table>
     </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    function confirmDelete(invoiceId) {
      if (confirm('Are you sure you want to delete this invoice?')) {
        window.location.href = '<?php echo BASE_URL; ?>invoices/delete?id=' + invoiceId;
      }
    }
  </script>