<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['completed'])) {
    header("Location: dashboard.php");
    exit();
}

$todo_id = $_GET['id'];
$is_completed = $_GET['completed'];

$stmt = $conn->prepare("UPDATE todos SET is_completed = :is_completed WHERE id = :id");
$stmt->bindParam(':is_completed', $is_completed);
$stmt->bindParam(':id', $todo_id);

if ($stmt->execute()) {
      header("Location: dashboard.php");
    exit();
} else {
     echo "<script>alert('Error marking todo.');</script>";
      header("Location: dashboard.php");
    exit();
}
?>