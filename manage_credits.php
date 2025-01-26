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
        echo "<script>alert('Credits updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating credits.');</script>";
    }
}

$stmt = $conn->prepare("SELECT credits FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Credits - Lead Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-blue-600 p-4 text-white">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold">Lead Platform</a>
        <div class="flex space-x-4">
            <a href="dashboard.php" class="hover:underline">Dashboard</a>
            <a href="search_leads.php" class="hover:underline">Search Leads</a>
            <a href="manage_credits.php" class="hover:underline">Manage Credits</a>
            <a href="logout.php" class="hover:underline">Logout</a>
        </div>
    </div>
</nav>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Credits</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-gray-800 mb-4"><strong>Current Credits:</strong> <?php echo $user['credits']; ?></p>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="credits" class="block text-gray-700">Add/Remove Credits</label>
                    <input type="number" name="credits" id="credits" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Credits</button>
            </form>
        </div>
    </div>
</body>
</html>