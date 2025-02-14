<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$lead_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM leads WHERE id = :id");
$stmt->bindParam(':id', $lead_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "leads/add");
    exit();
} else {
    echo "<script>alert('Error deleting lead.');</script>";
}
?>