<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "team/roles/manage");
    exit();
}

$role_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM team_roles WHERE id = :id");
$stmt->bindParam(':id', $role_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "team/roles/manage");
    exit();
} else {
    echo "<script>alert('Error deleting role.');</script>";
     header("Location: " . BASE_URL . "team/roles/manage");
      exit();
}
?>