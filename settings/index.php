<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

?>
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Settings</h1>
  <div class="flex justify-center">
      <ul class="text-xl">
      <li class="mb-2"> <a href="<?php echo BASE_URL; ?>settings/invoice/index" class="text-blue-600 hover:underline">Invoice Settings</a></li>
       </ul>
       <ul class="text-xl">
      <li class="mb-2"> <a href="<?php echo BASE_URL; ?>mail/settings" class="text-blue-600 hover:underline">E-mail Settings</a></li>
       </ul>
       
  </div>

