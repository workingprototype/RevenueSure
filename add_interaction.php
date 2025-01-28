<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
$interaction_type = $_POST['interaction_type'];
$details = $_POST['details'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $customer_id) {

  $stmt = $conn->prepare("INSERT INTO customer_interactions (customer_id, interaction_type, details) VALUES (:customer_id, :interaction_type, :details)");
    $stmt->bindParam(':customer_id', $customer_id);
     $stmt->bindParam(':interaction_type', $interaction_type);
     $stmt->bindParam(':details', $details);
    if ($stmt->execute()) {
        header("Location: view_customer.php?id=$customer_id&success=true");
        exit();
    } else {
        echo "<script>alert('Error adding interaction.');</script>";
    }

} else {
    header("Location: view_customer.php?id=$customer_id");
    exit();
}
?>