<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

// Get article id from GET
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch article details along with category and author information
$sql = "SELECT a.*, c.name AS category_name, u.username
        FROM knowledge_base_articles a
        LEFT JOIN knowledge_base_categories c ON a.category_id = c.id
        INNER JOIN users u ON a.user_id = u.id
        WHERE a.id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $article_id, PDO::PARAM_INT);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if article is not found or access is not allowed
if (!$article) {
    header("Location: " . BASE_URL . "knowledge_base/manage");
    exit();
}
if ($article['access_level'] === 'private' && $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "knowledge_base/manage");
    exit();
}

// Increment view count
$stmt = $conn->prepare("UPDATE knowledge_base_articles SET view_count = view_count + 1 WHERE id = :id");
$stmt->bindParam(':id', $article_id, PDO::PARAM_INT);
$stmt->execute();

// Initialize rating variables
$user_rated_value = '';
$user_rated_comment = '';

// Fetch article rating for the current user
$stmt = $conn->prepare("SELECT rating, comment FROM knowledge_base_article_ratings WHERE article_id = :article_id AND user_id = :user_id");
$stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user_rating = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user_rating) {
    $user_rated_value   = $user_rating['rating'];
    $user_rated_comment = $user_rating['comment'];
}

// Process rating form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $rating  = $_POST['rating'];
    $comment = $_POST['comment'] ?? '';
    $stmt = $conn->prepare("
        INSERT INTO knowledge_base_article_ratings (article_id, user_id, rating, comment)
        VALUES (:article_id, :user_id, :rating, :comment)
        ON DUPLICATE KEY UPDATE rating = :rating, comment = :comment, created_at = NOW()
    ");
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':comment', $comment);
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "knowledge_base/view?id={$article_id}&success=true");
        exit();
    } else {
        echo "<script>
                alert('Error submitting rating.');
                window.location.href='knowledge_base/view?id={$article_id}';
              </script>";
        exit();
    }
}

// Process bookmark form submission
// First, check if the article is already bookmarked
$stmt = $conn->prepare("SELECT * FROM knowledge_base_bookmarks WHERE user_id = :user_id AND article_id = :article_id");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
$stmt->execute();
$bookmarked = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bookmark'])) {
    $notes = $_POST['notes'] ?? '';
    if ($bookmarked) {
        // Remove bookmark if already exists
        $stmt = $conn->prepare("DELETE FROM knowledge_base_bookmarks WHERE id = :id");
        $stmt->bindParam(':id', $bookmarked['id'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "knowledge_base/view?id={$article_id}");
            exit();
        }
    } else {
        // Add new bookmark
        $stmt = $conn->prepare("INSERT INTO knowledge_base_bookmarks (user_id, article_id, notes) VALUES (:user_id, :article_id, :notes)");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':notes', $notes);
        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "knowledge_base/view?id={$article_id}");
            exit();
        }
    }
}

// Check for success message in GET parameter
$success = '';
if (isset($_GET['success']) && $_GET['success'] === 'true') {
    $success = "Rating updated successfully!";
}


?>
<!-- Inline CSS for page styling (can be moved to a separate file) -->
<style>
    .apple-doc {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        max-width: 900px;
        margin: 20px auto;
        padding: 40px 60px;
        background-color: #fefefe;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 12px;
        line-height: 1.7;
        font-size: 16px;
    }
    .apple-doc h1, .apple-doc h2, .apple-doc h3, .apple-doc h4, .apple-doc h5, .apple-doc h6 {
        font-family: 'Roboto Slab', serif;
        margin-bottom: 15px;
        line-height: 1.4;
        color: #333;
        font-weight: 700;
    }
    .apple-doc h1 { font-size: 2.6rem; }
    .apple-doc h2 { font-size: 2rem; border-bottom: 1px solid #eee; padding-bottom: 6px; }
    .apple-doc h3 { font-size: 1.5rem; }
    .apple-doc h4 { font-size: 1.5rem; }
    .apple-doc h5 { font-size: 1.25rem; }
    .apple-doc h6 { font-size: 1.1rem; }
    .apple-doc a {
        color: #0056b3;
        text-decoration: none;
        border-bottom: 1px solid transparent;
        transition: border-bottom 0.3s ease;
    }
    .apple-doc a:hover { border-bottom: 1px solid #0056b3; }
    .apple-doc p { margin-bottom: 15px; }
    .apple-doc ol, .apple-doc ul { padding-left: 25px; margin-bottom: 15px; }
    .apple-doc ul li { list-style-type: disc; margin-bottom: 10px; }
    .apple-doc ol li { list-style-type: decimal; margin-bottom: 10px; }
    .apple-doc blockquote {
        margin: 20px 0;
        padding: 15px 20px;
        border-left: 4px solid #c0c0c0;
        font-style: italic;
        color: #555;
        background-color: #fafafa;
    }
    .rating-bookmark-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }
    .rating-bookmark-container button {
        transition: all 0.3s ease;
    }
    .rating-bookmark-container button:hover { color: #0056b3; }
    .rating-bookmark-container textarea { margin-top: 5px; }
</style>

<div class="container mx-auto p-6 fade-in">
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <div class="flex items-start mb-4">
        <div class="apple-doc flex-1">
            <!-- Article Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h1>
                    <div class="metadata">
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category_name'] ?: 'Uncategorized'); ?></p>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($article['username']); ?></p>
                        <p>
                            <strong>Last Updated:</strong>
                            <?php echo htmlspecialchars($article['updated_at'] ? date('M d, Y H:i', strtotime($article['updated_at'])) : 'N/A'); ?>
                        </p>
                        <p><strong>Views:</strong> <?php echo htmlspecialchars($article['view_count'] ?: 0); ?></p>
                    </div>
                </div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div class="flex justify-end gap-2">
                        <a href="<?php echo BASE_URL; ?>knowledge_base/edit?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                        <a href="<?php echo BASE_URL; ?>knowledge_base/delete?id=<?php echo $article['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Article Content -->
            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>

            <!-- Rating and Bookmark Section -->
            <div class="rating-bookmark-container">
                <div class="flex items-center gap-2">
                    <!-- Upvote Form -->
                    <form method="POST" action="">
                    <?php echo csrfTokenInput(); ?>
                        <input type="hidden" name="rating" value="upvote">
                        <input type="hidden" name="comment" value="">
                        <button type="submit" name="submit_rating" class="hover:underline <?php echo ($user_rated_value === 'upvote') ? 'text-green-600' : 'text-gray-600'; ?>">
                            <i class="fas fa-thumbs-up"></i> Upvote
                        </button>
                    </form>
                    <!-- Downvote Form -->
                    <form method="POST" action="">
                    <?php echo csrfTokenInput(); ?>
                        <input type="hidden" name="rating" value="downvote">
                        <div class="flex gap-2">
                            <textarea name="comment" id="comment" class="border rounded-md p-1 text-gray-600 text-xs <?php echo ($user_rated_value !== 'downvote') ? 'hidden' : ''; ?>" placeholder="Give a reason"><?php echo htmlspecialchars($user_rated_comment); ?></textarea>
                            <button type="submit" name="submit_rating" class="hover:underline <?php echo ($user_rated_value === 'downvote') ? 'text-red-600' : 'text-gray-600'; ?>">
                                <i class="fas fa-thumbs-down"></i> Downvote
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex items-center">
                    <!-- Bookmark Form -->
                    <form method="POST" action="">
                    <?php echo csrfTokenInput(); ?>
                        <input type="text" name="notes" id="notes" class="border rounded-md p-1 text-gray-600 text-xs" placeholder="Add notes" />
                        <button type="submit" name="add_bookmark" class="hover:underline ml-2 text-blue-600">
                            <?php if ($bookmarked): ?>
                                <i class="fas fa-bookmark"></i> Remove Bookmark
                            <?php else: ?>
                                <i class="far fa-bookmark"></i> Bookmark
                            <?php endif; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Support and Navigation Buttons -->
    <div class="mt-6 flex justify-center gap-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
            <i class="fas fa-headset mr-2"></i>Contact Support
        </button>
        <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">
            <i class="fas fa-comment-dots mr-2"></i>Live Chat
        </button>
    </div>
    <div class="mt-4 flex justify-center gap-2">
        <a href="<?php echo BASE_URL; ?>knowledge_base/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Knowledge Base</a>
        <a href="<?php echo BASE_URL; ?>knowledge_base/request/add" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 inline-block">Request Article</a>
    </div>
</div>

<!-- Inline JavaScript for toggling downvote comment box (can be moved to a separate JS file) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ratingButtons = document.querySelectorAll('form input[name="rating"]');
    ratingButtons.forEach(button => {
        button.addEventListener('click', function () {
            const formDiv = this.parentNode.querySelector('div');
            if (formDiv) {
                const commentInput = formDiv.querySelector('textarea');
                if (this.value === 'downvote') {
                    commentInput.classList.remove('hidden');
                } else {
                    commentInput.classList.add('hidden');
                    commentInput.value = "";
                }
            }
        });
    });
});
</script>


