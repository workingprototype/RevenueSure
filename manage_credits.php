<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $credits = $_POST['credits'];

    $stmt = $conn->prepare("UPDATE users SET credits = credits + :credits WHERE id = :user_id");
    $stmt->bindParam(':credits', $credits);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        // Send email notification
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $to = $user['email'];
        $subject = 'Credits Updated';
        $message = "Your credits have been updated. New balance: {$_POST['credits']}.";
        $headers = 'From: no-reply@leadplatform.com';

        mail($to, $subject, $message, $headers);

        echo "<script>alert('Credits updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating credits.');</script>";
    }
}

$stmt = $conn->prepare("SELECT credits FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
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

<?php
// Include footer
require 'footer.php';
?>