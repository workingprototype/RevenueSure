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


$drawing_board_id = isset($_POST['drawing_board_id']) ? (int)$_POST['drawing_board_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if (empty($drawing_board_id) || empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Drawing Board ID and User ID are required.']);
    exit;
}

try {
    // Check if the user is already a collaborator
    $stmt_check = $conn->prepare("SELECT 1 FROM drawing_board_collaborators WHERE drawing_board_id = :drawing_board_id AND user_id = :user_id");
    $stmt_check->bindParam(':drawing_board_id', $drawing_board_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'User is already a collaborator.']);
        exit;
    }

    // Add the user as a collaborator
    $stmt = $conn->prepare("INSERT INTO drawing_board_collaborators (drawing_board_id, user_id) VALUES (:drawing_board_id, :user_id)");
    $stmt->bindParam(':drawing_board_id', $drawing_board_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Collaborator added successfully!']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['status' => 'error', 'message' => 'Error adding collaborator: ' . implode(", ", $errorInfo)]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
exit;
?>