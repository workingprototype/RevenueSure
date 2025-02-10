<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';


if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "manage");
    exit();
}

$customer_id = (int)$_GET['id']; // Sanitize input

try {
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = :id");
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "manage");
        exit();
    } else {
        echo "<script>alert('Error deleting customer.');</script>";
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<script>alert('Error deleting customer due to a database issue.');</script>";
}
?>