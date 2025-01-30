<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_roles.php");
    exit();
}

$role_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM team_roles WHERE id = :id");
$stmt->bindParam(':id', $role_id);

if ($stmt->execute()) {
    header("Location: manage_roles.php");
    exit();
} else {
    echo "<script>alert('Error deleting role.');</script>";
     header("Location: manage_roles.php");
      exit();
}
?>