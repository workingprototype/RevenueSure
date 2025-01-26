<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$per_page = 10; // Leads per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Fetch total leads count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM leads");
$stmt->execute();
$total_leads = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_leads / $per_page);

// Fetch leads for the current page
$stmt = $conn->prepare("SELECT * FROM leads LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Leads - RevenueSure</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">RevenueSure</a>
            <div class="flex space-x-4">
                <a href="dashboard.php" class="hover:underline">Dashboard</a>
                <a href="search_leads.php" class="hover:underline">Search Leads</a>
                <a href="manage_credits.php" class="hover:underline">Manage Credits</a>
                <a href="logout.php" class="hover:underline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-10 px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Search Leads</h1>
        <form method="GET" action="" class="mb-8">
            <input type="text" name="search" placeholder="Search by name or email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search</button>
        </form>

        <?php if ($leads): ?>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Search Results</h2>
                <div class="space-y-4">
                    <?php foreach ($leads as $lead): ?>
                        <div class="border-b pb-4">
                            <p class="text-gray-800"><strong>Name:</strong> <?php echo htmlspecialchars($lead['name']); ?></p>
                            <p class="text-gray-600"><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
                            <p class="text-gray-600"><strong>Phone:</strong> <?php echo htmlspecialchars($lead['phone']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-gray-600">No leads found.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="px-4 py-2 mx-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>