<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: view_ticket.php");
    exit();
}

$task_id = $_GET['id'];
$status = $_GET['status'];

// Fetch ticket id first
$stmt = $conn->prepare("SELECT ticket_id FROM support_ticket_tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($task){
    $ticket_id = $task['ticket_id'];
}
$stmt = $conn->prepare("UPDATE support_ticket_tasks SET status = :status WHERE id = :id");
$stmt->bindParam(':status', $status);
$stmt->bindParam(':id', $task_id);

if ($stmt->execute()) {
       header("Location: view_ticket.php?id=$ticket_id");
    exit();
} else {
    echo "<script>alert('Error updating task status.');</script>";
       header("Location: view_ticket.php?id=$ticket_id");
        exit();
}
?>