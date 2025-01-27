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
fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Category ID', 'Score', 'Category']);

// Add lead data
foreach ($leads as $lead) {
    $stmt = $conn->prepare("SELECT * FROM lead_scores WHERE lead_id = :lead_id");
    $stmt->bindParam(':lead_id', $lead['id']);
    $stmt->execute();
    $lead_score = $stmt->fetch(PDO::FETCH_ASSOC);

    $lead['score'] = $lead_score ? $lead_score['total_score'] : 0;
    $lead['category'] = $lead_score ? categorize_lead($lead_score['total_score']) : "Cold";

    fputcsv($output, $lead);
}

fclose($output);
exit();