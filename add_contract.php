<?php
ini_set('display_errors' , 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Fetch projects for the dropdown
$stmt = $conn->prepare("SELECT id, name FROM projects");
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customers for the dropdown
$stmt = $conn->prepare("SELECT id, name, email, company, address, phone FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Contract types for the dropdown
$stmt = $conn->prepare("SELECT id, name FROM contract_types");
$stmt->execute();
$contract_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $project_id = $_POST['project_id'] ?? null;
  $customer_id = $_POST['customer_id'] ?? null;
    $subject = trim($_POST['subject']);
    $contract_value = $_POST['contract_value'];
    $contract_type = $_POST['contract_type'] ?? null;
     $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];
      $hide_from_customer = isset($_POST['hide_from_customer']) ? 1 : 0;
    $contract_text = $_POST['contract_text'];


    if (empty($subject) || (empty($project_id) && empty($customer_id)) || empty($start_date) ) {
        $error = "Subject, Client or Project, and Start date are required.";
    }  else if ($contract_value < 0) {
        $error = "Contract Value must be greater than or equal to 0.";
    }
     else {
          $stmt = $conn->prepare("INSERT INTO contracts (project_id, customer_id, subject, contract_value, contract_type, start_date, end_date, description, hide_from_customer, contract_text) VALUES (:project_id, :customer_id, :subject, :contract_value, :contract_type, :start_date, :end_date, :description, :hide_from_customer, :contract_text)");
              $stmt->bindParam(':project_id', $project_id);
            $stmt->bindParam(':customer_id', $customer_id);
             $stmt->bindParam(':subject', $subject);
             $stmt->bindParam(':contract_value', $contract_value);
            $stmt->bindParam(':contract_type', $contract_type);
            $stmt->bindParam(':start_date', $start_date);
             $stmt->bindParam(':end_date', $end_date);
             $stmt->bindParam(':description', $description);
           $stmt->bindParam(':hide_from_customer', $hide_from_customer, PDO::PARAM_BOOL);
            $stmt->bindParam(':contract_text', $contract_text);
              if ($stmt->execute()) {
                   $contract_id = $conn->lastInsertId();
                   // Insert default status
                    $stmt = $conn->prepare("INSERT INTO contract_status (contract_id, status) VALUES (:contract_id, 'Draft')");
                     $stmt->bindParam(':contract_id', $contract_id);
                     $stmt->execute();
                  $success = "Contract added successfully!";
                 header("Location: view_contract.php?id=$contract_id&success=true");
                    exit();
             } else {
                  $error = "Error adding contract.";
               }
     }
}
// Fetch Contract types for dropdown
$stmt = $conn->prepare("SELECT id, name FROM contract_types");
$stmt->execute();
$contract_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Contract</h1>

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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="mb-4">
        <label for="project_id" class="block text-gray-700">Select Project</label>
        <select name="project_id" id="project_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" onchange="clearCustomer(this)">
            <option value="">Select Project</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
            <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-4">
          <label for="customer_id" class="block text-gray-700">Select Customer</label>
           <select name="customer_id" id="customer_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" onchange="updateCustomerInfo(this)">
             <option value="">Select Customer</option>
               <?php foreach ($customers as $customer): ?>
                  <option value="<?php echo $customer['id']; ?>" data-name = "<?php echo $customer['name']; ?>" data-email = "<?php echo $customer['email']; ?>" data-company = "<?php echo $customer['company']; ?>" data-address = "<?php echo $customer['address']; ?>" data-phone = "<?php echo $customer['phone']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                 <?php endforeach; ?>
           </select>
      </div>
            <div class="mb-4">
                 <label for="subject" class="block text-gray-700">Subject</label>
                 <input type="text" name="subject" id="subject" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
              </div>
            <div class="mb-4">
                <label for="contract_value" class="block text-gray-700">Contract Value</label>
                  <div class="flex">
                    <input type="number" name="contract_value" id="contract_value" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" min="0" step="0.01" required>
                        <select name="currency" id="currency" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                              <option value="USD">USD</option>
                              <option value="EUR">EUR</option>
                             <option value="GBP">GBP</option>
                        </select>
                     </div>
             </div>
             <div class="mb-4">
                    <label for="contract_type" class="block text-gray-700">Contract Type</label>
                    <div class="relative">
                         <select name="contract_type" id="contract_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                                <option value="">Select Contract Type</option>
                            <?php foreach ($contract_types as $type): ?>
                               <option value="<?php echo $type['name']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                             <?php endforeach; ?>
                        </select>
                        <button type="button"  onclick="openCategoryModal()" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-600 transition duration-200">
                                    <i class="fas fa-plus-circle"></i>
                             </button>
                    </div>
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
                 <label for="description" class="block text-gray-700">Description</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
              </div>
               <div class="mb-4">
                <label class="inline-flex items-center">
                  <input type="checkbox" name="hide_from_customer" id="hide_from_customer" class="mr-2">
                   <span class="text-gray-700">Hide from customer</span>
               </label>
           </div>
        </div>
           <div class="mb-4">
                <label for="contract_text" class="block text-gray-700">Contract Text</label>
                <textarea name="contract_text" id="contract_text" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Create Contract</button>
    </form>

    <!-- Add Category Modal -->
      <div id="categoryModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                     <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                  </div>
                     <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                       <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                           <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-headline">
                                  Add Contract Type
                                 </h3>
                               <form method="POST" action="">
                                    <div class="mb-4">
                                         <label for="add_contract_type_name" class="block text-gray-700">Contract Type Name</label>
                                           <input type="text" name="add_contract_type_name" id="add_contract_type_name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                                     </div>
                                      <div class="flex justify-end">
                                            <button type="button" onclick="addCategory()" class="bg-blue-700 text-white px-4 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Add Category</button>
                                            <button type="button" onclick="closeCategoryModal()" class="bg-gray-700 text-white px-4 py-3 rounded-xl hover:bg-gray-900 transition duration-300 shadow-md ml-2">Cancel</button>
                                        </div>
                                </form>
                           </div>
                       </div>
             </div>
         </div>
    </div>
</div>
<script>
      function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
         }
        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
         }
    function clearCustomer(selectElement){
          document.getElementById('customer_id').value = '';
    }
        function updateCustomerInfo(selectElement){
           const selectedOption = selectElement.options[selectElement.selectedIndex];
            if(selectedOption.value != ""){
               const name = selectedOption.dataset.name;
                const email = selectedOption.dataset.email;
                const company = selectedOption.dataset.company;
               const address = selectedOption.dataset.address;
               const phone = selectedOption.dataset.phone;
        } else {
         
          }
    }
      function addCategory() {
           const newCategoryName = document.getElementById('add_contract_type_name').value;
            if (newCategoryName.trim() !== '') {
                fetch('api.php', {
                   method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'add_contract_type', name: newCategoryName }),
                 })
                  .then(response => response.json())
                   .then(data => {
                        if (data.success) {
                            alert('Contract type added!');
                              closeCategoryModal()
                              document.getElementById('contract_type').innerHTML += `<option value="${newCategoryName}">${newCategoryName}</option>`;
                              document.getElementById('contract_type').value = newCategoryName;
                               document.getElementById('add_contract_type_name').value = '';
                           } else {
                              alert('Error adding contract type.');
                         }
                   })
                     .catch(error => {
                        console.error('Error adding category:', error);
                      });
            } else {
                alert('Please fill the field before adding.');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
             .create(document.querySelector('#contract_text'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable','undo', 'redo'],
                      heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                             { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                              ]
                           }
                 })
                 .catch(error => {
                     console.error(error);
                });
        });
</script>
<?php
// Include footer
require 'footer.php';
?>