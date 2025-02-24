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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['drawing_board_id']) && isset($_POST['elements'])) {
    $drawing_board_id = (int)$_POST['drawing_board_id'];
    $elements = $_POST['elements'];

    error_log("actions/save.php: Received drawing_board_id = " . $drawing_board_id);
    error_log("actions/save.php: Received elements data (length) = " . strlen($elements)); // Log length

    if (empty($elements)) {
        $error = "Elements data is required.";
        error_log("actions/save.php: Error - Elements data is empty.");
        echo json_encode(['status' => 'error', 'message' => $error, 'savedManually' => false]); // Indicate not manually saved
        exit;
    } else {
        try {
            // Update document
            $stmt = $conn->prepare("UPDATE drawing_boards SET elements = :elements, updated_at = NOW() WHERE id = :id");
            $stmt->bindParam(':elements', $elements, PDO::PARAM_STR);
            $stmt->bindParam(':id', $drawing_board_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                error_log("actions/save.php: Drawing Board saved successfully!");
                echo json_encode(['status' => 'success', 'message' => 'Drawing Board saved successfully!', 'savedManually' => true]); // Indicate manually saved
                exit();
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = "Error saving drawing board: " . implode(", ", $errorInfo);
                error_log("actions/save.php: Error saving drawing board: " . $error);
                error_log("actions/save.php: SQLSTATE=" . $errorInfo[0] . " | Driver Code=" . $errorInfo[1] . " | Message=" . $errorInfo[2]);
                echo json_encode(['status' => 'error', 'message' => $error, 'savedManually' => false]); // Indicate not manually saved
                exit;
            }
        } catch (PDOException $e) {
            error_log("actions/save.php: Database error: " . $e->getMessage() . " in " . __FILE__ . " on line " . __LINE__, 0);
            $error = "Database error. Please contact support.";
            echo json_encode(['status' => 'error', 'message' => $error, 'savedManually' => false]); // Indicate not manually saved
            exit;
        }
    }
}

error_log("actions/save.php: Invalid request.");
echo json_encode(['status' => 'error', 'message' => 'Invalid request.', 'savedManually' => false]); // Indicate not manually saved
exit;
?>