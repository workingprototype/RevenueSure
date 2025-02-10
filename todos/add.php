<?php


require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $related_type = $_POST['related_type'];
    $related_id = $_POST['related_id'];

    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO todos (user_id, title, description, due_date, related_type, related_id) VALUES (:user_id, :title, :description, :due_date, :related_type, :related_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':related_type', $related_type);
        $stmt->bindParam(':related_id', $related_id);
         if ($stmt->execute()) {
            header("Location: " . BASE_URL . "dashboard?success=true");
            exit();
        } else {
              echo "<script>alert('Error adding todo.');</script>";
            header("Location: " . BASE_URL . "dashboard");
             exit();
        }
    } else {
         header("Location: " . BASE_URL . "dashboard");
         exit();
    }
} else {
    header("Location: " . BASE_URL . "dashboard");
    exit();
}
?>