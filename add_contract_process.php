<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = isset($_POST['project_id']) && !empty($_POST['project_id']) ? $_POST['project_id'] : null;
    $customer_id = isset($_POST['customer_id']) && !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
    $subject = trim($_POST['subject']);
    $contract_value = $_POST['contract_value'];
    $contract_type = $_POST['contract_type'] ?? null;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?? null;
    $description = $_POST['description'];
    $hide_from_customer = isset($_POST['hide_from_customer']) ? 1 : 0;
      $bill_to_name = $_POST['bill_to_name'] ?? '';
    $bill_to_email = $_POST['bill_to_email'] ?? '';
      $bill_to_company = $_POST['bill_to_company'] ?? '';
      $bill_to_address = $_POST['bill_to_address'] ?? '';
    $contract_text = $_POST['contract_text'];
     $currency = $_POST['currency'] ?? 'USD';

     if(isset($_POST['add_contract_type_name'])){
                $new_contract_type_name = trim($_POST['add_contract_type_name']);
                     if (empty($new_contract_type_name)) {
                            $error = "Contract type name is required.";
                    } else {
                        // Check if the category already exists
                            $stmt = $conn->prepare("SELECT id FROM contract_types WHERE name = :name");
                            $stmt->bindParam(':name', $new_contract_type_name);
                             $stmt->execute();
                             if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $error = "A contract type with this name already exists.";
                            } else {
                                 // Insert the category
                                    $stmt = $conn->prepare("INSERT INTO contract_types (name) VALUES (:name)");
                                       $stmt->bindParam(':name', $new_contract_type_name);
                                    if ($stmt->execute()) {
                                            $success = "Contract type added successfully!";
                                           $contract_type  = $new_contract_type_name;
                                   } else {
                                         $error = "Error adding contract type.";
                                     }
                             }
                     }
            } else{
                  if (empty($subject) || (empty($project_id) && empty($customer_id)) || empty($start_date) || $contract_value < 0) {
                     $error = "All required fields must be completed and contract value must be >= 0.";
                    } else {
                         // Insert contract into database
                        $stmt = $conn->prepare("INSERT INTO contracts (project_id, customer_id, subject, contract_value, contract_type, start_date, end_date, description, hide_from_customer, contract_text) VALUES (:project_id, :customer_id, :subject, :contract_value, :contract_type, :start_date, :end_date, :description, :hide_from_customer, :contract_text)");
                      $stmt->bindParam(':project_id', $project_id);
                      $stmt->bindParam(':customer_id', $customer_id);
                       $stmt->bindParam(':subject', $subject);
                       $stmt->bindParam(':contract_value', $contract_value);
                       $stmt->bindParam(':contract_type', $contract_type);
                      $stmt->bindParam(':start_date', $start_date);
                       $stmt->bindParam(':end_date', $end_date);
                       $stmt->bindParam(':description', $description);
                        $stmt->bindParam(':hide_from_customer', $hide_from_customer);
                         $stmt->bindParam(':contract_text', $contract_text);
                    
                         if ($stmt->execute()) {
                            $contract_id = $conn->lastInsertId();
                              $stmt = $conn->prepare("INSERT INTO contract_status (contract_id, status) VALUES (:contract_id, :status)");
                                $stmt->bindParam(':contract_id', $contract_id);
                              $stmt->bindValue(':status', 'Draft');
                                $stmt->execute();

                              $success = "Contract created successfully!";
                              header("Location: view_contract.php?id=$contract_id&success=true");
                                 exit();
                         } else {
                                $error = "Error creating contract.";
                          }
                    }
         }
}
// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
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
    <div class="mt-6">
        <a href="manage_projects.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Projects</a>
   </div>
</div>

<?php
// Include footer
require 'footer.php';
?>