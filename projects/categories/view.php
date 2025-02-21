<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Get category ID from GET parameter
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM project_categories WHERE id = :id");
$stmt->bindParam(':id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: " . BASE_URL . "projects/categories/manage");
    exit();
}

// Fetch projects in this category
$stmt = $conn->prepare("SELECT projects.*, users.username AS manager_name FROM projects LEFT JOIN users ON projects.project_manager_id = users.id WHERE project_category_id = :category_id ORDER BY created_at DESC");
$stmt->bindParam(':category_id', $category_id);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate summary metrics
$total_projects = count($projects);
$total_budget = 0;
foreach ($projects as $project) {
    $total_budget += $project['budget'];
}

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Category: <?php echo htmlspecialchars($category['name']); ?></h1>

    <!-- Category Summary -->
    <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Category Overview</h2>
        <p class="text-gray-600">Category Name: <?php echo htmlspecialchars($category['name']); ?></p>
        <p class="text-gray-600">Total Projects: <?php echo htmlspecialchars($total_projects); ?></p>
        <p class="text-gray-600">Total Budget: $<?php echo number_format($total_budget, 2); ?></p>
    </div>

    <!-- Projects List -->
    <div class="bg-white p-6 rounded-2xl shadow-xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Projects in this Category</h2>
        <?php if ($projects): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($projects as $project): ?>
                    <div class="border rounded-md p-4 hover:shadow-lg transition-shadow duration-300">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($project['name']); ?></h3>
                        <p class="text-gray-600">Project ID: <?php echo htmlspecialchars($project['project_id']); ?></p>
                        <p class="text-gray-600">Manager: <?php echo htmlspecialchars($project['manager_name']); ?></p>
                         <p class="text-gray-600">Start Date: <?php echo htmlspecialchars($project['start_date']); ?></p>
                         <p class="text-gray-600">Budget: $<?php echo number_format($project['budget'], 2); ?></p>
                        <div class="mt-4 flex justify-between items-center">
                            <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $project['id']; ?>" class="text-blue-600 hover:underline">View Project Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No projects found in this category.</p>
        <?php endif; ?>
    </div>
</div>