<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "dashboard");
    exit();
}

$todo_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM todos WHERE id = :id");
$stmt->bindParam(':id', $todo_id);

if ($stmt->execute()) {
      header("Location: " . BASE_URL . "dashboard");
    exit();
} else {
    echo "<script>alert('Error deleting todo.');</script>";
     header("Location: " . BASE_URL . "dashboard");
    exit();
}
?>