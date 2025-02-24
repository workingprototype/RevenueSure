<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];

// Fetch drawing boards the user has access to
$stmt = $conn->prepare("SELECT drawing_boards.* FROM drawing_boards LEFT JOIN document_collaborators ON drawing_boards.id = document_collaborators.document_id WHERE drawing_boards.created_by = :user_id1 OR document_collaborators.user_id = :user_id2 ORDER BY drawing_boards.created_at DESC");
$stmt->bindParam(':user_id1', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);
$stmt->execute();
$drawing_boards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Drawing Boards</h1>

    <!-- Add Drawing Board Button -->
    <div class="mb-8">
        <a href="<?php echo BASE_URL; ?>drawings/add" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block"><i class="fas fa-plus-circle mr-2"></i>Add Drawing Board</a>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Drawing Boards Table -->
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
                <?php if ($drawing_boards): ?>
                    <?php foreach ($drawing_boards as $board): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($board['title']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($board['created_at']); ?></td>
                            <td class="px-4 py-2">
                                <a href="<?php echo BASE_URL; ?>drawings/view?id=<?php echo $board['id']; ?>" class="text-blue-600 hover:underline">View</a>
                                <a href="<?php echo BASE_URL; ?>drawings/edit?id=<?php echo $board['id']; ?>" class="text-green-600 hover:underline ml-2">Edit</a>
                                <a href="<?php echo BASE_URL; ?>drawings/delete?id=<?php echo $board['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-600">No drawing boards found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>