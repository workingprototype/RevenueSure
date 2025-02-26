<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

// Fetch all workbooks
$stmt = $conn->prepare("SELECT * FROM ai_workbooks WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$workbooks = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage AI Workbooks</h1>

    <!-- Add Workbook Button -->
    <div class="flex justify-between items-center mb-8">
         <a href="<?php echo BASE_URL; ?>ai/workbooks/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block"><i class="fas fa-plus-circle mr-2"></i>Add Workbook</a>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <!-- Workbooks Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Created At</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($workbooks): ?>
                    <?php foreach ($workbooks as $workbook): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($workbook['title']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($workbook['created_at']); ?></td>
                            <td class="px-4 py-2">
                                <a href="<?php echo BASE_URL; ?>ai/workbooks/edit?id=<?php echo $workbook['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                <a href="<?php echo BASE_URL; ?>ai/workbooks/delete?id=<?php echo $workbook['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-600">No workbooks found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>