<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pagination
$per_page = 10; // Leads per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Fetch total leads count (for pagination)
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM leads WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search");
    $stmt->bindValue(':search', "%$search%");
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM leads");
}
$stmt->execute();
$total_leads = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_leads / $per_page);

// Fetch leads for the current page with sorting and search
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM leads WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search ORDER BY $sort_by $order LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':search', "%$search%");
} else {
    $stmt = $conn->prepare("SELECT * FROM leads ORDER BY $sort_by $order LIMIT :limit OFFSET :offset");
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Search Leads</h1>

<!-- Search Form -->
<form method="GET" action="" class="mb-8">
    <input type="text" name="search" placeholder="Search by name, email, or phone" value="<?php echo htmlspecialchars($search); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
    <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search</button>
</form>

<!-- Leads Table -->
<form method="POST" action="mass_delete_leads.php">
    <div class="flex justify-between items-center mb-4">
        <div>
            <button type="submit" name="delete_selected" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Delete Selected</button>
        </div>
        <div>
            <label for="sort_by" class="text-gray-700">Sort By:</label>
            <select name="sort_by" id="sort_by" onchange="window.location.href = '?search=<?php echo urlencode($search); ?>&sort_by=' + this.value + '&order=<?php echo $order; ?>'" class="px-4 py-2 border rounded-lg">
                <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name</option>
                <option value="email" <?php echo $sort_by === 'email' ? 'selected' : ''; ?>>Email</option>
                <option value="phone" <?php echo $sort_by === 'phone' ? 'selected' : ''; ?>>Phone</option>
                <option value="category_id" <?php echo $sort_by === 'category_id' ? 'selected' : ''; ?>>Category</option>
            </select>
            <label for="order" class="text-gray-700 ml-4">Order:</label>
            <select name="order" id="order" onchange="window.location.href = '?search=<?php echo urlencode($search); ?>&sort_by=<?php echo $sort_by; ?>&order=' + this.value" class="px-4 py-2 border rounded-lg">
                <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
            </select>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="px-4 py-2"><input type="checkbox" id="select_all"></th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Phone</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($leads): ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><input type="checkbox" name="selected_leads[]" value="<?php echo $lead['id']; ?>"></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['email']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['phone']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['category_id']); ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_lead.php?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-600">No leads found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<!-- Pagination -->
<div class="flex justify-center mt-6">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?search=<?php echo urlencode($search); ?>&sort_by=<?php echo $sort_by; ?>&order=<?php echo $order; ?>&page=<?php echo $i; ?>" class="px-4 py-2 mx-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

<script>
    // Select All Checkbox
    document.getElementById('select_all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_leads[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>

<?php
// Include footer
require 'footer.php';
?>