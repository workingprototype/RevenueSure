<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];


// Fetch existing settings
$stmt = $conn->prepare("SELECT * FROM invoice_settings WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $_POST['company_name'] ?? '';
     $company_logo = isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === 0 ? $_FILES['company_logo'] : null;
    $company_tagline = $_POST['company_tagline'] ?? '';
    $company_address_line1 = $_POST['company_address_line1'] ?? '';
     $company_address_line2 = $_POST['company_address_line2'] ?? '';
    $company_phone_number = $_POST['company_phone_number'] ?? '';
    $overdue_charge_type = $_POST['overdue_charge_type'] ?? null;
    $overdue_charge_amount = $_POST['overdue_charge_amount'] ?? null;
    $overdue_charge_period = $_POST['overdue_charge_period'] ?? null;
    $thank_you_message = $_POST['thank_you_message'] ?? '';

       $file_path = $settings['company_logo'] ?? null;
          if($company_logo){
             $file_name = basename($company_logo['name']);
            $file_tmp = $company_logo['tmp_name'];
              $file_path = "public/uploads/logo/" . uniqid() . "_" . $file_name;
            if (!is_dir('public/uploads/logo')) {
                    mkdir('public/uploads/logo', 0777, true);
                }
               if (!move_uploaded_file($file_tmp, $file_path)) {
                   $file_path = $settings['company_logo'] ?? null;
               }
        }
        if ($settings) {
           // Update existing settings
            $stmt = $conn->prepare("UPDATE invoice_settings SET company_name = :company_name, company_logo = :company_logo, company_tagline = :company_tagline, company_address_line1 = :company_address_line1, company_address_line2 = :company_address_line2, company_phone_number = :company_phone_number, overdue_charge_type = :overdue_charge_type, overdue_charge_amount = :overdue_charge_amount, overdue_charge_period = :overdue_charge_period, thank_you_message = :thank_you_message WHERE user_id = :user_id");
        } else {
           // Insert new settings
            $stmt = $conn->prepare("INSERT INTO invoice_settings (company_name, company_logo, company_tagline, company_address_line1, company_address_line2, company_phone_number, overdue_charge_type, overdue_charge_amount, overdue_charge_period,  thank_you_message, user_id) VALUES (:company_name, :company_logo, :company_tagline, :company_address_line1, :company_address_line2, :company_phone_number, :overdue_charge_type, :overdue_charge_amount, :overdue_charge_period, :thank_you_message, :user_id)");
            $stmt->bindParam(':user_id', $user_id);
        }
         $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':company_logo', $file_path);
        $stmt->bindParam(':company_tagline', $company_tagline);
         $stmt->bindParam(':company_address_line1', $company_address_line1);
        $stmt->bindParam(':company_address_line2', $company_address_line2);
        $stmt->bindParam(':company_phone_number', $company_phone_number);
        $stmt->bindParam(':overdue_charge_type', $overdue_charge_type);
         $stmt->bindParam(':overdue_charge_amount', $overdue_charge_amount);
        $stmt->bindParam(':overdue_charge_period', $overdue_charge_period);
        $stmt->bindParam(':thank_you_message', $thank_you_message);

        if ($stmt->execute()) {
            $success = "Invoice settings saved successfully!";
            header("Location: " . BASE_URL . "settings/invoice?success=true");
            exit();
        } else {
            $error = "Error saving invoice settings.";
        }
}


?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Invoice Settings</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            Invoice settings saved successfully!
        </div>
    <?php endif; ?>

<form method="POST" action="" class="bg-white p-6 rounded-lg shadow-md"  enctype="multipart/form-data">
<?php echo csrfTokenInput(); ?>
      <div class="mb-4">
        <label for="company_name" class="block text-gray-700">Company Name</label>
          <input type="text" name="company_name" id="company_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" >
       </div>
     <div class="mb-4">
         <label for="company_logo" class="block text-gray-700">Company Logo</label>
          <input type="file" name="company_logo" id="company_logo" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
           <?php if($settings['company_logo']): ?>
              <img src="<?php echo $settings['company_logo']; ?>" alt="Company Logo" class="mt-2 w-32 h-auto object-cover">
           <?php endif; ?>
    </div>
    <div class="mb-4">
        <label for="company_tagline" class="block text-gray-700">Company Tagline</label>
         <input type="text" name="company_tagline" id="company_tagline" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['company_tagline'] ?? ''); ?>">
    </div>
    <div class="mb-4">
        <label for="company_address_line1" class="block text-gray-700">Address Line 1</label>
       <input type="text" name="company_address_line1" id="company_address_line1" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['company_address_line1'] ?? ''); ?>">
    </div>
       <div class="mb-4">
        <label for="company_address_line2" class="block text-gray-700">Address Line 2</label>
       <input type="text" name="company_address_line2" id="company_address_line2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['company_address_line2'] ?? ''); ?>">
    </div>
    <div class="mb-4">
        <label for="company_phone_number" class="block text-gray-700">Phone Number</label>
        <input type="text" name="company_phone_number" id="company_phone_number" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['company_phone_number'] ?? ''); ?>">
    </div>
      <div class="mb-4">
         <label class="block text-gray-700">Overdue Charges</label>
            <div class="mb-2">
               <label for="overdue_charge_type" class="block text-gray-700">Charge Type</label>
                <select name="overdue_charge_type" id="overdue_charge_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                     <option value="">Select</option>
                    <option value="percentage" <?php if($settings['overdue_charge_type'] == 'percentage') echo 'selected' ?> >Percentage</option>
                   <option value="fixed" <?php if($settings['overdue_charge_type'] == 'fixed') echo 'selected' ?>>Fixed</option>
               </select>
            </div>
              <div class="mb-2">
                 <label for="overdue_charge_amount" class="block text-gray-700">Amount</label>
                  <input type="number" name="overdue_charge_amount" id="overdue_charge_amount" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['overdue_charge_amount'] ?? ''); ?>" >
               </div>
             <div class="mb-2">
               <label for="overdue_charge_period" class="block text-gray-700">Charge Period</label>
                 <select name="overdue_charge_period" id="overdue_charge_period" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">Select</option>
                    <option value="monthly" <?php if($settings['overdue_charge_period'] == 'monthly') echo 'selected' ?>>Monthly</option>
                    <option value="daily" <?php if($settings['overdue_charge_period'] == 'daily') echo 'selected' ?>>Daily</option>
                       <option value="days" <?php if($settings['overdue_charge_period'] == 'days') echo 'selected' ?>>Days</option>
                </select>
              </div>
        </div>
    <div class="mb-4">
        <label for="thank_you_message" class="block text-gray-700">Custom Thank You Message</label>
         <textarea name="thank_you_message" id="thank_you_message" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"><?php echo htmlspecialchars($settings['thank_you_message'] ?? ''); ?></textarea>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Save Settings</button>
</form>

