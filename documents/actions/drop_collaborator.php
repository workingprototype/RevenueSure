<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Simulate core.php for testing database
define('ROOT_PATH', __DIR__ . '/../'); // Adjust path as needed
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "lead_platform";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $response = ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
    echo json_encode($response);
    exit;
}
header('Content-Type: application/json');

$document_id = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if (empty($document_id) || empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Document ID and User ID are required.']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM document_collaborators WHERE document_id = :document_id AND user_id = :user_id");
    $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
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