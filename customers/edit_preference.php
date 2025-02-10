<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';


$preference_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

// Fetch preference details
$stmt = $conn->prepare("SELECT * FROM customer_preferences WHERE id = :id");
$stmt->bindParam(':id', $preference_id);
$stmt->execute();
$preference = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$preference) {
    header("Location: " . BASE_URL . "customers/view?id=$customer_id");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updated_preference = trim($_POST['preference']);
     if (empty($updated_preference)) {
        $error = "Preference cannot be empty.";
    }else {
       $stmt = $conn->prepare("UPDATE customer_preferences SET preference = :preference WHERE id = :id");
       $stmt->bindParam(':preference', $updated_preference);
      $stmt->bindParam(':id', $preference_id);
        if($stmt->execute()) {
            $success = "Preference updated successfully!";
            header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
             exit();
        }else {
             $error = "Error updating preference.";
         }
     }
}

?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Preference</h1>
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
    <?php echo csrfTokenInput(); ?>
        <div class="mb-4">
            <label for="preference" class="block text-gray-700">Preference</label>
            <input type="text" name="preference" id="preference" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($preference['preference']); ?>" required>
          </div>
         <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Preference</button>
         <div class="mt-4">
             <a href="<?php echo BASE_URL; ?>customers/view?id=<?php echo $customer_id; ?>"  class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back To Customer</a>
         </div>
     </form>
</div>