<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "invoices/manage");
    exit();
}

$invoice_id = $_GET['id'];

// Delete the invoice
$stmt = $conn->prepare("DELETE FROM invoices WHERE id = :id");
$stmt->bindParam(':id', $invoice_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "invoices/manage");
    exit();
} else {
    echo "<script>alert('Error deleting invoice.');</script>";
}
?>