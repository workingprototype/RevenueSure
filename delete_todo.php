<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$todo_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM todos WHERE id = :id");
$stmt->bindParam(':id', $todo_id);

if ($stmt->execute()) {
      header("Location: dashboard.php");
    exit();
} else {
    echo "<script>alert('Error deleting todo.');</script>";
     header("Location: dashboard.php");
    exit();
}
?>