<?php
ini_set('display_errors' , 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['discussion_id'])) {
    header("Location: manage_discussions.php");
    exit();
}

$message_id = $_GET['id'];
$discussion_id = $_GET['discussion_id'];

$stmt = $conn->prepare("DELETE FROM discussion_messages WHERE id = :id");
$stmt->bindParam(':id', $message_id);


if ($stmt->execute()) {
    header("Location: view_discussion.php?id=$discussion_id");
    exit();
} else {
    echo "<script>alert('Error deleting message.'); window.location.href='view_discussion.php?id=$discussion_id';</script>";
    exit();
}
?>