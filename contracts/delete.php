<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "contracts/manage");
    exit();
}

$contract_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM contracts WHERE id = :id");
$stmt->bindParam(':id', $contract_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "contracts/manage");
    exit();
} else {
    echo "<script>alert('Error deleting contract.');</script>";
     header("Location: " . BASE_URL . "contracts/manage");
    exit();
}
?>