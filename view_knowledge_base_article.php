<?php
/**
 * Knowledge Base Article Viewer
 * 
 * This script allows users to view a knowledge base article, rate it, and bookmark it.
 * It also provides options for admins to edit or delete the article.
 */

// Start session and include necessary files
session_start();
require 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get article ID from the URL
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch article details with category and author information
$stmt = $conn->prepare("
    SELECT knowledge_base_articles.*, knowledge_base_categories.name as category_name, users.username 
    FROM knowledge_base_articles
    LEFT JOIN knowledge_base_categories ON knowledge_base_articles.category_id = knowledge_base_categories.id
    INNER JOIN users ON knowledge_base_articles.user_id = users.id 
    WHERE knowledge_base_articles.id = :id
");
$stmt->bindParam(':id', $article_id);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if article not found
if (!$article) {
    header("Location: manage_knowledge_base.php");
    exit();
}

// Fetch user's rating for the article
$stmt = $conn->prepare("
    SELECT rating 
    FROM knowledge_base_article_ratings 
    WHERE article_id = :article_id AND user_id = :user_id
");
$stmt->bindParam(':article_id', $article_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user_rating = $stmt->fetch(PDO::FETCH_ASSOC);
$user_rated_value = $user_rating ? $user_rating['rating'] : '';

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $stmt = $conn->prepare("
        INSERT INTO knowledge_base_article_ratings (article_id, user_id, rating, comment) 
        VALUES (:article_id, :user_id, :rating, :comment)
        ON DUPLICATE KEY UPDATE rating = :rating, comment = :comment, created_at = NOW()
    ");
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':comment', $comment);
    if ($stmt->execute()) {
        $user_rated_value = $rating;
        header("Location: view_knowledge_base_article.php?id=$article_id&success=true");
        exit();
    } else {
        echo "<script>alert('Error submitting rating.'); window.location.href='view_knowledge_base_article.php?id=$article_id';</script>";
    }
}

// Display success message if rating was updated
if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $success = "Rating updated successfully!";
}

// Fetch user's bookmark for the article
$stmt = $conn->prepare("
    SELECT * 
    FROM knowledge_base_bookmarks 
    WHERE user_id = :user_id AND article_id = :article_id
");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->bindParam(':article_id', $article_id);
$stmt->execute();
$bookmarked = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle bookmark submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bookmark'])) {
    $notes = $_POST['notes'];
    if ($bookmarked) {
        $stmt = $conn->prepare("
            DELETE FROM knowledge_base_bookmarks 
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $bookmarked['id']);
        if ($stmt->execute()) {
            header("Location: view_knowledge_base_article.php?id=$article_id");
            exit();
        }
    } else {
        $stmt = $conn->prepare("
            INSERT INTO knowledge_base_bookmarks (user_id, article_id, notes) 
            VALUES (:user_id, :article_id, :notes)
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':notes', $notes);
        if ($stmt->execute()) {
            header("Location: view_knowledge_base_article.php?id=$article_id");
            exit();
        }
    }
}

// Include header
require 'header.php';
?>

<!-- Main Content -->
<div class="container mx-auto p-6 fade-in">
    <!-- Article Title and Admin Actions -->
    <div class="flex justify-between items-start mb-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
            <?php echo htmlspecialchars($article['title']); ?>
        </h1>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="flex justify-end gap-2">
                <a href="edit_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                <a href="delete_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-red-600 hover:underline">Delete</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success Message -->
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <!-- Article Content -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <p class="mb-2"><strong>Category:</strong> <?php echo htmlspecialchars($article['category_name'] ? $article['category_name'] : 'Uncategorized'); ?></p>
        <p class="mb-2"><strong>Author:</strong> <?php echo htmlspecialchars($article['username']); ?></p>
        <p class="text-gray-700 leading-relaxed">
            <?php echo $article['content']; ?>
        </p>

        <!-- Rating and Bookmark Section -->
        <div class="flex items-center justify-between mt-6">
            <!-- Rating Buttons -->
            <div class="flex items-center gap-2">
                <form method="post" action="">
                    <input type="hidden" name="rating" value="upvote">
                    <input type="hidden" name="comment" value="">
                    <button type="submit" name="submit_rating" class="hover:underline <?php if($user_rated_value === 'upvote') echo 'text-green-600'; else echo 'text-gray-600'; ?>">
                        <i class="fas fa-thumbs-up"></i> Upvote
                    </button>
                </form>
                <form method="post" action="">
                    <input type="hidden" name="rating" value="downvote">
                    <div class="flex gap-2">
                        <textarea name="comment" id="comment" class="border rounded-md p-1 text-gray-600 text-xs hidden" placeholder="Give a reason"></textarea>
                        <button type="submit" name="submit_rating" class="hover:underline <?php if($user_rated_value === 'downvote') echo 'text-red-600'; else echo 'text-gray-600'; ?>">
                            <i class="fas fa-thumbs-down"></i> Downvote
                        </button>
                    </div>
                </form>
            </div>

            <!-- Bookmark Button -->
            <div class="flex items-center">
                <form method="post" action="">
                    <input type="text" name="notes" id="notes" class="border rounded-md p-1 text-gray-600 text-xs" placeholder="Add notes"/>
                    <button type="submit" name="add_bookmark" class="hover:underline ml-2 text-blue-600">
                        <?php if($bookmarked): ?>
                            <i class="fas fa-bookmark"></i> Remove Bookmark
                        <?php else: ?>
                            <i class="far fa-bookmark"></i> Bookmark
                        <?php endif; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="manage_knowledge_base.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Knowledge Base</a>
    </div>
</div>

<!-- JavaScript for Handling Downvote Comment Visibility -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const downVoteBtn = document.querySelectorAll('form input[name="rating"]');
    downVoteBtn.forEach(btn => {
        btn.addEventListener('click', function () {
            const closestDiv = this.parentNode.querySelector('div');
            const commentInput = closestDiv.querySelector('textarea');
            if (this.value == 'downvote') {
                commentInput.classList.remove('hidden');
            } else {
                commentInput.classList.add('hidden');
                commentInput.value = "";
            }
        });
    });
});
</script>

<?php
// Include footer
require 'footer.php';
?>