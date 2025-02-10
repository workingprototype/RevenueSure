<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;
$interaction_type = $_POST['interaction_type'];
$details = $_POST['details'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $customer_id) {

  $stmt = $conn->prepare("INSERT INTO customer_interactions (customer_id, interaction_type, details) VALUES (:customer_id, :interaction_type, :details)");
    $stmt->bindParam(':customer_id', $customer_id);
     $stmt->bindParam(':interaction_type', $interaction_type);
     $stmt->bindParam(':details', $details);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "customers/view?id=$customer_id&success=true");
        exit();
    } else {
        echo "<script>alert('Error adding interaction.');</script>";
    }

} else {
    header("Location: " . BASE_URL . "customers/view?id=$customer_id");
    exit();
}
?>