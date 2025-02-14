<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'mail/includes/email_functions.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';
$test_success = '';

$user_id = $_SESSION['user_id'];

// Function to test certificate authority
function fetchAndInstallCertificate(string $server, string $port): bool
{
    $command = "openssl s_client -showcerts -connect {$server}:{$port} 2>/dev/null | openssl x509 -outform PEM > {$server}.pem";
    $result = shell_exec($command);
    return false; //Always implement a good method rather than allowing self validation of the application
}

// Fetch existing settings
$settings = getUserEmailSettings($conn, $user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_settings'])) {  // Changed from checking $_SERVER['REQUEST_METHOD'] directly

        $imap_server = trim($_POST['imap_server']);
        $imap_port = (int)$_POST['imap_port'];
        $imap_username = trim($_POST['imap_username']);
        $imap_password = trim($_POST['imap_password']);  // Plain text - needs encryption!
        $smtp_server = trim($_POST['smtp_server']);
        $smtp_port = (int)$_POST['smtp_port'];
        $smtp_username = trim($_POST['smtp_username']);
        $smtp_password = trim($_POST['smtp_password']); // Plain text - needs encryption!
        $smtp_security = $_POST['smtp_security'];

        // Basic validation
        if (empty($imap_server) || empty($imap_port) || empty($imap_username) || empty($imap_password) || empty($smtp_server) || empty($smtp_port) || empty($smtp_username) || empty($smtp_password)) {
             $error = "All fields are required.";
         } else {

            //Encrypt passwords!
            $encrypted_imap_password = encrypt($imap_password);
            $encrypted_smtp_password = encrypt($smtp_password);
            $stmt = $conn->prepare("UPDATE users SET imap_server = :imap_server, imap_port = :imap_port, imap_username = :imap_username, imap_password = :imap_password, smtp_server = :smtp_server, smtp_port = :smtp_port, smtp_username = :smtp_username, smtp_password = :smtp_password, smtp_security = :smtp_security WHERE id = :user_id");
            $stmt->bindParam(':imap_server', $imap_server);
            $stmt->bindParam(':imap_port', $imap_port, PDO::PARAM_INT);
            $stmt->bindParam(':imap_username', $imap_username);
            $stmt->bindParam(':imap_password', $encrypted_imap_password);
            $stmt->bindParam(':smtp_server', $smtp_server);
            $stmt->bindParam(':smtp_port', $smtp_port, PDO::PARAM_INT);
             $stmt->bindParam(':smtp_username', $smtp_username);
            $stmt->bindParam(':smtp_password', $encrypted_smtp_password);
            $stmt->bindParam(':smtp_security', $smtp_security);
             $stmt->bindParam(':user_id', $user_id);
           if ($stmt->execute()) {
               $success = "Settings saved successfully! ";
              //Attempt autmatic Certificate setting
                if (IS_DEVELOPMENT) {
                    if ($imap_server && $imap_port) {
                        $cert_result = fetchAndInstallCertificate($imap_server, $imap_port);
                        if ($cert_result) {
                             $success .= "Automatically installed SSL certificate! This function will be improved in the future";
                        } else {
                            $success .= "Could not install SSL";
                        }
                     }
                   }else{
                       $error = "Unable to fetch the SSL, please try again!";
                   }
                   header("Location: " . BASE_URL . "mail/settings?success=true");
                  exit();
                } else {
                     $error = "Error saving settings.";
               }
          }
    }  elseif (isset($_POST['test_settings'])) {
        $test_email = trim($_POST['test_email']);

        if (empty($test_email) || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Valid test email is required.";
        } else {
            // Fetch user's email settings
            $settings = getUserEmailSettings($conn, $user_id); //Ensure we fetch latest creds

            if ($settings) {
                // Send test email using SMTP
                if (smtpSendEmail($settings, $test_email, 'Test Email from RevenueSure', 'This is a test email to verify your SMTP settings.', 'Test email')) {
                    $test_success = "Test email sent successfully! Check your inbox.";
                } else {
                    $error = "Failed to send test email. Check your SMTP settings.";
                }
            } else {
                $error = "Email settings not found. Please configure your email settings first.";
            }
        }
    }
}
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">E-mail Settings</h1>
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
     <?php if ($test_success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
           <?php echo $test_success; ?>
        </div>
    <?php endif; ?>
       <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
          <form method="POST" action="">
          <?php echo csrfTokenInput(); ?>
                <h2 class="text-xl font-bold text-gray-800 mb-4">IMAP & SMTP Settings</h2>
                <div class="mb-4">
                    <label for="imap_server" class="block text-gray-700">IMAP Server</label>
                     <input type="text" name="imap_server" id="imap_server" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['imap_server'] ?? ''); ?>" required>
                  </div>
                <div class="mb-4">
                    <label for="imap_port" class="block text-gray-700">IMAP Port</label>
                     <input type="number" name="imap_port" id="imap_port" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['imap_port'] ?? ''); ?>" required>
                </div>
               <div class="mb-4">
                     <label for="imap_username" class="block text-gray-700">IMAP Username</label>
                    <input type="text" name="imap_username" id="imap_username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['imap_username'] ?? ''); ?>" required>
                </div>
                 <div class="mb-4">
                     <label for="imap_password" class="block text-gray-700">IMAP Password</label>
                       <input type="password" name="imap_password" id="imap_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                 </div>
                  <div class="mb-4">
                        <label for="smtp_server" class="block text-gray-700">SMTP Server</label>
                         <input type="text" name="smtp_server" id="smtp_server" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['smtp_server'] ?? ''); ?>" required>
                   </div>
                   <div class="mb-4">
                      <label for="smtp_port" class="block text-gray-700">SMTP Port</label>
                        <input type="number" name="smtp_port" id="smtp_port" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-4">
                       <label for="smtp_username" class="block text-gray-700">SMTP Username</label>
                         <input type="text" name="smtp_username" id="smtp_username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>" required>
                  </div>
                   <div class="mb-4">
                      <label for="smtp_password" class="block text-gray-700">SMTP Password</label>
                         <input type="password" name="smtp_password" id="smtp_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    </div>
                  <div class="mb-4">
                        <label for="smtp_security" class="block text-gray-700">SMTP Security</label>
                          <select name="smtp_security" id="smtp_security" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                               <option value="tls" <?php if(isset($settings['smtp_security']) && $settings['smtp_security'] == 'tls') echo 'selected'; ?>>TLS</option>
                                 <option value="ssl" <?php if(isset($settings['smtp_security']) && $settings['smtp_security'] == 'ssl') echo 'selected'; ?>>SSL</option>
                                   <option value="none" <?php if(isset($settings['smtp_security']) && $settings['smtp_security'] == 'none') echo 'selected'; ?>>None</option>
                            </select>
                     </div>
                     <button type="submit" name="save_settings" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Save Settings</button>
                </form>
</div>
       <!-- Test Settings Form -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Test SMTP Settings</h2>
         <form method="POST" action="">
         <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="test_email" class="block text-gray-700">Test Email Address</label>
                 <input type="email" name="test_email" id="test_email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
           </div>
            <button type="submit" name="test_settings" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Send Test Email</button>
        </form>
</div>