<?php
require 'helper/core.php';
redirectIfUnauthorized(true);
header("Location: " . BASE_URL . "dashboard");
exit;

?>