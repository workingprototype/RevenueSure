<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to login to add leads.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("INSERT INTO leads (name, phone, email, category_id) VALUES (:name, :phone, :email, :category_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':category_id', $category_id);

    if ($stmt->execute()) {
        echo "Lead added successfully!";
    } else {
        echo "Error adding lead.";
    }
}
?>

<form method="POST" action="">
    <input type="text" name="name" placeholder="Name" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <input type="email" name="email" placeholder="Email" required>
    <select name="category_id" required>
        <option value="1">Category 1</option>
        <option value="2">Category 2</option>
        <!-- Add more categories as needed -->
    </select>
    <button type="submit">Add Lead</button>
</form>