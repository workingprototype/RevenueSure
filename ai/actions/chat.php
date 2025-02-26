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

error_log("Received Input: " . json_encode($input)); // Log received input

if (!isset($input['message'], $input['conversation_id'], $input['model'], $input['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
    exit;
}

$message = trim($input['message']);
$conversation_id = (int)$input['conversation_id'];
$model = $input['model'];
$action = $input['action'];
$csrf_token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

if ($csrf_token !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit;
}

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
    exit;
}

$stmt = $conn->prepare("SELECT 1 FROM ai_conversations WHERE id = :conversation_id AND user_id = :user_id");
$stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

if (!$stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid conversation ID.']);
    exit;
}

try {
    $conn->beginTransaction();

    if ($action === 'save_user_message') {
        $stmt = $conn->prepare("INSERT INTO ai_messages (conversation_id, sender, message) VALUES (:conversation_id, 'user', :message)");
        $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'User message saved.']);

    } elseif ($action === 'save_ai_response') {
        $stmt = $conn->prepare("INSERT INTO ai_messages (conversation_id, sender, message) VALUES (:conversation_id, 'ai', :message)");
        $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'AI response saved.']);

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        $conn->rollBack();
        exit;
    }
    $conn->commit();

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
exit;
?>