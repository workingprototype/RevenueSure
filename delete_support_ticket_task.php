<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_ticket.php");
    exit();
}

$task_id = $_GET['id'];

// Fetch ticket id first
$stmt = $conn->prepare("SELECT ticket_id FROM support_ticket_tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);
if ($task){
      $ticket_id = $task['ticket_id'];
}

$stmt = $conn->prepare("DELETE FROM support_ticket_tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);

if ($stmt->execute()) {
   header("Location: view_ticket.php?id=$ticket_id");
     exit();
} else {
    echo "<script>alert('Error deleting task.');</script>";
     header("Location: view_ticket.php?id=$ticket_id");
     exit();
}
?>