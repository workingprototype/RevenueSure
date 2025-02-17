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

// Get current time
$now = new DateTime();
$now->setTimezone(new DateTimeZone('UTC')); // Ensure UTC for consistency

// Fetch reminders due within the next 15 minutes
$stmt = $conn->prepare("
    SELECT reminders.*, users.email, users.timezone
    FROM reminders
    JOIN users ON reminders.user_id = users.id
    WHERE reminders.due_date BETWEEN :now AND DATE_ADD(:now, INTERVAL 15 MINUTE)
    AND reminders.status = 'pending'
");
$stmt->bindParam(':now', $now->format('Y-m-d H:i:s'));
$stmt->execute();
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process each reminder
foreach ($reminders as $reminder) {
    // Get user's timezone
    $userTimezone = new DateTimeZone($reminder['timezone'] ?: 'UTC');

    // Convert due date to user's timezone
    $due_date = new DateTime($reminder['due_date']);
    $due_date->setTimezone($userTimezone);

    // Deserialize notification preferences
    $notification_preferences = json_decode($reminder['notification_preferences'], true);

    // In-App Notification
    if (isset($notification_preferences['in_app']) && $notification_preferences['in_app']) {
        // Insert a notification for the user
        $message = "Reminder: {$reminder['title']} is due on {$due_date->format('Y-m-d H:i')}.";
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, message, related_id, type) 
            VALUES (:user_id, :message, :related_id, 'reminder')
        ");
        $stmt->bindParam(':user_id', $reminder['user_id']);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':related_id', $reminder['id']);
        $stmt->execute();
    }

    // Email Notification
    if (isset($notification_preferences['email']) && $notification_preferences['email']) {
        // Send email reminder
        $to = $reminder['email'];
        $subject = "Reminder: {$reminder['title']}";
        $message = "This is a reminder that {$reminder['title']} is due on {$due_date->format('Y-m-d H:i')}.";
        $headers = "From: no-reply@revenuesure.com";
        mail($to, $subject, $message, $headers);
    }

    // Update reminder status (optional: mark as snoozed, etc.)
    // $stmt = $conn->prepare("UPDATE reminders SET status = 'snoozed' WHERE id = :id");
    // $stmt->bindParam(':id', $reminder['id']);
    // $stmt->execute();
}