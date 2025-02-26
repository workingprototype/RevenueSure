<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $related_type = $_POST['related_type'] ?? null;
            $related_id = (!empty($_POST['related_id']) && $_POST['related_type'] != "") ? (int)$_POST['related_id'] : null;
             $user_id = $_SESSION['user_id'];
            if (empty($title)) {
                $error = "All fields are required.";
            } else {
                $stmt = $conn->prepare("INSERT INTO ai_workbooks (user_id, title, description, related_type, related_id) VALUES (:user_id, :title, :description, :related_type, :related_id)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':related_type', $related_type);
                $stmt->bindParam(':related_id', $related_id);

                if ($stmt->execute()) {
                    $success = "Workbook created successfully!";
                } else {
                    $error = "Error creating workbook.";
                }
            }
            break;
        case 'edit':
            // Edit logic here if needed.
            break;
        case 'delete':
            // Delete Logic Here
            break;
        default:
            $error = "Invalid action specified.";
            break;
    }
}

header("Location: " . BASE_URL . "ai/workbooks/manage");
exit();