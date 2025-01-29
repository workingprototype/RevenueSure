<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_invoices.php");
    exit();
}

$invoice_id = $_GET['id'];

// Delete the invoice
$stmt = $conn->prepare("DELETE FROM invoices WHERE id = :id");
$stmt->bindParam(':id', $invoice_id);

if ($stmt->execute()) {
    header("Location: manage_invoices.php");
    exit();
} else {
    echo "<script>alert('Error deleting invoice.');</script>";
}
?>