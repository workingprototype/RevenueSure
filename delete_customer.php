<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_customers.php");
    exit();
}

$customer_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
$stmt->bindParam(':id', $customer_id);

if ($stmt->execute()) {
    header("Location: manage_customers.php");
    exit();
} else {
    echo "<script>alert('Error deleting customer.');</script>";
}
?>