<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if (isset($_GET['id']) && isset($_GET['is_read'])) {
    $email_id = $_GET['id'];
     $is_read = $_GET['is_read'];
    $stmt = $conn->prepare("UPDATE emails SET is_read = :is_read WHERE id = :id");
        $stmt->bindParam(':is_read', $is_read);
       $stmt->bindParam(':id', $email_id);
       if ($stmt->execute()) {
           header("Location: " . BASE_URL . "mail/view?id=$email_id");
            exit();
       } else {
           $error = "Error deleting email.";
       }
}
?>