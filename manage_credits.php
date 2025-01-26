<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to login to manage credits.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $credits = $_POST['credits'];

    $stmt = $conn->prepare("UPDATE users SET credits = credits + :credits WHERE id = :user_id");
    $stmt->bindParam(':credits', $credits);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "Credits updated successfully!";
    } else {
        echo "Error updating credits.";
    }
}

$stmt = $conn->prepare("SELECT credits FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Current Credits: " . $user['credits'];
?>

<form method="POST" action="">
    <input type="number" name="credits" placeholder="Add/Remove Credits" required>
    <button type="submit">Update Credits</button>
</form>