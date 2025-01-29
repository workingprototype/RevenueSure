<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

function generateProjectId($conn) {
    $stmt = $conn->prepare("SELECT MAX(id) AS max_id FROM projects");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_id = ($result['max_id'] ?? 0) + 1;
    return 'PROJ-' . date('Ymd') . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = trim($_POST['project_name']);
    $assigned_lead_customer_id = $_POST['assigned_lead_customer_id'];
    $assigned_lead_customer_type = $_POST['assigned_lead_customer_type'] ?? null;
    $project_manager_id = $_POST['project_manager_id'];
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $project_category_id = $_POST['project_category_id'];
    $billing_type = $_POST['billing_type'];
    $budget = $_POST['budget'];
    $description = $_POST['description'];

    if (empty($project_name) || empty($project_manager_id) || empty($start_date) || empty($project_category_id)) {
        $error = "All fields are required.";
    } else {
        $project_id = generateProjectId($conn);
        $stmt = $conn->prepare("
            INSERT INTO projects (project_id, name, assigned_lead_customer_id, assigned_lead_customer_type, project_manager_id, start_date, end_date, status, priority, project_category_id, billing_type, budget, description) 
            VALUES (:project_id, :name, :assigned_lead_customer_id, :assigned_lead_customer_type, :project_manager_id, :start_date, :end_date, :status, :priority, :project_category_id, :billing_type, :budget, :description)"
        );
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':name', $project_name);
        $stmt->bindParam(':assigned_lead_customer_id', $assigned_lead_customer_id);
        $stmt->bindParam(':assigned_lead_customer_type', $assigned_lead_customer_type);
        $stmt->bindParam(':project_manager_id', $project_manager_id);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':project_category_id', $project_category_id);
        $stmt->bindParam(':billing_type', $billing_type);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $success = "Project created successfully!";
        } else {
            $error = "Error creating project.";
        }
    }
}

// Fetch leads
$stmt = $conn->prepare("SELECT id, name FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch customers
$stmt = $conn->prepare("SELECT id, name FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project managers
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'admin' OR role = 'user' ");
$stmt->execute();
$project_managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project categories (from project_categories table)
$stmt = $conn->prepare("SELECT id, name FROM project_categories");
$stmt->execute();
$project_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Project</h1>
     <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
            <div class="mb-4">
               <label for="project_name" class="block text-gray-700">Project Name</label>
               <input type="text" name="project_name" id="project_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="assigned_lead_customer_type" class="block text-gray-700">Assigned to</label>
                <select name="assigned_lead_customer_type" id="assigned_lead_customer_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showLeadCustomerSelect(this.value)">
                        <option value="">Select</option>
                    <option value="lead">Lead</option>
                   <option value="customer">Customer</option>
                 </select>
            </div>
               <div class="mb-4 hidden" id="lead_select_container">
                 <label for="assigned_lead_customer_id" class="block text-gray-700">Assigned Lead/Customer</label>
                  <select name="assigned_lead_customer_id" id="assigned_lead_customer_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="updateContactInfo(this)">
                         <option value="">Select Lead</option>
                          <?php foreach ($leads as $lead): ?>
                            <option value="<?php echo $lead['id']; ?>" data-type = "lead">
                                <?php echo htmlspecialchars($lead['name']); ?>
                            </option>
                          <?php endforeach; ?>
                           <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" data-type = "customer">
                                 <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                          <?php endforeach; ?>
                     </select>
              </div>
             <div class="mb-4">
                   <label for="project_manager_id" class="block text-gray-700">Project Manager</label>
                <select name="project_manager_id" id="project_manager_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                     <option value="">Select Manager</option>
                    <?php foreach ($project_managers as $manager): ?>
                        <option value="<?php echo $manager['id']; ?>"><?php echo htmlspecialchars($manager['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="mb-4">
               <label for="start_date" class="block text-gray-700">Start Date</label>
               <input type="date" name="start_date" id="start_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
             <div class="mb-4">
                <label for="end_date" class="block text-gray-700">End Date</label>
              <input type="date" name="end_date" id="end_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
           </div>
            <div class="mb-4">
                <label for="status" class="block text-gray-700">Project Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                   <option value="Completed">Completed</option>
                    <option value="On Hold">On Hold</option>
                      <option value="Canceled">Canceled</option>
                </select>
            </div>
               <div class="mb-4">
                    <label for="priority" class="block text-gray-700">Priority</label>
                   <select name="priority" id="priority" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                       <option value="Low">Low</option>
                       <option value="Medium">Medium</option>
                       <option value="High">High</option>
                    </select>
                 </div>
               <div class="mb-4">
                    <label for="project_category_id" class="block text-gray-700">Category</label>
                   <select name="project_category_id" id="project_category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                         <option value="">Select Category</option>
                         <?php foreach ($project_categories as $category): ?>
                           <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                         <?php endforeach; ?>
                   </select>
                 </div>
                  <div class="mb-4">
                        <label for="billing_type" class="block text-gray-700">Billing Type</label>
                          <select name="billing_type" id="billing_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="Hourly">Hourly</option>
                           <option value="Fixed Price">Fixed Price</option>
                             <option value="Retainer">Retainer</option>
                        </select>
               </div>
              <div class="mb-4">
                 <label for="budget" class="block text-gray-700">Budget</label>
                  <input type="number" name="budget" id="budget" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
              </div>
             <div class="mb-4">
                  <label for="description" class="block text-gray-700">Description</label>
                  <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
             <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Create Project</button>
        </form>
    </div>
     <script>
    function showLeadCustomerSelect(type){
         const leadSelectContainer = document.getElementById('lead_select_container');
        if(type != ''){
              leadSelectContainer.classList.remove('hidden');
        }else {
             leadSelectContainer.classList.add('hidden');
         }
    }
      function updateContactInfo(selectElement) {
           const selectedOption = selectElement.options[selectElement.selectedIndex];
            console.log(selectedOption.dataset)
      }
    </script>
<?php
// Include footer
require 'footer.php';
?>