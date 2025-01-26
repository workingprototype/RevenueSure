<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch leads
$stmt = $conn->prepare("SELECT * FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="leads_export.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Category ID']);

// Add lead data
foreach ($leads as $lead) {
    fputcsv($output, $lead);
}

fclose($output);
exit();