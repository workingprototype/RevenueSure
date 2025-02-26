<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Database connection (same as your other actions files)
define('ROOT_PATH', __DIR__ . '/../');
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "lead_platform";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized: User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['conversation_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing conversation ID.']);
    exit;
}

$hashed_conversation_id = $input['conversation_id'];
$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if ($csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit;
}
// Convert hashed conversation ID back to the original integer ID
$stmt = $conn->prepare("SELECT id FROM ai_conversations WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conversation_id = null;
foreach ($conversations as $convo) {
  if (hash('sha256', (string)$convo['id']) === $hashed_conversation_id) {
    $conversation_id = $convo['id'];
        break;
  }
}

if (!$conversation_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid conversation ID. It may not belong to this user.']);
    exit;
}

try {
    // Use a transaction for data consistency
    $conn->beginTransaction();

    // Delete messages associated with the conversation (foreign key constraint should handle this, but it's good practice)
    $stmt = $conn->prepare("DELETE FROM ai_messages WHERE conversation_id = :conversation_id");
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->execute();

    // Delete the conversation itself
    $stmt = $conn->prepare("DELETE FROM ai_conversations WHERE id = :conversation_id AND user_id = :user_id");
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();


    // Check if any rows were affected.  This confirms the deletion.
    if ($stmt->rowCount() === 0) {
        $conn->rollBack(); // Rollback if nothing was deleted
        echo json_encode(['status' => 'error', 'message' => 'Conversation not found or you do not have permission to delete it.']);
        exit;
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Conversation deleted.']);

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}