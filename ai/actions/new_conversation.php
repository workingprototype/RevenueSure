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
}// Check for login and get user_id
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO ai_conversations (user_id) VALUES (:user_id)");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$conversation_id = $conn->lastInsertId();

// Store the conversation ID in the session
$_SESSION['conversation_id'] = $conversation_id;

echo json_encode(['status' => 'success', 'conversation_id' => $conversation_id]);
?>