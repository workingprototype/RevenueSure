<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';  // 'user', 'employee', or 'customer'

header('Content-Type: application/json');

if (empty($query) || empty($type)) {
    echo json_encode([]);
    exit;
}

$results = [];

try {
    switch ($type) {
        case 'user':
           $stmt = $conn->prepare("SELECT id, username as name FROM users WHERE (role = 'user' OR role = 'admin') AND username LIKE :query LIMIT 10");
           $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
           $stmt->execute();
           $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'employee':
            $stmt = $conn->prepare("SELECT id, name FROM employees WHERE name LIKE :query LIMIT 10");
           $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->execute();
           $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'customer':
           $stmt = $conn->prepare("SELECT id, name FROM customers WHERE name LIKE :query LIMIT 10");
            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->execute();
             $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
             break;
    }

     echo json_encode($results);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error.']);
}
?>