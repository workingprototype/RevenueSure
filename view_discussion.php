<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$discussion_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch discussion details with creator's username
$stmt = $conn->prepare("SELECT discussions.*, users.username FROM discussions INNER JOIN users ON discussions.user_id = users.id WHERE discussions.id = :discussion_id");
$stmt->bindParam(':discussion_id', $discussion_id);
$stmt->execute();
$discussion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discussion) {
    header("Location: manage_discussions.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch all messages for this discussion in tree order
$stmt = $conn->prepare("SELECT discussion_messages.*, users.username FROM discussion_messages INNER JOIN users ON discussion_messages.user_id = users.id WHERE discussion_id = :discussion_id ORDER BY sent_at ASC");
$stmt->bindParam(':discussion_id', $discussion_id);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch participants
$stmt = $conn->prepare("SELECT discussion_participants.*, users.username FROM discussion_participants LEFT JOIN users ON discussion_participants.participant_id = users.id  WHERE discussion_id = :discussion_id AND participant_type = 'user'");
$stmt->bindParam(':discussion_id', $discussion_id);
$stmt->execute();
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT discussion_participants.*, employees.name FROM discussion_participants LEFT JOIN employees ON discussion_participants.participant_id = employees.id  WHERE discussion_id = :discussion_id AND participant_type = 'employee'");
$stmt->bindParam(':discussion_id', $discussion_id);
$stmt->execute();
$employee_participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
if($employee_participants){
    $participants = array_merge($participants, $employee_participants);
}
    $stmt = $conn->prepare("SELECT discussion_participants.*, customers.name FROM discussion_participants LEFT JOIN customers ON discussion_participants.participant_id = customers.id  WHERE discussion_id = :discussion_id AND participant_type = 'customer'");
       $stmt->bindParam(':discussion_id', $discussion_id);
    $stmt->execute();
      $customer_participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($customer_participants){
            $participants = array_merge($participants, $customer_participants);
         }
    $error = '';
$success = '';
 if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
        $message = $_POST['message'];
         $parent_id = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
      if (!empty($message)) {
            $stmt = $conn->prepare("INSERT INTO discussion_messages (discussion_id, user_id, message, parent_id) VALUES (:discussion_id, :user_id, :message, :parent_id)");
             $stmt->bindParam(':discussion_id', $discussion_id);
             $stmt->bindParam(':user_id', $_SESSION['user_id']);
             $stmt->bindParam(':message', $message);
              $stmt->bindParam(':parent_id', $parent_id);
             if ($stmt->execute()) {
                  // Update Last Viewed
                  $stmt = $conn->prepare("UPDATE discussion_participants SET last_viewed = NOW() WHERE discussion_id = :discussion_id AND participant_id = :user_id");
                     $stmt->bindParam(':discussion_id', $discussion_id);
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->execute();

                     $success = "Message added successfully!";
                     header("Location: view_discussion.php?id=$discussion_id&success=true");
                      exit();
               } else {
                      $error = "Error adding comment.";
                 }
        }else{
            $error = "Message cannot be empty";
         }
    }
    if(isset($_GET['success']) && $_GET['success'] == 'true'){
           $success = "Discussion updated successfully!";
      }

//Update last viewed timestamp.
$stmt = $conn->prepare("UPDATE discussion_participants SET last_viewed = NOW() WHERE discussion_id = :discussion_id AND participant_id = :user_id");
$stmt->bindParam(':discussion_id', $discussion_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
// Fetch unread messages count for this discussion
    $stmt = $conn->prepare("SELECT COUNT(*) as unread_count FROM discussion_messages LEFT JOIN discussion_participants ON discussion_messages.discussion_id = discussion_participants.discussion_id  WHERE discussion_messages.discussion_id = :discussion_id AND discussion_messages.user_id != :user_id AND discussion_messages.sent_at > (SELECT last_viewed FROM discussion_participants WHERE participant_id = :user_id2  AND discussion_id = :discussion_id2) OR (SELECT COUNT(*) FROM discussion_participants WHERE participant_id = :user_id3  AND discussion_id = :discussion_id3 AND last_viewed IS NULL) > 0");
    $stmt->bindParam(':discussion_id', $discussion_id);
     $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':user_id2', $user_id);
    $stmt->bindParam(':discussion_id2', $discussion_id);
     $stmt->bindParam(':user_id3', $user_id);
    $stmt->bindParam(':discussion_id3', $discussion_id);
     $stmt->execute();
     $unread_count = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

// Include header
require 'header.php';
?>
 <div class="container mx-auto p-6 fade-in">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Discussion: <?php echo htmlspecialchars($discussion['title']); ?></h1>
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

           <div class="bg-white border-2 border-gray-200 p-6 rounded-lg mb-8">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-gray-700"><strong>Created By:</strong> <?php echo htmlspecialchars($discussion['username']); ?></p>
                          <?php if($participants): ?>
                                 <p class="text-gray-700">
                                      <strong>Participants:</strong>
                                     <?php
                                       foreach($participants as $i => $participant){
                                           if($i < 2){
                                               if(isset($participant['name'])){
                                                   echo htmlspecialchars($participant['name']) . ($i < count($participants)-1 ? ',' : '');
                                              } else {
                                                 echo htmlspecialchars($participant['username']) . ($i < count($participants)-1 ? ',' : '');
                                                 }
                                                } else if ($i == 2){
                                                 echo '...';
                                                }
                                       }
                                          ?>
                                </p>
                               <?php else: ?>
                                   <p class="text-gray-700">No participants</p>
                             <?php endif; ?>
                </div>
            <!-- Message Area-->
            <div class="bg-white border border-gray-100 p-6 rounded-lg mb-8">
               <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                    Messages <span class="text-xs text-gray-500">(<?php echo $unread_count; ?> unread)</span>
               </h2>
                    <form method="POST" action="" class="mb-4">
                       <input type="hidden" name="parent_id" id="parent_id">
                         <textarea name="message" id="message" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Type your message..."></textarea>
                         <button type="submit" name="send_message" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4">Send</button>
                    </form>
                    <?php if($messages): ?>
                        <ul id="message-list">
                          <?php
                                function displayMessages($messages, $parentId = null, $level = 0) {
                                   foreach ($messages as $message) {
                                      if ($message['parent_id'] == $parentId) {
                                          $indent = str_repeat('   ', $level);
                                        echo '<li class="p-4 border-b border-gray-100 my-2 bg-gray-50 rounded-lg" style="margin-left: ' . ($level*20) . 'px;" data-comment-id="' . $message['id'] . '" >
                                                 <div class="flex justify-between items-center mb-2">
                                                     <p class="text-gray-800">' . $indent . htmlspecialchars($message['message']) . '</p>
                                                 </div>
                                               <div class="text-right">
                                                    <p class="text-gray-500 text-sm">
                                                       <i class="fas fa-user-circle mr-1"></i> <a href="profile.php">'. htmlspecialchars($message['username']) .'</a> - '. htmlspecialchars(date('Y-m-d H:i', strtotime($message['sent_at']))) . '</p>
                                                  </div>
                                                   <div class="flex justify-end gap-2">
                                                        <button onclick="replyToMessage('. $message['id'] .')" class="text-blue-600 hover:underline">Reply</button>
                                                </div>
                                                
                                          ';
                                      displayMessages($messages, $message['id'], $level + 1);
                                      echo '</li>';
                                         }
                                     }
                                 }
                                    displayMessages($messages);
                                ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-gray-600">No messages yet!</p>
                    <?php endif; ?>
                </div>
           </div>
        <div class="mt-6 flex justify-center">
                <a href="manage_discussions.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Discussions</a>
         </div>
</div>
  <script>
    function replyToMessage(commentId) {
       document.getElementById('parent_id').value = commentId;
        document.getElementById('message').focus();
    }
 </script>
<?php
// Include footer
require 'footer.php';
?>