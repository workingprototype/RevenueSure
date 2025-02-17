<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'helper/cache.php'; // Include cache functions

session_start();

// Clear user-specific cache
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    clearCache('header_user_' . $userId);
    clearCache('footer_user_' . $userId);
}

session_destroy();
header("Location: " . BASE_URL . "auth/login");
exit();
?>