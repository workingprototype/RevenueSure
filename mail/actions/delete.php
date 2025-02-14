<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if (isset($_GET['id'])) {
    $email_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM emails WHERE id = :id");
    $stmt->bindParam(':id', $email_id);
       if ($stmt->execute()) {
            header("Location: " . BASE_URL . "mail/index");
           exit();
       } else {
         $error = "Error deleting email.";
         }
    }
?>