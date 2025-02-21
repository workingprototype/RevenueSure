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
$stmt = $conn->prepare("SELECT project_issues.*, users.username AS reported_by_name FROM project_issues JOIN users ON project_issues.reported_by = users.id WHERE project_id = :project_id ORDER BY date_reported DESC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Issues for Project: <?php echo htmlspecialchars($project['name']); ?></h1>
    
    <div class="flex justify-between items-center mb-8">
        <a href="<?php echo BASE_URL; ?>projects/issues/add?project_id=<?php echo $project_id; ?>" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Report Issue</a>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-3">Issue ID</th>
                <th class="px-4 py-3">Title</th>
                 <th class="px-4 py-3">Reported By</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Priority</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($issues): ?>
                <?php foreach ($issues as $issue): ?>
                    <tr class="border-b transition hover:bg-gray-100">
                        <td class="px-4 py-3"><?php echo htmlspecialchars($issue['issue_id']); ?></td>
                         <td class="px-4 py-3"><?php echo htmlspecialchars($issue['issue_title']); ?></td>
                          <td class="px-4 py-3"><?php echo htmlspecialchars($issue['reported_by_name']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($issue['status']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($issue['priority']); ?></td>
                         <td class="px-4 py-3 flex gap-2">
                             <a href="<?php echo BASE_URL; ?>projects/issues/view?id=<?php echo urlencode($issue['id']); ?>" class="text-purple-600 hover:underline"><i class="fas fa-eye"></i> View</a>
                            <a href="<?php echo BASE_URL; ?>projects/issues/edit?id=<?php echo urlencode($issue['id']); ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                            <a href="<?php echo BASE_URL; ?>projects/issues/delete?id=<?php echo urlencode($issue['id']); ?>&project_id=<?php echo urlencode($project_id); ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this issue?')"><i class="fas fa-trash-alt"></i> Delete</a>
                         </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-600">No issues found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>