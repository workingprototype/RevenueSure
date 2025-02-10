<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($employee_id) {
    // Delete employee
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = :id");
    $stmt->bindParam(':id', $employee_id);
    $stmt->execute();
}

header("Location: " . BASE_URL . "employees/manage");
exit();
?>