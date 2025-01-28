<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['customer_id'])) {
    header("Location: manage_customers.php");
    exit();
}

$preference_id = $_GET['id'];
$customer_id = $_GET['customer_id'];


$stmt = $conn->prepare("DELETE FROM customer_preferences WHERE id = :id");
$stmt->bindParam(':id', $preference_id);

if ($stmt->execute()) {
    header("Location: view_customer.php?id=$customer_id");
    exit();
} else {
    echo "<script>alert('Error deleting preference.');</script>";
}
?>