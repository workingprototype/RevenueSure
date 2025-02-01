<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch counts for Dashboard
$stmt = $conn->prepare("SELECT COUNT(*) as total_draft FROM contract_status WHERE status = 'Draft'");
$stmt->execute();
$draft_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_draft'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_sent FROM contract_status WHERE status = 'Sent'");
$stmt->execute();
$sent_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_sent'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_signed FROM contract_status WHERE status = 'Active'");
$stmt->execute();
$signed_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_signed'];

$stmt = $conn->prepare("SELECT COUNT(*) as total_expired FROM contract_status WHERE status = 'Expired'");
$stmt->execute();
$expired_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_expired'];

// Fetch all contracts with project names and customer names and latest status
$query = "SELECT contracts.*, projects.name as project_name, customers.name as customer_name,
            (SELECT status FROM contract_status WHERE contract_id = contracts.id ORDER BY status_changed_at DESC LIMIT 1) as status
          FROM contracts
          LEFT JOIN projects ON contracts.project_id = projects.id
            LEFT JOIN customers ON contracts.customer_id = customers.id

          ORDER BY contracts.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Contracts</h1>

  <!-- Dashboard Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
             <!-- Draft Count -->
            <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <h3 class="text-xl font-semibold">Drafts</h3>
                    <p class="text-3xl font-bold"><?php echo htmlspecialchars($draft_count); ?></p>
               </div>
                <i class="fas fa-file-alt text-4xl opacity-70"></i>
           </div>
            <!-- Sent Contracts-->
            <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                <div class="flex flex-col gap-1">
                     <h3 class="text-xl font-semibold">Sent Contracts</h3>
                        <p class="text-3xl font-bold"><?php echo htmlspecialchars($sent_count); ?></p>
                </div>
               <i class="fas fa-paper-plane text-4xl opacity-70"></i>
          </div>
             <!-- Signed Contracts Card -->
              <div class="bg-gradient-to-r from-purple-400 to-purple-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                 <div class="flex flex-col gap-1">
                    <h3 class="text-xl font-semibold">Active/Enforced</h3>
                      <p class="text-3xl font-bold"><?php echo htmlspecialchars($signed_count); ?></p>
                 </div>
                  <i class="fas fa-file-signature text-4xl opacity-70"></i>
            </div>
             <!-- Expired Contracts Card -->
             <div class="bg-gradient-to-r from-red-400 to-red-600 text-white p-6 rounded-2xl shadow-xl flex items-center justify-between">
                 <div class="flex flex-col gap-1">
                        <h3 class="text-xl font-semibold">Expired Contracts</h3>
                        <p class="text-3xl font-bold"><?php echo htmlspecialchars($expired_count); ?></p>
                     </div>
                    <i class="fas fa-times-circle text-4xl opacity-70"></i>
               </div>
     </div>
    <div class="mb-8 flex justify-between items-center">
       <a href="add_contract.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide"><i class="fas fa-plus-circle mr-2"></i> Add Contract</a>
      </div>
    <!-- Contracts Table -->
      <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="w-full text-left">
                <thead>
                   <tr>
                      <th class="px-4 py-2">Subject</th>
                     <th class="px-4 py-2">Assigned To</th>
                    <th class="px-4 py-2">Value</th>
                      <th class="px-4 py-2">Type</th>
                         <th class="px-4 py-2">Status</th>
                       <th class="px-4 py-2">Created At</th>
                       <th class="px-4 py-2">Actions</th>
                  </tr>
             </thead>
          <tbody>
            <?php if ($contracts): ?>
              <?php foreach ($contracts as $contract): ?>
                  <tr class="border-b">
                       <td class="px-4 py-2"><?php echo htmlspecialchars($contract['subject']); ?></td>
                      <td class="px-4 py-2">
                             <?php
                               if($contract['project_name']){
                                   echo htmlspecialchars($contract['project_name']);
                                   } else if($contract['customer_name']){
                                     echo htmlspecialchars($contract['customer_name']);
                                      } else {
                                          echo 'N/A';
                                      }
                                 ?>
                            </td>
                       <td class="px-4 py-2">$<?php echo htmlspecialchars($contract['contract_value']); ?></td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($contract['contract_type'] ? $contract['contract_type']: "N/A"); ?></td>
                          <td class="px-4 py-2">
                             <span class="px-2 py-1 rounded-full <?php
                                    switch ($contract['status']) {
                                        case 'Draft':
                                            echo 'bg-gray-200 text-gray-800';
                                            break;
                                       case 'Sent':
                                            echo 'bg-blue-200 text-blue-800';
                                             break;
                                        case 'Active':
                                            echo 'bg-green-200 text-green-800';
                                           break;
                                          case 'Expired':
                                             echo 'bg-red-200 text-red-800';
                                                break;
                                                case 'Canceled':
                                                  echo 'bg-red-200 text-red-800';
                                                 break;
                                         default:
                                             echo 'bg-gray-100 text-gray-800';
                                            break;
                                      }
                             ?>"><?php echo htmlspecialchars($contract['status']); ?>
                           </span>
                        </td>
                       <td class="px-4 py-2"><?php echo htmlspecialchars($contract['created_at']); ?></td>
                       <td class="px-4 py-2 flex gap-2">
                         <a href="view_contract.php?id=<?php echo $contract['id']; ?>" class="text-purple-600 hover:underline">View</a>
                           <a href="edit_contract.php?id=<?php echo $contract['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <button onclick="confirmDelete(<?php echo $contract['id']; ?>)" class="text-red-600 hover:underline ml-2">Delete</button>
                           </td>
                    </tr>
                <?php endforeach; ?>
              <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-2 text-center text-gray-600">No contracts found.</td>
                        </tr>
                  <?php endif; ?>
             </tbody>
          </table>
      </div>
</div>
<script>
    function confirmDelete(contractId) {
        if (confirm('Are you sure you want to delete this contract?')) {
            window.location.href = 'delete_contract.php?id=' + contractId;
        }
    }
</script>
<?php
// Include footer
require 'footer.php';
?>