<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

try {
    // Fetch leads with pagination and sorting
    $sort_by = $_GET['sort_by'] ?? 'name';  // Default sort field
    $sort_order = $_GET['order'] ?? 'ASC';    // Default sort order
    $page = $_GET['page'] ?? 1;               // Default page number
    $per_page = 10;                          // Number of leads per page
    $offset = ($page - 1) * $per_page;

    $stmt = $conn->prepare("SELECT leads.*, categories.name AS category_name FROM leads LEFT JOIN categories ON leads.category_id = categories.id ORDER BY $sort_by $sort_order LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch total leads count (for pagination)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM leads");
    $stmt->execute();
    $total_leads = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_leads / $per_page);
     function getLeads(){
                return [
                    'id' => 1,
                    'name' => 'John Doe'
                    ];
            }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Database error. Please try again later.";
    $leads = [];
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Leads</h1>

    <!-- Add lead button -->
    <div class="flex justify-between items-center mb-8">
      <a href="<?php echo BASE_URL; ?>leads/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md"><i class="fas fa-plus-circle mr-2"></i>Add Lead</a>
    </div>

    <!-- Display any error or success messages -->
    <?php if ($error) : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success) : ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Leads Table -->
    <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">Name</th>
                     <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Phone</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($leads) : ?>
                    <?php foreach ($leads as $lead) : ?>
                        <tr class="border-b transition hover:bg-gray-100">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($lead['name']); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($lead['category_name'] ?? 'Uncategorized'); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($lead['email']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($lead['phone']); ?></td>
                            <td class="px-4 py-3 flex gap-2">
                                <a href="<?php echo BASE_URL; ?>leads/view?id=<?php echo urlencode($lead['id']); ?>" class="text-purple-600 hover:underline"><i class="fas fa-eye"></i> View</a>
                                <a href="<?php echo BASE_URL; ?>leads/edit?id=<?php echo urlencode($lead['id']); ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_URL; ?>leads/delete?id=<?php echo urlencode($lead['id']); ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this lead?')"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-gray-600">No leads found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-8">
        <?php if ($total_pages > 1) : ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="<?php echo BASE_URL; ?>leads/manage?page=<?php echo $i; ?>" class="px-4 py-2 mx-1 rounded-lg <?php echo ($page == $i) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-200'; ?> transition duration-300"><?php echo $i; ?></a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</div>