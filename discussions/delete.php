<?php

require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id']) || !isset($_GET['discussion_id'])) {
    header("Location: " . BASE_URL . "discussions/manage");
    exit();
}

$message_id = $_GET['id'];
$discussion_id = $_GET['discussion_id'];

$stmt = $conn->prepare("DELETE FROM discussion_messages WHERE id = :id");
$stmt->bindParam(':id', $message_id);


if ($stmt->execute()) {
    header("Location: " . BASE_URL . "discussions/view?id=$discussion_id");
    exit();
} else {
    echo "<script>alert('Error deleting message.'); window.location.href='<?php echo BASE_URL; ?>discussions/view?id=$discussion_id';</script>";
    exit();
}
?>