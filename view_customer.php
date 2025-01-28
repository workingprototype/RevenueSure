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
    if (isset($_POST['update_company'])) {
         $company = trim($_POST['company']);

         $stmt = $conn->prepare("UPDATE customers SET company = :company WHERE id = :id");
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':id', $customer_id);

         if($stmt->execute()){
           $success = "Company details updated successfully!";
            header("Location: view_customer.php?id=$customer_id&success=true");
             exit();
        }else {
           $error =  "There was an error updating customer info.";
         }
     }elseif (isset($_POST['add_preference'])) {
        $preference = trim($_POST['preference']);

         if(!empty($preference)){
             $stmt = $conn->prepare("INSERT INTO customer_preferences (customer_id, preference) VALUES (:customer_id, :preference)");
            $stmt->bindParam(':customer_id', $customer_id);
             $stmt->bindParam(':preference', $preference);
             if ($stmt->execute()) {
                $success = "Preference added successfully!";
                    header("Location: view_customer.php?id=$customer_id&success=true");
                     exit();
                } else {
                      $error = "Error adding preference.";
                   }
          } else {
                $error = "Preference cannot be empty!";
         }

    } elseif (isset($_POST['add_interaction'])) {
           $interaction_type = trim($_POST['interaction_type']);
           $details = trim($_POST['details']);

          $stmt = $conn->prepare("INSERT INTO customer_interactions (customer_id, interaction_type, details) VALUES (:customer_id, :interaction_type, :details)");
            $stmt->bindParam(':customer_id', $customer_id);
             $stmt->bindParam(':interaction_type', $interaction_type);
             $stmt->bindParam(':details', $details);
             if ($stmt->execute()) {
                   $success = "Interaction added successfully!";
                    header("Location: view_customer.php?id=$customer_id&success=true");
                    exit();
                } else {
                      $error = "Error adding interaction.";
                  }
    }
}
 if(isset($_GET['success']) && $_GET['success'] == 'true'){
       $success = "Customer details updated successfully!";
  }


// Fetch customer preferences
$stmt = $conn->prepare("SELECT * FROM customer_preferences WHERE customer_id = :customer_id ORDER BY created_at ASC");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$preferences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer interactions
$stmt = $conn->prepare("SELECT * FROM customer_interactions WHERE customer_id = :customer_id ORDER BY interaction_at DESC");
$stmt->bindParam(':customer_id', $customer_id);
$stmt->execute();
$interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
             <ul class="mb-4">
                 <?php if($preferences): ?>
                     <?php foreach ($preferences as $preference): ?>
                            <li class="flex justify-between items-center mb-2">
                                 <?php echo htmlspecialchars($preference['preference']); ?>
                                  <div class="flex gap-2">
                                    <a href="edit_preference.php?id=<?php echo $preference['id']; ?>&customer_id=<?php echo $customer_id; ?>" class="text-blue-600 hover:underline">Edit</a>
                                  <a href="delete_preference.php?id=<?php echo $preference['id']; ?>&customer_id=<?php echo $customer_id; ?>" class="text-red-600 hover:underline">Delete</a>
                                   </div>
                            </li>
                        <?php endforeach; ?>
                   <?php else: ?>
                     <p>No Preferences added.</p>
                  <?php endif; ?>
               </ul>
             <div class="mb-4">
                <input type="text" name="preference" id="preference" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Add Preference">
                </div>
                <button type="submit" name="add_preference" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Preference</button>
        </div>
           <button type="submit" name="update_company_preferences" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-4">Update Company</button>
    </form>
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Past Interactions</h2>
             <ul>
                   <?php if($interactions): ?>
                         <?php foreach ($interactions as $interaction): ?>
                            <li class="mb-4">
                                 <p class="text-gray-600 text-sm">
                                    <strong><?php echo htmlspecialchars($interaction['interaction_type']); ?> on:</strong> <?php echo date('Y-m-d H:i', strtotime($interaction['interaction_at'])); ?>
                                </p>
                                 <p class="text-gray-800"><?php echo htmlspecialchars($interaction['details']); ?></p>
                             </li>
                         <?php endforeach; ?>
                    <?php else: ?>
                      <p class="text-gray-600">No interactions found.</p>
                  <?php endif; ?>
             </ul>
             <form method="POST" action="">
                <div class="mb-4">
                    <label for="interaction_type" class="block text-gray-700">Interaction Type</label>
                        <select name="interaction_type" id="interaction_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                           <option value="Call">Call</option>
                            <option value="Email">Email</option>
                           <option value="Meeting">Meeting</option>
                           <option value="Other">Other</option>
                      </select>
                 </div>
                <div class="mb-4">
                     <label for="details" class="block text-gray-700">Details</label>
                        <textarea name="details" id="details" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                </div>
                  <button type="submit" name="add_interaction" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Interaction</button>
           </form>
    </div>
    <div class="mb-4">
        <a href="manage_customers.php"  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Back To Customers</a>
   </div>

<?php
// Include footer
require 'footer.php';
?>