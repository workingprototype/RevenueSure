<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "ai/workbooks/manage");
    exit();
}

$workbook_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM ai_workbooks WHERE id = :id");
$stmt->bindParam(':id', $workbook_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "ai/workbooks/manage");
    exit();
} else {
    echo "<script>alert('Error deleting workbook.');</script>";
}
?>