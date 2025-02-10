<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch project details
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :id");
$stmt->bindParam(':id', $project_id);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: " . BASE_URL . "projects/manage");
    exit();
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
     }else {
            $stmt = $conn->prepare("UPDATE projects SET name = :name, assigned_lead_customer_id = :assigned_lead_customer_id, assigned_lead_customer_type = :assigned_lead_customer_type, project_manager_id = :project_manager_id, start_date = :start_date, end_date = :end_date, status = :status, priority = :priority, project_category_id = :project_category_id, billing_type = :billing_type, budget = :budget, description = :description WHERE id = :id");
            $stmt->bindParam(':id', $project_id);
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
           $success = "Project updated successfully!";
            header("Location: " . BASE_URL . "projects/view?id=$project_id&success=true");
             exit();
        } else {
            $error = "Error updating project.";
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
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'admin' OR role = 'user'");
$stmt->execute();
$project_managers = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch project categories
$stmt = $conn->prepare("SELECT id, name FROM project_categories");
$stmt->execute();
$project_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Project</h1>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
              Project updated successfully!
        </div>
    <?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <form method="POST" action="">
    <?php echo csrfTokenInput(); ?>
         <div class="mb-4">
               <label for="project_name" class="block text-gray-700">Project Name</label>
               <input type="text" name="project_name" id="project_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($project['name']); ?>" required>
            </div>
             <div class="mb-4">
                <label for="assigned_lead_customer_type" class="block text-gray-700">Assigned to</label>
                <select name="assigned_lead_customer_type" id="assigned_lead_customer_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showLeadCustomerSelect(this.value)">
                        <option value="" <?php if(!$project['assigned_lead_customer_type']) echo 'selected'; ?>>Select</option>
                    <option value="lead" <?php if($project['assigned_lead_customer_type'] === 'lead') echo 'selected'; ?>>Lead</option>
                   <option value="customer" <?php if($project['assigned_lead_customer_type'] === 'customer') echo 'selected'; ?>>Customer</option>
                 </select>
            </div>
               <div class="mb-4 <?php if(!$project['assigned_lead_customer_type']) echo 'hidden' ?>" id="lead_select_container">
                 <label for="assigned_lead_customer_id" class="block text-gray-700">Assigned Lead/Customer</label>
                  <select name="assigned_lead_customer_id" id="assigned_lead_customer_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="updateContactInfo(this)">
                       <option value="">Select Lead</option>
                          <?php foreach ($leads as $lead): ?>
                            <option value="<?php echo $lead['id']; ?>" data-type = "lead"  <?php if($project['assigned_lead_customer_id'] == $lead['id'] && $project['assigned_lead_customer_type'] == 'lead') echo 'selected'; ?>>
                                <?php echo htmlspecialchars($lead['name']); ?>
                            </option>
                          <?php endforeach; ?>
                           <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" data-type = "customer"  <?php if($project['assigned_lead_customer_id'] == $customer['id'] && $project['assigned_lead_customer_type'] == 'customer') echo 'selected'; ?>>
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
                        <option value="<?php echo $manager['id']; ?>" <?php if ($project['project_manager_id'] == $manager['id']) echo 'selected'; ?>><?php echo htmlspecialchars($manager['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="start_date" class="block text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="end_date" class="block text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($project['end_date'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
              <div class="mb-4">
                  <label for="status" class="block text-gray-700">Project Status</label>
                  <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                      <option value="Not Started" <?php if ($project['status'] === 'Not Started') echo 'selected'; ?>>Not Started</option>
                        <option value="In Progress" <?php if ($project['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                         <option value="Completed" <?php if ($project['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
                         <option value="On Hold" <?php if ($project['status'] === 'On Hold') echo 'selected'; ?>>On Hold</option>
                          <option value="Canceled" <?php if ($project['status'] === 'Canceled') echo 'selected'; ?>>Canceled</option>
                    </select>
              </div>
             <div class="mb-4">
                    <label for="priority" class="block text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                           <option value="Low" <?php if ($project['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                           <option value="Medium" <?php if ($project['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                            <option value="High" <?php if ($project['priority'] === 'High') echo 'selected'; ?>>High</option>
                    </select>
              </div>
            <div class="mb-4">
                <label for="project_category_id" class="block text-gray-700">Category</label>
                <select name="project_category_id" id="project_category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                      <option value="">Select Category</option>
                      <?php foreach ($project_categories as $category): ?>
                         <option value="<?php echo $category['id']; ?>" <?php if($project['project_category_id'] == $category['id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                </select>
             </div>
             <div class="mb-4">
                  <label for="billing_type" class="block text-gray-700">Billing Type</label>
                     <select name="billing_type" id="billing_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="Hourly" <?php if ($project['billing_type'] === 'Hourly') echo 'selected'; ?>>Hourly</option>
                         <option value="Fixed Price" <?php if ($project['billing_type'] === 'Fixed Price') echo 'selected'; ?>>Fixed Price</option>
                           <option value="Retainer" <?php if ($project['billing_type'] === 'Retainer') echo 'selected'; ?>>Retainer</option>
                    </select>
               </div>
             <div class="mb-4">
                 <label for="budget" class="block text-gray-700">Budget</label>
                    <input type="number" name="budget" id="budget" value="<?php echo htmlspecialchars($project['budget'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
               </div>
              <div class="mb-4">
                   <label for="description" class="block text-gray-700">Description</label>
                  <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
            </div>
           <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Project</button>
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
         showLeadCustomerSelect(document.getElementById('assigned_lead_customer_type').value);
    </script>
