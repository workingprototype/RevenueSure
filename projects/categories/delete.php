<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "projects/categories/manage");
    exit();
}

$category_id = $_GET['id'];

// Delete the category
$stmt = $conn->prepare("DELETE FROM project_categories WHERE id = :id");
$stmt->bindParam(':id', $category_id);

if ($stmt->execute()) {
    header("Location: " . BASE_URL . "projects/categories/manage");
    exit();
} else {
    echo "<script>alert('Error deleting category.');</script>";
     header("Location: " . BASE_URL . "projects/categories/manage");
      exit();
}
?>