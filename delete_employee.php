<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($employee_id) {
    // Delete employee
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = :id");
    $stmt->bindParam(':id', $employee_id);
    $stmt->execute();
}

header("Location: manage_employees.php");
exit();
?>