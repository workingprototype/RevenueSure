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
// Fetch features for the project
$stmt = $conn->prepare("SELECT project_features.*, users.username as owner_name FROM project_features LEFT JOIN users ON project_features.owner_id = users.id WHERE project_id = :project_id ORDER BY created_date DESC");
$stmt->bindParam(':project_id', $project_id);
$stmt->execute();
$features = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Features for Project: <?php echo htmlspecialchars($project['name']); ?></h1>
    
    <div class="flex justify-between items-center mb-8">
        <a href="<?php echo BASE_URL; ?>projects/features/add?project_id=<?php echo $project_id; ?>" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Feature</a>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Feature Title</th>
                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Priority</th>
                 <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Owner</th>
                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Estimated Completion Date</th>
                <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($features): ?>
                <?php foreach ($features as $feature): ?>
                    <tr class="border-b transition hover:bg-gray-100">
                        <td class="px-4 py-3"><?php echo htmlspecialchars($feature['feature_title']); ?></td>
                           <td class="px-4 py-3"><?php echo htmlspecialchars($feature['priority']); ?></td>
                           <td class="px-4 py-3">
                             <?php
                                    $stmt = $conn->prepare("SELECT username FROM users WHERE id = :id");
                                    $stmt->bindParam(':id', $feature['owner_id']);
                                    $stmt->execute();
                                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo $user ? htmlspecialchars($user['username']) : 'Unassigned'; ?>
                            </td>
                           <td class="px-4 py-3"><?php echo htmlspecialchars($feature['status']); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($feature['estimated_completion_date']); ?></td>
                                 <td class="px-4 py-3 flex gap-2">
                                <a href="<?php echo BASE_URL; ?>projects/features/view?id=<?php echo urlencode($feature['id']); ?>" class="text-purple-600 hover:underline"><i class="fas fa-eye"></i> View</a>
                                    <a href="<?php echo BASE_URL; ?>projects/features/edit?id=<?php echo urlencode($feature['id']); ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="<?php echo BASE_URL; ?>projects/features/delete?id=<?php echo urlencode($feature['id']); ?>&project_id=<?php echo urlencode($project_id); ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this feature?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                           
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-600">No features found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>