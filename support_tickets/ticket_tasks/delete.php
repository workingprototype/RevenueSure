<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "support_tickets/view");
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
   header("Location: " . BASE_URL . "support_tickets/view?id=$ticket_id");
     exit();
} else {
    echo "<script>alert('Error deleting task.');</script>";
     header("Location: " . BASE_URL . "support_tickets/view?id=$ticket_id");
     exit();
}
?>