<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_lead.php");
    exit();
}

$attachment_id = $_GET['id'];

// Fetch attachment details
$stmt = $conn->prepare("SELECT * FROM attachments WHERE id = :id");
$stmt->bindParam(':id', $attachment_id);
$stmt->execute();
$attachment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($attachment) {
    // Delete the file from the server
    if (file_exists($attachment['file_path'])) {
        unlink($attachment['file_path']);
    }

    // Delete the attachment record from the database
    $stmt = $conn->prepare("DELETE FROM attachments WHERE id = :id");
    $stmt->bindParam(':id', $attachment_id);

    if ($stmt->execute()) {
        header("Location: view_lead.php?id={$attachment['lead_id']}");
        exit();
    } else {
        echo "<script>alert('Error deleting attachment.');</script>";
    }
} else {
    header("Location: view_lead.php");
    exit();
}
?>