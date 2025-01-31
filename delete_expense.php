<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_expenses.php");
    exit();
}

$expense_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM expenses WHERE id = :id");
$stmt->bindParam(':id', $expense_id);

if ($stmt->execute()) {
    header("Location: manage_expenses.php");
    exit();
} else {
     echo "<script>alert('Error deleting expense record.');</script>";
      header("Location: manage_expenses.php");
         exit();
}
?>