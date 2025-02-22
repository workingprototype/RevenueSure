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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['document_id']) && isset($_POST['content'])) {
    $document_id = (int)$_POST['document_id'];
    $content = $_POST['content'];

    if (empty($content)) {
        $error = "Content is required.";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE documents SET content = :content, updated_at = NOW() WHERE id = :id");
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':id', $document_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Document saved successfully!']);
                exit;
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = "Error saving document: " . implode(", ", $errorInfo);
                echo json_encode(['status' => 'error', 'message' => $error]);
                exit;
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__, 0);
            $error = "Database error. Please contact support.";
            echo json_encode(['status' => 'error', 'message' => $error]);
            exit;
        }
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
exit;


?>