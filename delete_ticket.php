<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_tickets.php");
    exit();
}

$ticket_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM support_tickets WHERE id = :id");
$stmt->bindParam(':id', $ticket_id);

if ($stmt->execute()) {
    header("Location: manage_tickets.php");
    exit();
} else {
    echo "<script>alert('Error deleting ticket.');</script>";
    header("Location: manage_tickets.php");
        exit();
}
?>