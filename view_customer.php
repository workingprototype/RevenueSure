<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = :id");
$stmt->bindParam(':id', $customer_id);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: manage_customers.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_company_preferences'])) {
         $company = trim($_POST['company']);
          $preferences = trim($_POST['preferences']);
         $stmt = $conn->prepare("UPDATE customers SET company = :company, preferences = :preferences WHERE id = :id");
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':preferences', $preferences);
        $stmt->bindParam(':id', $customer_id);

        if($stmt->execute()){
           $success = "Customer details updated successfully!";
            header("Location: view_customer.php?id=$customer_id&success=true"); // Redirect back to profile page
             exit();
        }else {
           $error =  "There was an error updating customer info.";
         }
     } elseif (isset($_POST['add_interaction'])) {
           $stmt = $conn->prepare("UPDATE customers SET last_interaction = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $customer_id);
            if ($stmt->execute()) {
                $success = "Interaction updated successfully!";
                  header("Location: view_customer.php?id=$customer_id&success=true"); // Redirect back to profile page
                 exit();
            } else {
                 $error = "There was an error updating last interaction.";
            }
    }
 }

if(isset($_GET['success']) && $_GET['success'] == 'true'){
       $success = "Customer details updated successfully!";
  }

// Include header
require 'header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Customer Details: <?php echo htmlspecialchars($customer['name']); ?></h1>
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
        <h2 class="text-xl font-bold text-gray-800 mb-4">Contact Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
    </div>
    <form method="post" action="">
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Company Profile</h2>
         <div class="mb-4">
             <label for="company" class="block text-gray-700">Company</label>
           <input type="text" name="company" id="company" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($customer['company'] ?? ''); ?>">
         </div>
    </div>

     <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Preferences</h2>
         <div class="mb-4">
          <textarea name="preferences" id="preferences" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($customer['preferences'] ?? ''); ?></textarea>
        </div>
    </div>
       <button type="submit" name="update_company_preferences" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-4">Update Company/Preferences</button>
    </form>
     <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Past Interactions</h2>
        <?php if($customer['last_interaction']): ?>
            <p><strong>Last Interaction:</strong><?php echo date('Y-m-d H:i', strtotime($customer['last_interaction'])); ?> </p>
         <?php else: ?>
          <p>No Interactions</p>
           <?php endif; ?>
            <form method="post" action="">
                <button type="submit" name="add_interaction" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-2">Update Interaction</button>
           </form>
    </div>
    <div class="mb-4">
        <a href="manage_customers.php"  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Back To Customers</a>
   </div>

<?php
// Include footer
require 'footer.php';
?>