<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch article details with category and author
$stmt = $conn->prepare("SELECT knowledge_base_articles.*, knowledge_base_categories.name as category_name, users.username FROM knowledge_base_articles
                      LEFT JOIN knowledge_base_categories ON knowledge_base_articles.category_id = knowledge_base_categories.id
                      INNER JOIN users ON knowledge_base_articles.user_id = users.id WHERE knowledge_base_articles.id = :id");
$stmt->bindParam(':id', $article_id);
$stmt->execute();
$article = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$article) {
    header("Location: manage_knowledge_base.php");
    exit();
}

// Fetch article ratings
$stmt = $conn->prepare("SELECT rating, comment FROM knowledge_base_article_ratings WHERE article_id = :article_id AND user_id = :user_id");
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user_rating = $stmt->fetch(PDO::FETCH_ASSOC);
   $user_rated_value = $user_rating ? $user_rating['rating'] : '';
    $user_rated_comment = $user_rating ? $user_rating['comment'] : '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_rating'])) {
        $rating = $_POST['rating'];
            $comment = $_POST['comment'];
         $stmt = $conn->prepare("INSERT INTO knowledge_base_article_ratings (article_id, user_id, rating, comment) VALUES (:article_id, :user_id, :rating, :comment)
            ON DUPLICATE KEY UPDATE rating = :rating, comment = :comment, created_at = NOW()
            ");
        $stmt->bindParam(':article_id', $article_id);
       $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':rating', $rating);
           $stmt->bindParam(':comment', $comment);
        if ($stmt->execute()) {
           $user_rated_value = $rating;
           $user_rated_comment = $comment;
             header("Location: view_knowledge_base_article.php?id=$article_id&success=true");
             exit();

        } else {
            echo "<script>alert('Error submitting rating.'); window.location.href='view_knowledge_base_article.php?id=$article_id';</script>";
        }
    }
   if(isset($_GET['success']) && $_GET['success'] == 'true'){
      $success = "Rating updated successfully!";
  }
  // Fetch bookmarks
   $stmt = $conn->prepare("SELECT * FROM knowledge_base_bookmarks WHERE user_id = :user_id AND article_id = :article_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
      $stmt->bindParam(':article_id', $article_id);
     $stmt->execute();
    $bookmarked = $stmt->fetch(PDO::FETCH_ASSOC);

   if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bookmark'])){
      $notes = $_POST['notes'];
       if($bookmarked){
              $stmt = $conn->prepare("DELETE FROM knowledge_base_bookmarks WHERE id = :id");
               $stmt->bindParam(':id', $bookmarked['id']);
                 if($stmt->execute()){
                      header("Location: view_knowledge_base_article.php?id=$article_id");
                    exit();
                  }
           } else {
                 $stmt = $conn->prepare("INSERT INTO knowledge_base_bookmarks (user_id, article_id, notes) VALUES (:user_id, :article_id, :notes)");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                  $stmt->bindParam(':article_id', $article_id);
                 $stmt->bindParam(':notes', $notes);
                 if($stmt->execute()){
                      header("Location: view_knowledge_base_article.php?id=$article_id");
                      exit();
                   }
             }
    }
// Include header
require 'header.php';
?>
 <style>
     .apple-doc {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
           max-width: 900px;
            margin: 20px auto;
             background-color: #fefefe;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 12px;
             padding: 40px 60px;
         }
       .apple-doc h1, .apple-doc h2, .apple-doc h3 {
            font-weight: 700;
           margin-bottom: 15px;
            color: #222;
            line-height: 1.3;
        }
        .apple-doc h1 {
            font-size: 2.6rem;
          
        }
         .apple-doc h2 {
            font-size: 2rem;
              border-bottom: 1px solid #eee;
               padding-bottom: 10px;
         }
            .apple-doc h3 {
               font-size: 1.5rem;
           }
       .apple-doc p {
             font-size: 1.05rem;
            color: #333;
             line-height: 1.7;
            margin-bottom: 20px;
       }
        .apple-doc blockquote {
            background: #f8f8f8;
            border-left: 4px solid #ddd;
            margin: 20px 0;
             padding: 15px;
         }
        .apple-doc a {
            color: #007aff;
             text-decoration: none;
             transition: color 0.2s;
             }
            .apple-doc a:hover {
              color: #0056b3;
            }
          .apple-doc .metadata{
                font-size: .9rem;
             color: #777;
              margin-bottom: 20px;
          }
         .apple-doc .rating-bookmark-container {
             display: flex;
           justify-content: space-between;
            align-items: center;
               margin-top: 30px;
             padding-top: 15px;
              border-top: 1px solid #eee;
        }
              .apple-doc .rating-bookmark-container button {
                  transition: all 0.3s ease;
               }
                 .apple-doc .rating-bookmark-container button:hover {
                     color: #007aff;
                }
                 .apple-doc .rating-bookmark-container textarea {
                    margin-top: 5px;
                     }
</style>
<div class="container mx-auto p-6 fade-in">
       <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
    <div class="apple-doc">
         <div class="flex justify-between items-start mb-4">
             <div>
                   <h1 class="text-3xl font-bold text-gray-800 mb-4">
                     <?php echo htmlspecialchars($article['title']); ?>
                   </h1>
                  <div class="metadata">
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($article['category_name'] ? $article['category_name'] : 'Uncategorized'); ?></p>
                         <p><strong>Author:</strong> <?php echo htmlspecialchars($article['username']); ?></p>
                           <p><strong>Last Updated:</strong> <?php echo htmlspecialchars($article['updated_at'] ? date('M d, Y H:i', strtotime($article['updated_at'])) : 'N/A'); ?></p>
                   </div>
              </div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                   <div class="flex justify-end gap-2">
                        <a href="edit_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                           <a href="delete_knowledge_base_article.php?id=<?php echo $article['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                      </div>
                <?php endif; ?>
         </div>
        <?php echo $article['content']; ?>
          <div class="rating-bookmark-container">
               <div class="flex items-center gap-2">
                     <form method="post" action="">
                        <input type="hidden" name="rating" value="upvote">
                         <input type="hidden" name="comment" value="">
                         <button type="submit" name="submit_rating" class="hover:underline <?php if($user_rated_value === 'upvote') echo 'text-green-600'; else echo 'text-gray-600' ?>">
                                <i class="fas fa-thumbs-up"></i> Upvote
                            </button>
                      </form>
                      <form method="post" action="">
                        <input type="hidden" name="rating" value="downvote">
                          <div class="flex gap-2">
                            <textarea name="comment" id="comment" class="border rounded-md p-1 text-gray-600 text-xs <?php if($user_rated_value !== 'downvote') echo 'hidden'; ?>" placeholder="Give a reason"><?php echo htmlspecialchars($user_rated_comment) ?></textarea>
                             <button type="submit" name="submit_rating" class="hover:underline <?php if($user_rated_value === 'downvote') echo 'text-red-600'; else echo 'text-gray-600' ?>">
                               <i class="fas fa-thumbs-down"></i> Downvote
                           </button>
                       </div>
                  </form>
              </div>
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
    <div class="mt-6 text-center">
    <p class="text-gray-600">Still can't find what you're looking for?</p>
    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-2"><i class="fas fa-headset mr-2"></i>Contact Support</button>
     <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 mt-2"><i class="fas fa-comment-dots mr-2"></i>Live Chat</button>
    </div>
        <div class="mt-4">
          <a href="manage_knowledge_base.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back To Knowledge Base</a>
        </div>
</div>
 <script>
    document.addEventListener('DOMContentLoaded', function () {
       const downVoteBtn = document.querySelectorAll('form input[name="rating"]');
      downVoteBtn.forEach(btn => {
           btn.addEventListener('click', function () {
               const closestDiv = this.parentNode.querySelector('div');
                 const commentInput = closestDiv.querySelector('textarea');
                if(this.value == 'downvote' ){
                  commentInput.classList.remove('hidden');
                    }else {
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