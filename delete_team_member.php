<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_teams.php");
    exit();
}

$member_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
$stmt->bindParam(':id', $member_id);

if ($stmt->execute()) {
    header("Location: manage_teams.php");
    exit();
} else {
    echo "<script>alert('Error deleting team member.');</script>";
    header("Location: manage_teams.php");
      exit();
}
?>