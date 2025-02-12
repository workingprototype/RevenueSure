<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$feature_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch feature details
$stmt = $conn->prepare("SELECT project_features.*, users.username FROM project_features JOIN users ON project_features.owner_id = users.id WHERE project_features.id = :feature_id");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$feature = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$feature) {
    header("Location: " . BASE_URL . "projects/manage");
    exit();
}

// Fetch attachments for the project features
$stmt = $conn->prepare("SELECT * FROM feature_attachments WHERE feature_id = :feature_id");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch comments for the project features
$stmt = $conn->prepare("SELECT feature_comments.*, users.username FROM feature_comments INNER JOIN users ON feature_comments.user_id = users.id WHERE feature_id = :feature_id ORDER BY created_at ASC");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     if (isset($_POST['add_comment'])) {
            $comment = $_POST['comment'];
              if (!empty($comment)) {
                $stmt = $conn->prepare("INSERT INTO feature_comments (feature_id, user_id, comment) VALUES (:feature_id, :user_id, :comment)");
                $stmt->bindParam(':feature_id', $feature_id);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->bindParam(':comment', $comment);
                if ($stmt->execute()) {
                     $success = "Comment added successfully!";
                       header("Location: " . BASE_URL . "projects/features/view?id=$feature_id&success=true");
                        exit();
                } else {
                    $error = "Error adding comment.";
                 }
              }  else{
                 $error = "Comment cannot be empty";
             }
        }
}


?>
 <div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Feature Details</h1>
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Feature Information</h2>
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
        <p><strong>Feature Title:</strong> <?php echo htmlspecialchars($feature['feature_title']); ?></p>
          <p><strong>Description:</strong> <?php echo htmlspecialchars($feature['description']); ?></p>
         <p><strong>Priority:</strong> <?php echo htmlspecialchars($feature['priority']); ?></p>
          <p><strong>Status:</strong> <?php echo htmlspecialchars($feature['status']); ?></p>
        <p><strong>Owner:</strong> <?php echo htmlspecialchars($feature['username']); ?></p>
        <p><strong>Estimated Completion Date:</strong> <?php echo htmlspecialchars($feature['estimated_completion_date'] ? $feature['estimated_completion_date'] : 'N/A'); ?></p>
             <p><strong>Actual Completion Date:</strong> <?php echo htmlspecialchars($feature['actual_completion_date'] ? $feature['actual_completion_date'] : 'N/A'); ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Attachments</h2>
     <?php if($attachments): ?>
         <ul>
                <?php foreach ($attachments as $attachment): ?>
                    <li class="mb-2">
                      <a href="<?php echo BASE_URL; ?><?php echo $attachment['file_path']; ?>" download><?php echo htmlspecialchars($attachment['file_name']); ?></a>
                   </li>
               <?php endforeach; ?>
         </ul>
         <?php else: ?>
             <p>No attachments yet!</p>
          <?php endif; ?>
    </div>
     <div class="bg-white p-6 rounded-lg shadow-md">
         <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
            Comments
          </h2>
           <form method="POST" action="">
           <?php echo csrfTokenInput(); ?>
                <textarea name="comment" id="comment" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Write a comment..."></textarea>
                  <button type="submit" name="add_comment" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4">Add Comment</button>
           </form>
             <?php if($comments): ?>
                   <h2 class="text-xl font-bold text-gray-800 mt-6 mb-4">Comments</h2>
                    <ul id="comment-list">
                        <?php
                            foreach ($comments as $comment) {
                                 echo '<li class="p-4 border-b border-gray-100 my-2 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between items-center mb-2">
                                          <p class="text-gray-800">' . htmlspecialchars($comment['comment']) . '</p>
                                       </div>
                                        <div class="text-right">
                                                <p class="text-gray-500 text-sm">
                                                <i class="fas fa-user-circle mr-1"></i> '. htmlspecialchars($comment['username']) .' - '. htmlspecialchars(date('Y-m-d H:i', strtotime($comment['created_at']))) . '</p>
                                        </div>
                                  ';
                                 echo '</li>';
                                    }
                             ?>
                       </ul>
                  <?php else: ?>
                      <p class="text-gray-600">No comments yet!</p>
                   <?php endif; ?>
        </div>
    <div class="mt-4">
     <a href="<?php echo BASE_URL; ?>projects/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Project</a>
    </div>
</div>