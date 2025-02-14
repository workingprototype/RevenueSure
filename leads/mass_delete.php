<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_leads'])) {
    $selected_leads = $_POST['selected_leads'];
    if (!empty($selected_leads)) {
        $placeholders = implode(',', array_fill(0, count($selected_leads), '?'));
        $stmt = $conn->prepare("DELETE FROM leads WHERE id IN ($placeholders)");
        $stmt->execute($selected_leads);
        echo "<script>alert('Selected leads deleted successfully!'); window.location.href='<?php echo BASE_URL; ?>leads/search';</script>";
    } else {
        echo "<script>alert('No leads selected for deletion.'); window.location.href='<?php echo BASE_URL; ?>leads/search';</script>";
    }
} else {
    header("Location: " . BASE_URL . "leads/search");
    exit();
}
?>