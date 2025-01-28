<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: view_tasks.php");
    exit();
}

$task_id = $_GET['id'];
$status = $_GET['status'];

// Fetch task details to get lead_id
$stmt = $conn->prepare("SELECT lead_id FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

$lead_id = $task ? $task['lead_id'] : null;


$stmt = $conn->prepare("UPDATE tasks SET status = :status WHERE id = :id");
$stmt->bindParam(':status', $status);
$stmt->bindParam(':id', $task_id);


if ($stmt->execute()) {
   if($lead_id){
         header("Location: view_tasks.php?lead_id=$lead_id");
   }else {
      header("Location: view_tasks.php");
   }
   exit();
} else {
    echo "<script>alert('Error updating task status.');</script>";
    if($lead_id){
         header("Location: view_tasks.php?lead_id=$lead_id");
    }else {
       header("Location: view_tasks.php");
   }
    exit();
}
?>