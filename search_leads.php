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

// Initialize filters
$filters = [];
$params = [];

// Search by name, email, or phone
if (!empty($search)) {
    $filters[] = "(name LIKE :search OR email LIKE :search OR phone LIKE :search)";
    $params[':search'] = "%$search%";
}

// Filter by category
if (!empty($_GET['category_id'])) {
    $filters[] = "category_id = :category_id";
    $params[':category_id'] = $_GET['category_id'];
}

// Filter by status
if (!empty($_GET['status'])) {
    $filters[] = "status = :status";
    $params[':status'] = $_GET['status'];
}

// Filter by date range
if (!empty($_GET['start_date'])) {
    $filters[] = "created_at >= :start_date";
    $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $filters[] = "created_at <= :end_date";
    $params[':end_date'] = $_GET['end_date'] . ' 23:59:59'; // Include the entire end date
}

// Filter by location
if (!empty($_GET['city'])) {
    $filters[] = "city LIKE :city";
    $params[':city'] = "%{$_GET['city']}%";
}
if (!empty($_GET['state'])) {
    $filters[] = "state LIKE :state";
    $params[':state'] = "%{$_GET['state']}%";
}
if (!empty($_GET['country'])) {
    $filters[] = "country LIKE :country";
    $params[':country'] = "%{$_GET['country']}%";
}

// Build the base query
$query = "SELECT * FROM leads";

// Add filters to the query
if (!empty($filters)) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

// Add sorting and pagination
$query .= " ORDER BY $sort_by $order LIMIT :limit OFFSET :offset";

// Fetch total leads count (for pagination)
$count_query = "SELECT COUNT(*) as total FROM leads";
if (!empty($filters)) {
    $count_query .= " WHERE " . implode(" AND ", $filters);
}
$stmt = $conn->prepare($count_query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$total_leads = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_leads / $per_page);

// Fetch leads for the current page
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
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
    <!-- Search by Name, Email, or Phone -->
    <input type="text" name="search" placeholder="Search by name, email, or phone" value="<?php echo htmlspecialchars($search); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">

    <!-- Category Filter -->
    <div class="mt-4">
        <label for="category_id" class="block text-gray-700">Category</label>
        <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">All Categories</option>
            <?php
            $stmt = $conn->prepare("SELECT * FROM categories");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo isset($_GET['category_id']) && $_GET['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Status Filter -->
    <div class="mt-4">
        <label for="status" class="block text-gray-700">Status</label>
        <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            <option value="">All Statuses</option>
            <option value="New" <?php echo isset($_GET['status']) && $_GET['status'] == 'New' ? 'selected' : ''; ?>>New</option>
            <option value="Contacted" <?php echo isset($_GET['status']) && $_GET['status'] == 'Contacted' ? 'selected' : ''; ?>>Contacted</option>
            <option value="Converted" <?php echo isset($_GET['status']) && $_GET['status'] == 'Converted' ? 'selected' : ''; ?>>Converted</option>
        </select>
    </div>

    <!-- Date Range Filter -->
    <div class="mt-4">
        <label for="start_date" class="block text-gray-700">Start Date</label>
        <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
    </div>
    <div class="mt-4">
        <label for="end_date" class="block text-gray-700">End Date</label>
        <input type="date" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
    </div>

    <!-- Location Filter -->
    <div class="mt-4">
        <label for="city" class="block text-gray-700">City</label>
        <input type="text" name="city" id="city" placeholder="City" value="<?php echo isset($_GET['city']) ? htmlspecialchars($_GET['city']) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
    </div>
    <div class="mt-4">
        <label for="state" class="block text-gray-700">State</label>
        <input type="text" name="state" id="state" placeholder="State" value="<?php echo isset($_GET['state']) ? htmlspecialchars($_GET['state']) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
    </div>
    <div class="mt-4">
        <label for="country" class="block text-gray-700">Country</label>
        <input type="text" name="country" id="country" placeholder="Country" value="<?php echo isset($_GET['country']) ? htmlspecialchars($_GET['country']) : ''; ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
    </div>

    <!-- Search Button -->
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
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">City</th>
                    <th class="px-4 py-2">State</th>
                    <th class="px-4 py-2">Country</th>
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
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['status']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['city']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['state']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($lead['country']); ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="delete_lead.php?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="px-4 py-2 text-center text-gray-600">No leads found.</td>
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