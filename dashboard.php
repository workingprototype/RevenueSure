<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Get the user's role from the session

// Fetch user details
$stmt = $conn->prepare("SELECT username, credits FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch leads count
$stmt = $conn->prepare("SELECT COUNT(*) as total_leads FROM leads");
$stmt->execute();
$leads_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_leads'];

// Include header
require 'header.php';

?>


        <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Credits Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Your Credits</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $user['credits']; ?></p>
                <p class="text-gray-600 mt-2">Credits available for accessing leads.</p>
                <a href="manage_credits.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Manage Credits</a>
            </div>

            <!-- Leads Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Total Leads</h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $leads_count; ?></p>
                <p class="text-gray-600 mt-2">Leads available in the platform.</p>
                <a href="search_leads.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search Leads</a>
            </div>

            <!-- Admin-Specific Card -->
            <?php if ($role === 'admin'): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Admin Actions</h3>
                    <p class="text-gray-600">Manage users and leads.</p>
                    <a href="admin_dashboard.php" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Go to Admin Dashboard</a>
                </div>
            <?php endif; ?>
        </div>

<?php
// Include footer
require 'footer.php';
?>