<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_project_categories.php");
    exit();
}

$category_id = $_GET['id'];

// Delete the category
$stmt = $conn->prepare("DELETE FROM project_categories WHERE id = :id");
$stmt->bindParam(':id', $category_id);

if ($stmt->execute()) {
    header("Location: manage_project_categories.php");
    exit();
} else {
    echo "<script>alert('Error deleting category.');</script>";
     header("Location: manage_project_categories.php");
      exit();
}
?>