<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "team/manage");
    exit();
}

$member_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
$stmt->bindParam(':id', $member_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "team/manage");
    exit();
} else {
    echo "<script>alert('Error deleting team member.');</script>";
    header("Location: " . BASE_URL . "team/manage");
      exit();
}
?>