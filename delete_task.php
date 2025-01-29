<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task details to get lead_id or project_id
$stmt = $conn->prepare("SELECT lead_id, project_id FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($task) {
    $lead_id = $task['lead_id'];
     $project_id = $task['project_id'];

    // Delete the task
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(':id', $task_id);

    if ($stmt->execute()) {
          if ($lead_id) {
               header("Location: view_tasks.php?lead_id=$lead_id");
            } else if($project_id){
                header("Location: view_tasks.php?project_id=$project_id");
            } else {
                header("Location: view_tasks.php");
            }
         exit();
    } else {
        echo "<script>alert('Error deleting task.');</script>";
        if($lead_id){
            header("Location: view_tasks.php?lead_id=$lead_id");
          } else if($project_id){
            header("Location: view_tasks.php?project_id=$project_id");
          }else {
             header("Location: view_tasks.php");
         }
            exit();
    }
} else {
       if($lead_id){
            header("Location: view_tasks.php?lead_id=$lead_id");
          } else if($project_id){
            header("Location: view_tasks.php?project_id=$project_id");
          }else {
             header("Location: view_tasks.php");
         }
      exit();
}
?>