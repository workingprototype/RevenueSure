<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all projects
$stmt = $conn->prepare("SELECT projects.*, project_categories.name as category_name, users.username as manager_name
                        FROM projects
                        LEFT JOIN project_categories ON projects.project_category_id = project_categories.id
                        LEFT JOIN users ON projects.project_manager_id = users.id 
                        ORDER BY created_at DESC");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Projects</h1>

<!-- Add Project Button -->
<a href="add_project.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Project</a>

<!-- Projects Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Project ID</th>
                 <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Manager</th>
                 <th class="px-4 py-2">Category</th>
                <th class="px-4 py-2">Start Date</th>
                 <th class="px-4 py-2">Status</th>
                 <th class="px-4 py-2">Priority</th>
                  <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($projects): ?>
                <?php foreach ($projects as $project): ?>
                    <tr class="border-b">
                       <td class="px-4 py-2"><?php echo htmlspecialchars($project['project_id']); ?></td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($project['name']); ?></td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($project['manager_name']); ?></td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($project['category_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($project['start_date']); ?></td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-full <?php
                                    switch ($project['status']) {
                                        case 'Not Started':
                                            echo 'bg-gray-200 text-gray-800';
                                            break;
                                       case 'In Progress':
                                            echo 'bg-blue-200 text-blue-800';
                                           break;
                                        case 'Completed':
                                             echo 'bg-green-200 text-green-800';
                                              break;
                                      case 'On Hold':
                                             echo 'bg-yellow-200 text-yellow-800';
                                               break;
                                        case 'Canceled':
                                           echo 'bg-red-200 text-red-800';
                                               break;
                                        default:
                                             echo 'bg-gray-100 text-gray-800';
                                             break;
                                    }
                                    ?>"><?php echo htmlspecialchars($project['status']); ?>
                           </span>
                           </td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($project['priority']); ?></td>
                        <td class="px-4 py-2">
                            <a href="view_project.php?id=<?php echo $project['id']; ?>" class="text-purple-600 hover:underline">View</a>
                            <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                           <a href="delete_project.php?id=<?php echo $project['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                         </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="px-4 py-2 text-center text-gray-600">No projects found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
// Include footer
require 'footer.php';
?>