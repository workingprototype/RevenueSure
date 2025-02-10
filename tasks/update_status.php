<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

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