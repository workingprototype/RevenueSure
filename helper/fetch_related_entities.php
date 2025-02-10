<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$user_id = $_SESSION['user_id'];


if (!empty($search) && !empty($type)) {
    $results = [];
    switch ($type) {
       case 'task':
          $stmt = $conn->prepare("SELECT id, description as title FROM tasks WHERE user_id = :user_id AND description LIKE :search LIMIT 10");
           $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
             $stmt->bindParam(':user_id', $user_id);
           $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
           break;
         case 'employee':
            $stmt = $conn->prepare("SELECT id, name FROM employees WHERE name LIKE :search LIMIT 10");
           $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'lead':
            $stmt = $conn->prepare("SELECT id, name FROM leads WHERE name LIKE :search AND assigned_to = :user_id ORDER BY name ASC LIMIT 5");
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
              $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
             $assignedLeads = $stmt->fetchAll(PDO::FETCH_ASSOC);
               $stmt = $conn->prepare("SELECT id, name FROM leads WHERE name LIKE :search AND (assigned_to IS NULL OR assigned_to != :user_id) ORDER BY name ASC LIMIT 5");
                 $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                 $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $unassignedLeads = $stmt->fetchAll(PDO::FETCH_ASSOC);
                 $results = array_merge($assignedLeads, $unassignedLeads);
             break;
        case 'customer':
            $stmt = $conn->prepare("SELECT id, name FROM customers WHERE name LIKE :search ORDER BY name ASC LIMIT 10");
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            break;
          default:
             break;
    }
    header('Content-Type: application/json');
    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>