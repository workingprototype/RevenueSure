<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($lead_id && $action) {
    // Fetch or create lead score record
    $stmt = $conn->prepare("SELECT * FROM lead_scores WHERE lead_id = :lead_id");
    $stmt->bindParam(':lead_id', $lead_id);
    $stmt->execute();
    $lead_score = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead_score) {
        $stmt = $conn->prepare("INSERT INTO lead_scores (lead_id) VALUES (:lead_id)");
        $stmt->bindParam(':lead_id', $lead_id);
        $stmt->execute();
        $lead_score = ['lead_id' => $lead_id, 'website_visits' => 0, 'email_opens' => 0, 'form_submissions' => 0];
    }

    // Update behavior based on action
    switch ($action) {
        case 'website_visit':
            $stmt = $conn->prepare("UPDATE lead_scores SET website_visits = website_visits + 1 WHERE lead_id = :lead_id");
            break;
        case 'email_open':
            $stmt = $conn->prepare("UPDATE lead_scores SET email_opens = email_opens + 1 WHERE lead_id = :lead_id");
            break;
        case 'form_submission':
            $stmt = $conn->prepare("UPDATE lead_scores SET form_submissions = form_submissions + 1 WHERE lead_id = :lead_id");
            break;
        default:
            echo "<script>alert('Invalid action.');</script>";
            exit();
    }

    $stmt->bindParam(':lead_id', $lead_id);
    $stmt->execute();

    // Recalculate total score
    $stmt = $conn->prepare("UPDATE lead_scores SET total_score = (website_visits * 1) + (email_opens * 2) + (form_submissions * 3) WHERE lead_id = :lead_id");
    $stmt->bindParam(':lead_id', $lead_id);
    $stmt->execute();

    echo "<script>alert('Behavior tracked successfully!'); window.location.href='view_lead.php?id=$lead_id';</script>";
} else {
    echo "<script>alert('Invalid request.');</script>";
}
?>