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
            // Check if related id is a project or a lead
            $related_id = $notification['related_id'];
            $stmt = $conn->prepare("SELECT lead_id, project_id FROM tasks WHERE id = :id");
            $stmt->bindParam(':id', $related_id);
            $stmt->execute();
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($task) {
                if ($task['lead_id']) {
                    header("Location: view_tasks.php?lead_id={$task['lead_id']}");
                } else if ($task['project_id']) {
                     header("Location: view_tasks.php?project_id={$task['project_id']}");
                }
                else {
                   header("Location: view_tasks.php");
                  }
                exit();
            } else {
                // If related id is not a project or lead, redirect to dashboard
                header("Location: dashboard.php");
                exit();
            }
              break;
        case 'knowledge_base_article_update':
              // Redirect to the knowledge base article
               $related_id = $notification['related_id'];
               header("Location: view_knowledge_base_article.php?id=$related_id");
              exit();
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
?>