<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include header
require 'header.php';
?>
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Settings</h1>
  <div class="flex justify-center">
      <ul class="text-xl">
         <li class="mb-2"> <a href="invoice_settings.php" class="text-blue-600 hover:underline">Invoice Settings</a></li>
       </ul>
  </div>

<?php
// Include footer
require 'footer.php';
?>