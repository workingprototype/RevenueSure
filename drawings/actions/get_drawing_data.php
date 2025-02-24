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

$drawing_board_id = isset($_GET['drawing_board_id']) ? (int)$_GET['drawing_board_id'] : 0;

if (empty($drawing_board_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Drawing Board ID is required.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT elements FROM drawing_boards WHERE id = :id");
    $stmt->bindParam(':id', $drawing_board_id, PDO::PARAM_INT);
    $stmt->execute();
    $drawing_board = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($drawing_board) {
        $elements = $drawing_board['elements'];
        // Decode the elements JSON string
        $data = json_decode($elements, true);

        // Check if decoding was successful
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['status' => 'error', 'message' => 'Error decoding elements JSON: ' . json_last_error_msg()]);
            exit;
        }

        // Check if elements and appState are defined
        if (!isset($data['elements']) || !isset($data['appState'])) {
            echo json_encode(['status' => 'error', 'message' => 'Elements or appState not found in drawing data.']);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'elements' => $data['elements'],
            'appState' => $data['appState']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Drawing board not found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

exit;