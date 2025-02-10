<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

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
        $headers = 'From: no-reply@revenuesure.com';

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


?>
<div class="container mx-auto p-6 fade-in">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Manage Credits</h1>
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-blue-500 transition hover:shadow-2xl">
            <p class="text-gray-800 mb-4"><span class="font-semibold text-gray-900">Current Credits:</span> <?php echo $user['credits']; ?></p>
            <form method="POST" action="">
            <?php echo csrfTokenInput(); ?>
                 <div class="mb-4">
                    <label for="credits" class="block text-gray-700">Add/Remove Credits</label>
                    <input type="number" name="credits" id="credits" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                 </div>
                 <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Update Credits</button>
            </form>
        </div>
</div>
