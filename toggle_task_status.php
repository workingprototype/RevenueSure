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

// Fetch task details to get lead_id or project_id
$stmt = $conn->prepare("SELECT lead_id, project_id FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $task_id);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

$lead_id = $task ? $task['lead_id'] : null;
 $project_id = $task ? $task['project_id'] : null;

    // Check if there are dependencies, fetch if any.
    $stmt = $conn->prepare("SELECT depends_on_task_id FROM task_dependencies WHERE task_id = :task_id");
    $stmt->bindParam(':task_id', $task_id);
    $stmt->execute();
   $dependencies = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($status === 'In Progress' && !empty($dependencies)) {
        $status = 'In Progress';
            foreach($dependencies as $dependency_id){
                $stmt = $conn->prepare("SELECT status FROM tasks WHERE id = :id");
                $stmt->bindParam(':id', $dependency_id);
                $stmt->execute();
                $dependent_task_status = $stmt->fetch(PDO::FETCH_ASSOC)['status'];
                if ($dependent_task_status !== 'Completed') {
                   echo "<script>alert('Cannot start this task. Required task #{$dependency_id} is not completed.');";
                         if($lead_id){
                            echo"window.location.href='view_tasks.php?lead_id=$lead_id';";
                         } else if ($project_id){
                            echo "window.location.href='view_tasks.php?project_id=$project_id';";
                          } else {
                            echo"window.location.href='view_tasks.php';";
                       }
                   echo"</script>";
                    exit;
                 }

            }
    }


$stmt = $conn->prepare("UPDATE tasks SET status = :status WHERE id = :id");
$stmt->bindParam(':status', $status);
$stmt->bindParam(':id', $task_id);


if ($stmt->execute()) {
    if($lead_id){
        header("Location: view_tasks.php?lead_id=$lead_id");
      }else if($project_id){
         header("Location: view_tasks.php?project_id=$project_id");
        }else {
         header("Location: view_tasks.php");
       }
   exit();
} else {
    echo "<script>alert('Error updating task status.');</script>";
     if($lead_id){
         header("Location: view_tasks.php?lead_id=$lead_id");
      }else if($project_id){
         header("Location: view_tasks.php?project_id=$project_id");
        }else {
           header("Location: view_tasks.php");
       }
    exit();
}
?>