<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "notes/manage");
    exit();
}

$note_id = $_GET['id'];

// Delete the note
$stmt = $conn->prepare("DELETE FROM notes WHERE id = :id");
$stmt->bindParam(':id', $note_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "notes/manage");
    exit();
} else {
    echo "<script>alert('Error deleting note.');</script>";
      header("Location: " . BASE_URL . "notes/manage");
    exit();
}
?>