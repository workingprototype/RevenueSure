<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all articles with category names and author names
$stmt = $conn->prepare("SELECT knowledge_base_articles.*, knowledge_base_categories.name as category_name, users.username as author_name
                        FROM knowledge_base_articles
                        LEFT JOIN knowledge_base_categories ON knowledge_base_articles.category_id = knowledge_base_categories.id
                        LEFT JOIN users ON knowledge_base_articles.user_id = users.id
                        ORDER BY created_at DESC");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Include header
require 'header.php';
?>
    <div class="container mx-auto p-6 fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Knowledge Base</h1>

        <!-- Add Article Button -->
        <a href="add_knowledge_base_article.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide mb-6 inline-block">
            <i class="fas fa-plus-circle mr-2"></i> Add Article
        </a>

        <!-- Article Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
           <table class="w-full text-left">
              <thead>
                   <tr>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Category</th>
                       <th class="px-4 py-2">Author</th>
                       <th class="px-4 py-2">Created At</th>
                         <th class="px-4 py-2">Updated At</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
               </thead>
                <tbody>
                    <?php if ($articles): ?>
                      <?php foreach ($articles as $article): ?>
                           <tr class="border-b">
                               <td class="px-4 py-2"><a href="view_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($article['title']); ?></a></td>
                               <td class="px-4 py-2"><?php echo htmlspecialchars($article['category_name'] ? $article['category_name'] : "Uncategorized"); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($article['author_name']); ?></td>
                                 <td class="px-4 py-2"><?php echo htmlspecialchars($article['created_at']); ?></td>
                                 <td class="px-4 py-2"><?php echo htmlspecialchars($article['updated_at'] ? $article['updated_at'] : 'N/A'); ?></td>
                             <td class="px-4 py-2">
                                 <a href="edit_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                   <a href="delete_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                              </td>
                         </tr>
                       <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                         <td colspan="5" class="px-4 py-2 text-center text-gray-600">No articles found.</td>
                         </tr>
                    <?php endif; ?>
             </tbody>
          </table>
      </div>
    </div>
<?php
require 'footer.php';
?>