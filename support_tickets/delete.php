<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "support_tickets/manage");
    exit();
}

$ticket_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM support_tickets WHERE id = :id");
$stmt->bindParam(':id', $ticket_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "support_tickets/manage");
    exit();
} else {
    echo "<script>alert('Error deleting ticket.');</script>";
    header("Location: " . BASE_URL . "support_tickets/manage");
        exit();
}
?>