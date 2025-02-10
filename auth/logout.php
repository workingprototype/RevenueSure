<?php
require_once ROOT_PATH . 'helper/core.php';
session_start();
session_destroy();
header("Location: " . BASE_URL . "auth/login");
exit();
?>