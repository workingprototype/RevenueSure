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

$document_id = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

error_log("add_collaborator.php: document_id = " . $document_id . ", user_id = " . $user_id);

if (empty($document_id) || empty($user_id)) {
    $response = ['status' => 'error', 'message' => 'Document ID and User ID are required.'];
    error_log("add_collaborator.php: Input validation failed - " . $response['message']);
    echo json_encode($response);
    exit;
}

error_log("add_collaborator.php: Input validation passed");

try {
    // Check if the user is already a collaborator
    $sql_check = "SELECT 1 FROM document_collaborators WHERE document_id = :document_id AND user_id = :user_id";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':document_id', $document_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->fetch()) {
        $response = ['status' => 'error', 'message' => 'User is already a collaborator.'];
        error_log("add_collaborator.php: User is already a collaborator");
        echo json_encode($response);
        exit;
    }

    error_log("add_collaborator.php: User is not already a collaborator");

    // Add the user as a collaborator
    $sql_insert = "INSERT INTO document_collaborators (document_id, user_id) VALUES (:document_id, :user_id)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bindParam(':document_id', $document_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    $success = $stmt->execute(); // Store result of execute

    if ($success) {
        $response = ['status' => 'success', 'message' => 'Collaborator added successfully!'];
        error_log("add_collaborator.php: Collaborator added successfully!");
    } else {
        $errorInfo = $stmt->errorInfo();
        $response = ['status' => 'error', 'message' => 'Error adding collaborator: ' . implode(", ", $errorInfo)];
        error_log("add_collaborator.php: Error adding collaborator - " . implode(", ", $errorInfo));
    }

} catch (PDOException $e) {
    $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    error_log("add_collaborator.php: Database error (insert) - " . $e->getMessage());
}

echo json_encode($response);
exit;
?>