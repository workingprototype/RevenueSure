<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id']) || !isset($_GET['completed'])) {
    header("Location: " . BASE_URL . "dashboard");
    exit();
}

$todo_id = $_GET['id'];
$is_completed = $_GET['completed'];

$stmt = $conn->prepare("UPDATE todos SET is_completed = :is_completed WHERE id = :id");
$stmt->bindParam(':is_completed', $is_completed);
$stmt->bindParam(':id', $todo_id);

if ($stmt->execute()) {
      header("Location: " . BASE_URL . "dashboard");
    exit();
} else {
     echo "<script>alert('Error marking todo.');</script>";
      header("Location: " . BASE_URL . "dashboard");
    exit();
}
?>