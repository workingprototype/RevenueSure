<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Database connection
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

if (!isset($input['message_id'], $input['new_message'], $input['conversation_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
    exit;
}

$message_id = (int)$input['message_id'];
$new_message = trim($input['new_message']);
$conversation_id = (int)$input['conversation_id'];
$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if ($csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit;
}
// Validate that the message belongs to the conversation, and the conversation to the user
$stmt = $conn->prepare("SELECT 1 FROM ai_messages m JOIN ai_conversations c ON m.conversation_id = c.id WHERE m.id = :message_id AND m.conversation_id = :conversation_id AND c.user_id = :user_id");
$stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
if(!$stmt->fetch()){
     echo json_encode(['status' => 'error', 'message' => 'Invalid message ID, conversation ID, or unauthorized access.']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE ai_messages SET message = :new_message WHERE id = :message_id");
    $stmt->bindParam(':new_message', $new_message, PDO::PARAM_STR);
    $stmt->bindParam(':message_id', $message_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Message updated.']);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}