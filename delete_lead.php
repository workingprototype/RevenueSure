<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$lead_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM leads WHERE id = :id");
$stmt->bindParam(':id', $lead_id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    echo "<script>alert('Error deleting lead.');</script>";
}
?>