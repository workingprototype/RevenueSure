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
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10; // Leads per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Initialize filters
$filters = [];
$params = [];

// Search by name, email, or phone
if (!empty($search)) {
    $filters[] = "(leads.name LIKE :search OR leads.email LIKE :search OR leads.phone LIKE :search)";
    $params[':search'] = "%$search%";
}

// Filter by category
if (!empty($_GET['category_id'])) {
    $filters[] = "leads.category_id = :category_id";
    $params[':category_id'] = $_GET['category_id'];
}

// Filter by status
if (!empty($_GET['status'])) {
    $filters[] = "leads.status = :status";
    $params[':status'] = $_GET['status'];
}

// Filter by date range
if (!empty($_GET['start_date'])) {
    $filters[] = "leads.created_at >= :start_date";
    $params[':start_date'] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $filters[] = "leads.created_at <= :end_date";
    $params[':end_date'] = $_GET['end_date'] . ' 23:59:59'; // Include the entire end date
}
 // Filter by location
if (!empty($_GET['city'])) {
    $filters[] = "leads.city LIKE :city";
    $params[':city'] = "%{$_GET['city']}%";
}
if (!empty($_GET['state'])) {
    $filters[] = "leads.state LIKE :state";
    $params[':state'] = "%{$_GET['state']}%";
}
if (!empty($_GET['country'])) {
    $filters[] = "leads.country LIKE :country";
    $params[':country'] = "%{$_GET['country']}%";
}

// Build the base query with JOIN
$query = "SELECT leads.*, employees.name as assigned_employee, customers.name as customer_name, customers.id as customer_id_new
          FROM leads
          LEFT JOIN employees ON leads.assigned_to = employees.id
          LEFT JOIN customers ON leads.customer_id = customers.id";

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

// Define the categorize_lead function
function categorize_lead($score) {
    if ($score >= 10) {
        return "Hot";
    } elseif ($score >= 5) {
        return "Warm";
    } else {
        return "Cold";
    }
}
?>
  <div data-total-pages="<?php echo $total_pages; ?>">
 <form method="POST" action="mass_delete_leads.php">
    <div id="delete_button_container" class="hidden mb-4">
        <button type="submit" name="delete_selected" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Delete Selected</button>
    </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
        <?php if ($leads): ?>
            <?php foreach ($leads as $lead): ?>
                  <div class="bg-white rounded-lg shadow-md p-4 flex flex-col justify-between">
                   <div>
                      <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($lead['name']); ?></h2>
                            <input type="checkbox" name="selected_leads[]" value="<?php echo $lead['id']; ?>">
                        </div>
                          <p class="text-gray-600 mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
                          <p class="text-gray-600 mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($lead['phone']); ?></p>
                              <p class="text-gray-600 mb-2">
                                <strong>Status:</strong>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        <?php
                                        switch ($lead['status']) {
                                            case 'New':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'Contacted':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'Converted':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                        }
                                        ?>
                                        "><?php echo htmlspecialchars($lead['status']); ?>
                                    </span>
                                </p>
                                  <p class="text-gray-600 mb-2">
                                <strong>Category:</strong>
                                   <?php
                                        $stmt = $conn->prepare("SELECT name FROM categories WHERE id = :id");
                                        $stmt->bindParam(':id', $lead['category_id']);
                                        $stmt->execute();
                                        $category_name = $stmt->fetch(PDO::FETCH_ASSOC);
                                         echo $category_name ?  htmlspecialchars($category_name['name']) : 'Uncategorized';
                                  ?>
                             </p>
                               <p class="text-gray-600 mb-2">
                                <strong>City:</strong> <?php echo htmlspecialchars($lead['city'] ? $lead['city'] : 'N/A'); ?>
                              </p>
                               <p class="text-gray-600 mb-2">
                                 <strong>State:</strong> <?php echo htmlspecialchars($lead['state'] ? $lead['state'] : 'N/A'); ?>
                                </p>
                                <p class="text-gray-600 mb-2">
                                  <strong>Country:</strong> <?php echo htmlspecialchars($lead['country'] ? $lead['country'] : 'N/A'); ?>
                                </p>
                                  <p class="text-gray-600 mb-2">
                                    <strong>Score:</strong>  <?php
                                        $stmt = $conn->prepare("SELECT * FROM lead_scores WHERE lead_id = :lead_id");
                                        $stmt->bindParam(':lead_id', $lead['id']);
                                        $stmt->execute();
                                        $lead_score = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo $lead_score ? $lead_score['total_score'] : 0;
                                    ?>
                            </p>
                             <p class="text-gray-600 mb-2"><strong>Category:</strong> <?php echo $lead_score ? categorize_lead($lead_score['total_score']) : "Cold"; ?></p>
                            <p class="text-gray-600 mb-2">
                                <strong>Customer:</strong> 
                                <?php if($lead['customer_id_new']): ?>
                                    <a href="edit_customer.php?id=<?php echo $lead['customer_id_new']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($lead['customer_name'] ? $lead['customer_name'] : $lead['customer_id_new']); ?></a>
                                <?php else: ?>
                                  N/A
                                <?php endif; ?>
                             </p>
                            <p class="text-gray-600 mb-2"><strong>Assigned To:</strong> <?php echo htmlspecialchars($lead['assigned_employee'] ? $lead['assigned_employee'] : 'Unassigned'); ?></p>
                    </div>
                      <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <a href="view_lead.php?id=<?php echo $lead['id']; ?>" class="text-purple-600 hover:underline">View Lead</a>
                            <a href="edit_lead.php?id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                        </div>
                           <div class="flex space-x-2">
                                <a href="view_tasks.php?lead_id=<?php echo $lead['id']; ?>" class="text-green-600 hover:underline">View Tasks</a>
                                <a href="delete_lead.php?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                           </div>
                      </div>
                  </div>
                <?php endforeach; ?>
            <?php else: ?>
                 <p class="text-gray-600 text-center">No leads found.</p>
             <?php endif; ?>
        </div>
     </form>
</div>
<script>
    // Select All Checkbox
    document.getElementById('select_all')?.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="selected_leads[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>
<?php
?>