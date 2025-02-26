<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Simulate core.php for testing database  (Keep your existing database setup)
define('ROOT_PATH', __DIR__ . '/../'); // Adjust path as needed
$servername = "localhost";  // Your database server
$username = "username"; // Your database username
$password = "password";     // Your database password
$dbname = "lead_platform";        // Your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $response = ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
    echo json_encode($response);
    exit;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("INSERT INTO ai_conversations (user_id) VALUES (:user_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $conversation_id = $conn->lastInsertId();

    // Hash the conversation ID for the *response*.  The JavaScript will use this.
    $hashed_conversation_id = hash('sha256', (string)$conversation_id); // Cast to string!

    // Store the *unhashed* ID in the session.  This is for database queries.
    $_SESSION['conversation_id'] = $conversation_id; // Store the *integer* ID.

    echo json_encode(['status' => 'success', 'conversation_id' => $hashed_conversation_id]); // Send back the *hashed* ID

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to create conversation: ' . $e->getMessage()]);
    exit;
}

?>