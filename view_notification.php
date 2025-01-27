<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$notification_id = $_GET['id'];

// Mark the notification as read
$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
$stmt->bindParam(':id', $notification_id);
$stmt->execute();

// Fetch the notification details
$stmt = $conn->prepare("SELECT * FROM notifications WHERE id = :id");
$stmt->bindParam(':id', $notification_id);
$stmt->execute();
$notification = $stmt->fetch(PDO::FETCH_ASSOC);

if ($notification) {
    // Redirect based on the notification type
    switch ($notification['type']) {
        case 'task_reminder':
            header("Location: view_tasks.php?lead_id={$notification['related_id']}");
            break;
        default:
            header("Location: dashboard.php");
            break;
    }
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}