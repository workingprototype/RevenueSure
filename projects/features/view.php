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

$stmt = $conn->prepare("SELECT * FROM feature_attachments WHERE feature_id = :feature_id");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_comment'])) {
        $comment = $_POST['comment'];
        if (!empty($comment)) {
            $stmt = $conn->prepare("INSERT INTO feature_comments (feature_id, user_id, comment) VALUES (:feature_id, :user_id, :comment)");
            $stmt->bindParam(':feature_id', $feature_id);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':comment', $comment);

            if($stmt->execute()) {
              $success = "Comment added successfully!";
            } else {
              $error = "Error adding comment.";
            }
         } else {
            $error = "Comment cannot be empty.";
         }
    }elseif (isset($_POST['add_subtask'])) {
        $subtask_title = trim($_POST['subtask_title']);
         $subtask_description = $_POST['subtask_description'];
         $subtask_due_date = $_POST['subtask_due_date'];
        $subtask_assigned_to = $_POST['subtask_assigned_to'];

         if (empty($subtask_title) || empty($subtask_description) || empty($subtask_due_date)) {
            $error = "All subtask fields are required.";
         } else {
                 $stmt = $conn->prepare("INSERT INTO feature_subtasks (feature_id, title, description, due_date, assigned_to) VALUES (:feature_id, :title, :description, :due_date, :assigned_to)");
                $stmt->bindParam(':feature_id', $feature_id);
               $stmt->bindParam(':title', $subtask_title);
                $stmt->bindParam(':description', $subtask_description);
                 $stmt->bindParam(':due_date', $subtask_due_date);
                $stmt->bindParam(':assigned_to', $subtask_assigned_to);
             if($stmt->execute()) {
                $success = "Subtask added successfully!";
            } else {
                  $error = "Error adding subtask.";
             }
          }
    }  elseif (isset($_POST['add_resource'])) {
        $resource_type = $_POST['resource_type'];
        $estimated_value = $_POST['estimated_value'];
        $actual_value = $_POST['actual_value'];
        $notes = trim($_POST['notes']);
          if (empty($resource_type) || empty($estimated_value)) {
                $error = "All fields are required.";
            } else {
                $stmt = $conn->prepare("INSERT INTO feature_resources (feature_id, resource_type, estimated_value, actual_value, notes) VALUES (:feature_id, :resource_type, :estimated_value, :actual_value, :notes)");
                $stmt->bindParam(':feature_id', $feature_id);
                $stmt->bindParam(':resource_type', $resource_type);
                $stmt->bindParam(':estimated_value', $estimated_value);
                $stmt->bindParam(':actual_value', $actual_value);
                  $stmt->bindParam(':notes', $notes);

               if ($stmt->execute()) {
                   $success = "Resource added successfully!";
                } else {
                    $error = "Error adding resource.";
               }
           }
        
    }

    elseif (isset($_POST['add_comment'])) {
           $comment = $_POST['comment'];
            if(!empty($comment)){
               $stmt = $conn->prepare("INSERT INTO feature_comments (feature_id, user_id, comment) VALUES (:feature_id, :user_id, :comment)");
               $stmt->bindParam(':feature_id', $feature_id);
               $stmt->bindParam(':user_id', $_SESSION['user_id']);
               $stmt->bindParam(':comment', $comment);

               if($stmt->execute()) {
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

 // Fetch subtasks for the feature
$stmt = $conn->prepare("SELECT feature_subtasks.*, users.username as assignee_name FROM feature_subtasks LEFT JOIN users ON feature_subtasks.assigned_to = users.id WHERE feature_id = :feature_id ORDER BY due_date ASC");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$subtasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count Completed Subtasks
$stmt = $conn->prepare("SELECT COUNT(*) as completed_count FROM feature_subtasks WHERE feature_id = :feature_id AND status = 'Completed'");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$completed_count = $stmt->fetch(PDO::FETCH_ASSOC)['completed_count'] ?? 0;

// Fetch Resources allocated for the feature
$stmt = $conn->prepare("SELECT * FROM feature_resources WHERE feature_id = :feature_id ORDER BY created_at ASC");
$stmt->bindParam(':feature_id', $feature_id);
$stmt->execute();
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Progress percentage
$total_subtasks = count($subtasks);
$progress_percentage = ($total_subtasks > 0) ? round(($completed_count / $total_subtasks) * 100) : 0;

 // Fetch users for assignee dropdown
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role = 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
  
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Feature Details: <?php echo htmlspecialchars($feature['feature_title']); ?></h1>

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
          <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Feature Information</h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($feature['description']); ?></p>
          <!--Display Progress-->
             <p class="mt-4 text-gray-600">
                <strong>Progress:</strong>
                <div class="overflow-hidden h-2 text-xs flex rounded bg-blue-100">
                    <div style="width:<?php echo $progress_percentage; ?>%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"></div>
                </div>
                <span class="text-gray-600"><?php echo $completed_count; ?>/<?php echo $total_subtasks; ?> Subtasks Completed (<?php echo $progress_percentage; ?>%)</span>
         </p>
          
              </div>
                 <!--ADD TASK SECTION-->
                 <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Add New Subtask</h2>
                     <form method="POST" action="">
                     <?php echo csrfTokenInput(); ?>
                         <div class="mb-4">
                             <label for="subtask_title" class="block text-gray-700">Title</label>
                            <input type="text" name="subtask_title" id="subtask_title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                         </div>
                        <div class="mb-4">
                            <label for="subtask_description" class="block text-gray-700">Description</label>
                           <textarea name="subtask_description" id="subtask_description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                       </div>
                      <div class="mb-4">
                        <label for="subtask_due_date" class="block text-gray-700">Due Date</label>
                          <input type="datetime-local" name="subtask_due_date" id="subtask_due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                       </div>
                       <div class="mb-4">
                            <label for="subtask_assigned_to" class="block text-gray-700">Assigned To</label>
                             <select name="subtask_assigned_to" id="subtask_assigned_to" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                                  <option value="">Select User</option>
                                   <?php foreach ($users as $user): ?>
                                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                     <?php endforeach; ?>
                            </select>
                        </div>
                      <button type="submit" name="add_subtask" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Subtask</button>
                 </form>
                   <?php if ($subtasks): ?>
                      <h2 class="text-xl font-bold text-gray-800 mt-6 mb-4">Subtasks</h2>
                       <ul>
                           <?php
                              foreach ($subtasks as $subtask) {
                                     echo '<li class="p-4 border-b border-gray-100 my-2 bg-gray-50 rounded-lg">
                                               <div class="flex justify-between items-center mb-2">
                                                <p class="text-gray-800">' . htmlspecialchars($subtask['title']) . '</p>
                                                   <p class="text-gray-800">Assigned to: '. htmlspecialchars($subtask['assignee_name']) . '</p>
                                              </div>
                                                <div class="text-right">
                                                        <p class="text-gray-500 text-sm">
                                                        <i class="fas fa-user-circle mr-1"></i> Due Date: '. htmlspecialchars(date('Y-m-d H:i', strtotime($subtask['due_date']))) . '</p>
                                                </div>
                                           ';
                                        echo '</li>';
                                         }
                                 ?>
                       </ul>
                 <?php else: ?>
                  <p class="text-gray-600">No task for the functions </p>
                <?php endif; ?>
                   
            </div>
  <!----ADD RESOURCES TO SPECIFIC TASKS----->
            <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                 <h2 class="text-xl font-bold text-gray-800 mb-4">Add Resources</h2>
                  <form method="POST" action="">
                  <?php echo csrfTokenInput(); ?>
                       <div class="mb-4">
                            <label for="resource_type" class="block text-gray-700">Resource Type</label>
                             <select name="resource_type" id="resource_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                                    <option value="Hours">Hours</option>
                                      <option value="Budget">Budget</option>
                               </select>
                        </div>
                        <div class="mb-4">
                            <label for="estimated_value" class="block text-gray-700">Estimated Value</label>
                             <input type="number" name="estimated_value" id="estimated_value" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>
                         <div class="mb-4">
                            <label for="actual_value" class="block text-gray-700">Actual Value</label>
                            <input type="number" name="actual_value" id="actual_value" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>
                         <div class="mb-4">
                           <label for="notes" class="block text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                         </div>
                          <button type="submit" name="add_resource" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Resource</button>
                  </form>
        <?php if($resources): ?>
               <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Resources</h2>
                      <ul>
                          <?php
                            foreach ($resources as $resource) {
                                     echo '<li class="p-4 border-b border-gray-100 my-2 bg-gray-50 rounded-lg">
                                             <div class="flex justify-between items-center mb-2">
                                                 <p class="text-gray-800">'. htmlspecialchars($resource['resource_type']) . '</p>
                                                 <p class="text-gray-500 text-sm">
                                                         <i class="fas fa-dollar-circle mr-1"></i> $'. htmlspecialchars($resource['estimated_value']) .' - <i class="fas fa-user-circle mr-1"></i> $'.htmlspecialchars($resource['actual_value']).'</p>
                                              </div>
                                       ';
                                    echo '</li>';
                                  }
                            ?>
                      </ul>
                </div>
                
         <?php else: ?>
           <p class="text-gray-600">No comments added yet to the features!</p>
         <?php endif; ?>
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
                 <form method="POST" action="" class="mb-4">
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
        <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $feature_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Project</a>
    </div>
</div>