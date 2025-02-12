<?php

require_once ROOT_PATH . 'helper/core.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_id = isset($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;

    if ($entry_id > 0) {
        try {
            // Toggle the requires_review status of the ledger entry
            $stmt = $conn->prepare("UPDATE ledger_entries SET requires_review = 1 WHERE id = :entry_id");
            $stmt->bindParam(':entry_id', $entry_id, PDO::PARAM_INT);
             if ($stmt->execute()) {
               echo json_encode(['success' => true, 'message' => 'Review status updated successfully.']);

            } else {
                 echo json_encode(['success' => false, 'message' => 'Failed to update review status.']);
            }
          header("Location: " . BASE_URL . "accounting/ledger?tab=review");
        exit();
        } catch (PDOException $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Invalid entry_id.']);
        exit;
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit;
}