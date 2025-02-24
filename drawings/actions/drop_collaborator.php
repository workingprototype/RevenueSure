<?php
require_once '../../helper/core.php'; // Adjust path as needed
redirectIfUnauthorized(true);
header('Content-Type: application/json');

$drawing_board_id = isset($_POST['drawing_board_id']) ? (int)$_POST['drawing_board_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if (empty($drawing_board_id) || empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Drawing Board ID and User ID are required.']);
    exit;
}

try {
    // Delete the collaborator
    $stmt = $conn->prepare("DELETE FROM drawing_board_collaborators WHERE drawing_board_id = :drawing_board_id AND user_id = :user_id");
    $stmt->bindParam(':drawing_board_id', $drawing_board_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Collaborator dropped successfully!']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['status' => 'error', 'message' => 'Error dropping collaborator: ' . implode(", ", $errorInfo)]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
exit;
?>