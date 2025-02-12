<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

// Fetch project details
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: " . BASE_URL . "projects/manage");
    exit();
}
// Fetch issues for the project
$stmt = $conn->prepare("SELECT project_issues.*, users.username AS reported_by_name FROM project_issues JOIN users ON project_issues.reported_by = users.id WHERE project_id = :project_id ORDER BY created_date DESC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Issues for Project: <?php echo htmlspecialchars($project['name']); ?></h1>

<!-- Add issue button -->
<a href="<?php echo BASE_URL; ?>projects/issues/add?project_id=<?php echo $project_id; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-6 inline-block">Add Issue</a>

<!-- issues Table -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">Issue ID</th>
                <th class="px-4 py-2">Title</th>
                 <th class="px-4 py-2">Reported By</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Priority</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($issues): ?>
                <?php foreach ($issues as $issue): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($issue['issue_id']); ?></td>
                         <td class="px-4 py-2"><?php echo htmlspecialchars($issue['issue_title']); ?></td>
                          <td class="px-4 py-2"><?php echo htmlspecialchars($issue['reported_by_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($issue['status']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($issue['priority']); ?></td>
                         <td class="px-4 py-2">
                             <a href="<?php echo BASE_URL; ?>projects/issues/view?id=<?php echo $issue['id']; ?>" class="text-blue-600 hover:underline">View</a>
                            <a href="<?php echo BASE_URL; ?>projects/issues/edit?id=<?php echo $issue['id']; ?>" class="text-blue-600 hover:underline ml-2">Edit</a>
                              <a href="<?php echo BASE_URL; ?>projects/issues/delete?id=<?php echo $issue['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                         </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-600">No issues found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>