<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task details to get lead_id
$stmt = $conn->prepare("SELECT lead_id FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($task) {
    $lead_id = $task['lead_id'];

    // Delete the task
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $task_id);

    if ($stmt->execute()) {
        header("Location: view_tasks.php?lead_id=$lead_id");
        exit();
    } else {
        echo "<script>alert('Error deleting task.');</script>";
    }
} else {
    header("Location: view_tasks.php");
    exit();
}
?>