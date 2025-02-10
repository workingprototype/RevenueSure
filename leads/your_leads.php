<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

// Sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Pagination
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10; // Leads per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Build the base query with JOIN
$query = "SELECT leads.*, employees.name as assigned_employee
          FROM leads
          LEFT JOIN employees ON leads.assigned_to = employees.id
          WHERE leads.assigned_to = :user_id";

// Add sorting and pagination
$query .= " ORDER BY $sort_by $order LIMIT :limit OFFSET :offset";

// Fetch total leads count (for pagination)
$count_query = "SELECT COUNT(*) as total FROM leads WHERE leads.assigned_to = :user_id";
$stmt = $conn->prepare($count_query);
$stmt->bindValue(':user_id', $user_id);

$stmt->execute();
$total_leads = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_leads / $per_page);


// Fetch leads for the current page
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id);
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
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Your Leads</h1>
    <div data-total-pages="<?php echo $total_pages; ?>">
      <form method="POST" action="leads/mass_delete">
      <?php echo csrfTokenInput(); ?>
            <div id="delete_button_container" class="hidden mb-4">
                <button type="submit" name="delete_selected" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Delete Selected</button>
            </div>
           <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                <?php if ($leads): ?>
                    <?php foreach ($leads as $lead): ?>
                         <div class="bg-white rounded-2xl shadow-xl p-6 flex flex-col justify-between transition hover:shadow-2xl border-l-4"
                              style="border-left-color:<?php
                                        switch ($lead['status']) {
                                            case 'New':
                                                 echo '#007aff';
                                                    break;
                                                case 'Contacted':
                                                    echo '#facc15';
                                                    break;
                                                case 'Converted':
                                                    echo '#22c55e';
                                                  break;
                                                default:
                                                  echo '#94a3b8';
                                                        break;
                                        }
                                    ?>;"
                             >
                                <div>
                                  <div class="flex justify-between items-start mb-4">
                                        <h2 class="text-2xl font-semibold text-gray-900">
                                              <?php echo htmlspecialchars($lead['name']); ?>
                                        </h2>
                                          <input type="checkbox" name="selected_leads[]" value="<?php echo $lead['id']; ?>">
                                    </div>
                                      <p class="text-gray-700 mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
                                    <p class="text-gray-700 mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($lead['phone']); ?></p>
                                     <p class="text-gray-700 mb-2">
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
                                  <p class="text-gray-600 mb-2"><strong>Assigned To:</strong> <?php echo htmlspecialchars($lead['assigned_employee'] ? $lead['assigned_employee'] : 'Unassigned'); ?></p>
                           </div>
                           <div class="flex justify-between items-center mt-4">
                                 <div class="flex gap-2">
                                     <a href="<?php echo BASE_URL; ?>leads/view?id=<?php echo $lead['id']; ?>" class="text-purple-600 hover:underline">View</a>
                                   <a href="<?php echo BASE_URL; ?>leads/edit?id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                </div>
                             <div class="flex gap-2">
                                      <a href="<?php echo BASE_URL; ?>tasks/viewtasks?lead_id=<?php echo $lead['id']; ?>" class="text-green-600 hover:underline">View Tasks</a>
                                     <a href="<?php echo BASE_URL; ?>leads/edit?id=<?php echo $lead['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                                 </div>
                            </div>
                       </div>
                    <?php endforeach; ?>
                <?php else: ?>
                      <p class="text-gray-600 text-center">No leads assigned to you.</p>
                <?php endif; ?>
            </div>
         </form>
    </div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
             const selectAllCheckbox = document.createElement('input');
                selectAllCheckbox.type = 'checkbox';
               selectAllCheckbox.id = 'select_all';
              const formContainer = document.querySelector('form');
               const container = document.createElement('div');
                container.classList.add('flex', 'items-center', 'justify-end', 'mb-4', 'gap-2');
                const label = document.createElement('label');
                label.for = 'select_all';
                label.textContent = 'Select All';
                 container.appendChild(selectAllCheckbox);
                container.appendChild(label)
                formContainer.insertBefore(container, formContainer.firstChild);

            const checkboxes = document.querySelectorAll('input[name="selected_leads[]"]');
            const deleteButtonContainer = document.getElementById('delete_button_container');
            function updateDeleteButtonVisibility() {
                 let checkedCount = 0;
                 checkboxes.forEach(checkbox => {
                   if (checkbox.checked) {
                     checkedCount++;
                    }
                });
                 if(checkedCount > 0) {
                      deleteButtonContainer.classList.remove('hidden');
                } else {
                     deleteButtonContainer.classList.add('hidden');
                 }
            }
            // Initial check
           updateDeleteButtonVisibility();

            // Select All Checkbox
            selectAllCheckbox.addEventListener('click', function() {
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                  updateDeleteButtonVisibility();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                   updateDeleteButtonVisibility();
                });
            });
        });
    </script>