<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_leads'])) {
    $selected_leads = $_POST['selected_leads'];
    if (!empty($selected_leads)) {
        $placeholders = implode(',', array_fill(0, count($selected_leads), '?'));
        $stmt = $conn->prepare("DELETE FROM leads WHERE id IN ($placeholders)");
        $stmt->execute($selected_leads);
        echo "<script>alert('Selected leads deleted successfully!'); window.location.href='search_leads.php';</script>";
    } else {
        echo "<script>alert('No leads selected for deletion.'); window.location.href='search_leads.php';</script>";
    }
} else {
    header("Location: search_leads.php");
    exit();
}
?>