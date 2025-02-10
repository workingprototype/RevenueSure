<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: " . BASE_URL . "support_tickets/view");
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
       header("Location: " . BASE_URL . "support_tickets/view?id=$ticket_id");
    exit();
} else {
    echo "<script>alert('Error updating task status.');</script>";
       header("Location: " . BASE_URL . "support_tickets/view?id=$ticket_id");
        exit();
}
?>