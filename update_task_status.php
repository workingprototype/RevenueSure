<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    if ($task_id && !empty($status)) {
      $stmt = $conn->prepare("UPDATE tasks SET status = :status WHERE id = :id");
       $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $task_id);
         if($stmt->execute()){
            echo 'success';
         }
    } else{
        echo 'error';
     }
} else {
    echo 'error';
}
exit;
?>