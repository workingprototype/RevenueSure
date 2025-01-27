<?php
require 'db.php';

// Fetch tasks with due dates in the next 24 hours
$stmt = $conn->prepare("
    SELECT tasks.*, users.email 
    FROM tasks 
    JOIN users ON tasks.user_id = users.id 
    WHERE due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 DAY)
");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tasks as $task) {
    // Insert a notification for the user
    $message = "Reminder: Task '{$task['description']}' is due on {$task['due_date']}.";
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, message, related_id, type) 
        VALUES (:user_id, :message, :related_id, 'task_reminder')
    ");
    $stmt->bindParam(':user_id', $task['user_id']);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':related_id', $task['id']);
    $stmt->execute();

    // Optionally, send an email reminder
    $to = $task['email'];
    $subject = "Task Reminder: {$task['description']}";
    $headers = "From: no-reply@revenuesure.com";
    mail($to, $subject, $message, $headers);
}