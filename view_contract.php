<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$contract_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';
// Fetch contract details
$stmt = $conn->prepare("SELECT contracts.*, projects.name as project_name, customers.name as customer_name FROM contracts LEFT JOIN projects ON contracts.project_id = projects.id LEFT JOIN customers ON contracts.customer_id = customers.id WHERE contracts.id = :contract_id");
$stmt->bindParam(':contract_id', $contract_id);
$stmt->execute();
$contract = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contract) {
    header("Location: manage_contracts.php");
    exit();
}
$project_customer_name = '';
if($contract['project_name']){
    $project_customer_name = $contract['project_name'];
} else if($contract['customer_name']){
   $project_customer_name = $contract['customer_name'];
}

// Fetch existing signatures
$stmt = $conn->prepare("SELECT users.username, contract_signatures.* FROM contract_signatures LEFT JOIN users ON contract_signatures.user_id = users.id WHERE contract_id = :contract_id ORDER BY signed_at DESC");
$stmt->bindParam(':contract_id', $contract_id);
$stmt->execute();
$signatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
        $newStatus = $_POST['status'];
        $stmt = $conn->prepare("INSERT INTO contract_status (contract_id, status) VALUES (:contract_id, :status)");
            $stmt->bindParam(':contract_id', $contract_id);
            $stmt->bindParam(':status', $newStatus);
           if($stmt->execute()){
              //log action for audit
              $stmt = $conn->prepare("INSERT INTO contract_audit_trail (contract_id, user_id, action) VALUES (:contract_id, :user_id, :action)");
             $stmt->bindParam(':contract_id', $contract_id);
             $stmt->bindParam(':user_id', $_SESSION['user_id']);
              $stmt->bindParam(':action', 'Contract status changed to '. $newStatus);
           $stmt->execute();

              header("Location: view_contract.php?id=$contract_id&success=true");
              exit();
           }else {
               $error = "Error updating status.";
           }
     }
    if(isset($_GET['success']) && $_GET['success'] == 'true'){
           $success = "Contract updated successfully!";
      }

$stmt = $conn->prepare("SELECT status FROM contract_status WHERE contract_id = :contract_id ORDER BY status_changed_at DESC LIMIT 1");
$stmt->bindParam(':contract_id', $contract_id);
$stmt->execute();
$status = $stmt->fetch(PDO::FETCH_ASSOC)['status'];

// Fetch audit Trail
$stmt = $conn->prepare("SELECT contract_audit_trail.*, users.username FROM contract_audit_trail LEFT JOIN users ON contract_audit_trail.user_id = users.id WHERE contract_id = :contract_id ORDER BY created_at DESC");
$stmt->bindParam(':contract_id', $contract_id);
$stmt->execute();
$audit_trail = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Add comment handling
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
        $comment = trim($_POST['comment']);
        if (!empty($comment)) {
        //log action for audit
          $stmt = $conn->prepare("INSERT INTO contract_audit_trail (contract_id, user_id, action, details) VALUES (:contract_id, :user_id, 'Added comment', :comment)");
          $stmt->bindParam(':contract_id', $contract_id);
          $stmt->bindParam(':user_id', $_SESSION['user_id']);
           $stmt->bindParam(':comment', $comment);
        $stmt->execute();

           $success = "Comment added successfully!";
            header("Location: view_contract.php?id=$contract_id&success=true");
            exit();

        } else {
            $error = "Comment cannot be empty";
        }
    }

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Contract Details: <?php echo htmlspecialchars($contract['subject']); ?></h1>
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
  
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                     <h2 class="text-xl font-bold text-gray-800 mb-4"> Contract Information </h2>
                            <p class="text-gray-700 mb-2"><strong>Assigned to:</strong> <?php echo htmlspecialchars($project_customer_name); ?></p>
                            <p class="text-gray-700 mb-2"><strong>Contract Value:</strong> $<?php echo htmlspecialchars($contract['contract_value']); ?></p>
                            <p class="text-gray-700 mb-2"><strong>Contract Type:</strong> <?php echo htmlspecialchars($contract['contract_type'] ? $contract['contract_type'] : "N/A"); ?></p>
                             <p class="text-gray-700 mb-2"><strong>Start Date:</strong> <?php echo htmlspecialchars($contract['start_date']); ?></p>
                             <?php if ($contract['end_date']): ?>
                                   <p class="text-gray-700 mb-2"><strong>End Date:</strong> <?php echo htmlspecialchars($contract['end_date']); ?></p>
                             <?php endif; ?>
                              <p class="text-gray-700 mb-2"><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
                 </div>
                   <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Description</h2>
                      <p class="text-gray-700 mb-2">
                         <?php echo nl2br(htmlspecialchars($contract['description'] ? $contract['description']: 'N/A')); ?>
                     </p>
                    <?php if($_SESSION['role'] === 'admin') : ?>
                      
                         <div class="mt-6">
                                  <form method="post" action="">
                                      <label for="status" class="block text-gray-700">Set Status:</label>
                                       <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                                           <option value="Draft" <?php if ($status == 'Draft') echo 'selected' ?>>Draft</option>
                                           <option value="Sent" <?php if ($status == 'Sent') echo 'selected' ?>>Sent</option>
                                             <option value="Active" <?php if ($status == 'Active') echo 'selected' ?>>Active</option>
                                              <option value="Expired" <?php if ($status == 'Expired') echo 'selected' ?>>Expired</option>
                                               <option value="Canceled" <?php if ($status == 'Canceled') echo 'selected' ?>>Canceled</option>
                                       </select>
                                     <button type="submit" name="toggle_status" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4"> Update Status </button>
                                  </form>
                         </div>
                    <?php endif; ?>
                   </div>
             </div>
       </div>
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
         <h2 class="text-xl font-bold text-gray-800 mb-4">Contract Content</h2>
         <div class="contract-content border border-gray-200 p-4 rounded-lg">
               <?php echo $contract['contract_text']; ?>
         </div>
   </div>
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Signature Panel</h2>
           <?php if($signatures): ?>
                 <?php foreach($signatures as $signature): ?>
                   <div class="mb-4 border border-gray-200 p-4 rounded-lg">
                      <h3 class="text-md font-bold text-gray-800 mb-2">
                             <i class="fas fa-user-signature mr-1"></i> <?php echo htmlspecialchars($signature['username']); ?>
                        </h3>
                           <?php if($signature['signature_data']): ?>
                                 <div class="text-center">
                                     <img src="<?php echo $signature['signature_data']; ?>" alt="User Signature" class="max-w-sm mx-auto"/>
                                 </div>
                             <?php else: ?>
                                  <p>No signature added.</p>
                              <?php endif; ?>
                            <p class="text-right text-gray-600 text-sm mt-2">
                                <?php echo htmlspecialchars(date('Y-m-d h:i A', strtotime($signature['signed_at']))); ?>
                           </p>
                   </div>
                 <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-600">No Signatures Yet.</p>
            <?php endif; ?>

            <!-- Signature form to take the signature  -->
             <?php if ($status === 'Sent' ||  $status === 'Active') : ?>
                <div class="mt-6">
                     <a href="sign_contract.php?id=<?php echo $contract['id']; ?>"  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Digital Signature</a>
                  </div>
           <?php endif; ?>
       </div>
         <?php if($audit_trail): ?>
             <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                 <h2 class="text-xl font-bold text-gray-800 mb-4">Audit Trail</h2>
                   <ul>
                        <?php foreach ($audit_trail as $log): ?>
                           <li class="mb-2">
                             <p class="text-gray-700">
                                  <strong><?php echo htmlspecialchars($log['username'] ? $log['username'] : 'System') ?>: </strong>
                                     <?php echo htmlspecialchars($log['action']); ?>
                               </p>
                              <div class="pl-8 text-sm">
                                   <?php if ($log['ip_address']) :?>
                                      <p><strong>IP:</strong> <?php echo htmlspecialchars($log['ip_address']); ?> </p>
                                      <?php endif; ?>
                                   <?php if ($log['geolocation_data']) :
                                             $geo_data = json_decode($log['geolocation_data'], true);
                                                if($geo_data): ?>
                                               <p> <strong>Location:</strong> <?php echo htmlspecialchars($geo_data['city']) . ", " . htmlspecialchars($geo_data['region']). ", " . htmlspecialchars($geo_data['country']); ?> </p>
                                       <?php endif; ?>
                                   <?php endif; ?>
                                      <?php if ($log['timezone']) :?>
                                         <p><strong>Timezone:</strong> <?php echo htmlspecialchars($log['timezone']); ?> </p>
                                     <?php endif; ?>
                                    <?php if ($log['device_info']) :
                                        $device_info = json_decode($log['device_info'], true);
                                           if($device_info): ?>
                                                 <p><strong>Device:</strong> <?php echo htmlspecialchars($device_info['browser']); ?> - <?php echo htmlspecialchars($device_info['os']); ?></p>
                                            <?php endif; ?>
                                    <?php endif; ?>
                                     <?php if(!empty($log['details'])): ?>
                                       <blockquote class="bg-gray-50 p-2 border-l-4 border-gray-200  text-sm"><?php echo nl2br(htmlspecialchars($log['details'])); ?></blockquote>
                                    <?php endif; ?>
                                   <p class="text-gray-500">  <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($log['created_at']))); ?></p>
                              </div>
                           </li>
                      <?php endforeach; ?>
                    <?php else: ?>
                         <p class="text-gray-600">No audit logs.</p>
                   <?php endif; ?>
            </ul>
    </div>
        <div class="mt-4 flex justify-center">
          <a href="manage_contracts.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Contracts</a>
            <a href="edit_contract.php?id=<?php echo $contract['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Edit Contract</a>
        </div>
 </div>
  <style>
       .contract-content * {
          word-break: break-word;
          font-size: 16px;
          line-height: 1.7;
      }
        .contract-content h1 {font-size: 2.5rem; }
      .contract-content h2{
        font-size: 2rem;
        }
      .contract-content h3{
         font-size: 1.75rem
       }
        .contract-content h4{
           font-size: 1.5rem;
        }
         .contract-content h5{
             font-size: 1.25rem;
             }
            .contract-content h6{
                font-size: 1.1rem;
            }
       .contract-content table { border-collapse: collapse; width: 100%; margin-bottom: 15px;}
       .contract-content table, .contract-content th, .contract-content td {border: 1px solid #ddd; padding: 8px;}
      .contract-content blockquote {
         margin: 20px 0;
        padding: 15px 20px;
          border-left: 4px solid #c0c0c0;
          font-style: italic;
            color: #555;
            background-color: #fafafa;
      }
  </style>

<?php
// Include footer
require 'footer.php';
?>