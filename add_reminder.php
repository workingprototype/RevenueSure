<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$due_date = isset($_GET['due_date']) ? $_GET['due_date'] : null;

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($task && $due_date) {
    // Insert a notification for the user
    $message = "Reminder: Task '{$task['description']}' is due on {$task['due_date']}.";
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, message, related_id, type, created_at) 
        VALUES (:user_id, :message, :related_id, 'task_reminder', :created_at)
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':related_id', $task_id);
      $stmt->bindParam(':created_at', $due_date);
    if ($stmt->execute()) {
         if($task['lead_id']){
            header("Location: view_tasks.php?lead_id={$task['lead_id']}");
          }else if ($task['project_id']){
                header("Location: view_tasks.php?project_id={$task['project_id']}");
          }else {
                header("Location: view_tasks.php");
          }
        exit();
    }else {
         echo "<script>alert('Error setting reminder.');</script>";
           if($task['lead_id']){
            header("Location: view_tasks.php?lead_id={$task['lead_id']}");
          }else if ($task['project_id']){
                header("Location: view_tasks.php?project_id={$task['project_id']}");
          }else {
               header("Location: view_tasks.php");
          }
            exit();
    }
} else {
    if($task['lead_id']){
        header("Location: view_tasks.php?lead_id={$task['lead_id']}");
      } else if($task['project_id']){
        header("Location: view_tasks.php?project_id={$task['project_id']}");
        }else {
            header("Location: view_tasks.php");
       }
      exit();
}