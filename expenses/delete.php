<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "expenses/manage");
    exit();
}

$expense_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM expenses WHERE id = :id");
$stmt->bindParam(':id', $expense_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "expenses/manage");
    exit();
} else {
     echo "<script>alert('Error deleting expense record.');</script>";
      header("Location: " . BASE_URL . "expenses/manage");
         exit();
}
?>