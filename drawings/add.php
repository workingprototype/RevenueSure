<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO drawing_boards (title, created_by) VALUES (:title, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            $drawing_board_id = $conn->lastInsertId();
            header("Location: " . BASE_URL . "drawings/edit?id=$drawing_board_id"); // Redirect to edit page
            exit();
        } else {
            $error = "Error creating drawing board.";
        }
    }
}
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Drawing Board</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Create Drawing Board</button>
        </form>
    </div>
</div>