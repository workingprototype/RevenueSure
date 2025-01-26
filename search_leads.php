<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to login to search leads.");
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];

    $stmt = $conn->prepare("SELECT * FROM leads WHERE name LIKE :search OR email LIKE :search");
    $stmt->bindValue(':search', "%$search%");
    $stmt->execute();
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($leads) {
        foreach ($leads as $lead) {
            echo "Name: " . $lead['name'] . " - Email: " . $lead['email'] . " - Phone: " . $lead['phone'] . "<br>";
        }
    } else {
        echo "No leads found.";
    }
}
?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search leads" required>
    <button type="submit">Search</button>
</form>