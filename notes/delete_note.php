<?php
session_start();
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $note_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        $query = "DELETE FROM notes WHERE id = :id AND created_by = :user_id";
        $stmt  = $conn->prepare($query);
        $stmt->bindValue(':id', $note_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success'] = "Note deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting note: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "No note ID provided.";
}

header("Location: " . BASE_URL . "notes/");
exit;
