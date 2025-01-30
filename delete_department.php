<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_departments.php");
    exit();
}

$department_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM team_departments WHERE id = :id");
$stmt->bindParam(':id', $department_id);

if ($stmt->execute()) {
    header("Location: manage_departments.php");
    exit();
} else {
    echo "<script>alert('Error deleting department.');</script>";
    header("Location: manage_departments.php");
    exit();
}
?>