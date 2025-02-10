<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "departments/manage");
    exit();
}

$department_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM team_departments WHERE id = :id");
$stmt->bindParam(':id', $department_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "departments/manage");
    exit();
} else {
    echo "<script>alert('Error deleting department.');</script>";
    header("Location: " . BASE_URL . "departments/manage");
    exit();
}
?>