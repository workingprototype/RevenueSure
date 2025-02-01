<?php
ini_set('display_errors' , 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all discussions
$stmt = $conn->prepare("SELECT discussions.*, users.username FROM discussions INNER JOIN users ON discussions.user_id = users.id ORDER BY created_at DESC");
$stmt->execute();
$discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch participants for each discussion
$all_participants = [];
foreach ($discussions as $discussion) {
    $stmt = $conn->prepare("SELECT discussion_participants.*, users.username FROM discussion_participants LEFT JOIN users ON discussion_participants.participant_id = users.id  WHERE discussion_id = :discussion_id AND participant_type = 'user'");
    $stmt->bindParam(':discussion_id', $discussion['id']);
    $stmt->execute();
    $all_participants[$discussion['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);

     $stmt = $conn->prepare("SELECT discussion_participants.*, employees.name FROM discussion_participants LEFT JOIN employees ON discussion_participants.participant_id = employees.id  WHERE discussion_id = :discussion_id AND participant_type = 'employee'");
    $stmt->bindParam(':discussion_id', $discussion['id']);
    $stmt->execute();
      $employee_participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($employee_participants){
            $all_participants[$discussion['id']] = array_merge($all_participants[$discussion['id']], $employee_participants);
          }
       $stmt = $conn->prepare("SELECT discussion_participants.*, customers.name FROM discussion_participants LEFT JOIN customers ON discussion_participants.participant_id = customers.id  WHERE discussion_id = :discussion_id AND participant_type = 'customer'");
    $stmt->bindParam(':discussion_id', $discussion['id']);
     $stmt->execute();
    $customer_participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($customer_participants){
      $all_participants[$discussion['id']] = array_merge($all_participants[$discussion['id']], $customer_participants);
        }
}
 // Calculate unread messages
$unread_counts = [];
foreach ($discussions as $discussion) {
    $stmt = $conn->prepare("SELECT COUNT(*) as unread_count FROM discussion_messages LEFT JOIN discussion_participants ON discussion_messages.discussion_id = discussion_participants.discussion_id  WHERE discussion_messages.discussion_id = :discussion_id AND discussion_messages.user_id != :user_id AND discussion_messages.sent_at > (SELECT last_viewed FROM discussion_participants WHERE participant_id = :user_id2  AND discussion_participants.discussion_id = :discussion_id2) OR (SELECT COUNT(*) FROM discussion_participants WHERE participant_id = :user_id3  AND discussion_participants.discussion_id = :discussion_id3 AND last_viewed IS NULL) > 0");
    $stmt->bindParam(':discussion_id', $discussion['id']);
     $stmt->bindParam(':user_id', $user_id);
      $stmt->bindParam(':user_id2', $user_id);
        $stmt->bindParam(':discussion_id2', $discussion['id']);
         $stmt->bindParam(':user_id3', $user_id);
      $stmt->bindParam(':discussion_id3', $discussion['id']);
    $stmt->execute();
    $unread_counts[$discussion['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
    
    $stmt = $conn->prepare("SELECT MAX(sent_at) as last_message_time FROM discussion_messages WHERE discussion_id = :discussion_id");
    $stmt->bindParam(':discussion_id', $discussion['id']);
    $stmt->execute();
    $last_message_times[$discussion['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['last_message_time'];
}

function categorize_discussion($discussion, $all_participants, $unread_counts, $last_message_times, $user_id) {
    $current_timestamp = new DateTime();
    $created_at = new DateTime($discussion['created_at']);
       $days_difference = $current_timestamp->diff($created_at)->days;
    $has_unread_messages = $unread_counts[$discussion['id']] > 0;
    $last_message_time = $last_message_times[$discussion['id']];
       $days_since_last_message = 0;
        if ($last_message_time) {
             $last_message_time_dt = new DateTime($last_message_time);
            $days_since_last_message = $current_timestamp->diff($last_message_time_dt)->days;
         }

    $is_ongoing = false;
      if($last_message_time && $days_since_last_message <= 7){
         $is_ongoing = true;
        }
     // Check if the user who created discussion replied in the thread or not. if they replied and there are unread messages, its an ongoing discussion
      $stmt = $GLOBALS['conn']->prepare("SELECT id FROM discussion_messages WHERE discussion_id = :discussion_id AND user_id = :user_id AND parent_id IS NULL");
          $stmt->bindParam(':discussion_id', $discussion['id']);
          $stmt->bindParam(':user_id', $discussion['user_id']);
        $stmt->execute();
     $initial_message_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        
         $has_initial_message_reply = 0;
          if($initial_message_id){
                 $stmt =  $GLOBALS['conn']->prepare("SELECT id FROM discussion_messages WHERE discussion_id = :discussion_id AND user_id != :user_id AND parent_id = :initial_message_id");
                     $stmt->bindParam(':discussion_id', $discussion['id']);
                     $stmt->bindParam(':user_id', $discussion['user_id']);
                      $stmt->bindParam(':initial_message_id', $initial_message_id);
                      $stmt->execute();
                      $has_initial_message_reply  = $stmt->fetch(PDO::FETCH_ASSOC);

          }


  if ($has_unread_messages && !$has_initial_message_reply) {
            return 'New Discussions';
     } else if ($has_unread_messages || $is_ongoing){
            return 'Ongoing Discussions';
       } else if ($discussion['status'] === 'closed') {
           return 'Previous Discussions';
      } else {
         return 'Ongoing Discussions';
       }
}

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
 <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Discussions</h1>
    <div class="flex justify-between items-center mb-8">
         <a href="add_discussion.php" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
             <i class="fas fa-plus-circle mr-2"></i> Create New Discussion
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">New Discussions</h2>
                 <?php if ($discussions) : ?>
                  <div class="flex flex-col gap-4">
                     <?php foreach ($discussions as $discussion): ?>
                            <?php if (categorize_discussion($discussion, $all_participants, $unread_counts, $last_message_times, $user_id) == 'New Discussions'): ?>
                            <a href="view_discussion.php?id=<?php echo $discussion['id']; ?>" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center hover:shadow-lg transition duration-300 border-l-4 border-blue-500">
                                <div class="flex-1">
                                       <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($discussion['title']); ?></p>
                                         <p class="text-gray-500 text-sm">
                                       Created By: <?php echo htmlspecialchars($discussion['username']); ?> ,
                                       <?php $participants = $all_participants[$discussion['id']];
                                        if ($participants){
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

                                            } else {
                                                echo "No Participants";
                                            }
                                         ?>
                                           </p>
                                </div>
                                 <div class="flex items-center">
                                     <?php if($unread_counts[$discussion['id']] > 0): ?>
                                             <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full mr-2"><?php echo htmlspecialchars($unread_counts[$discussion['id']]); ?></span>
                                     <?php endif; ?>
                                   <i class="fas fa-arrow-right text-gray-500"></i>
                               </div>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </div>
                 <?php else: ?>
                           <p class="text-gray-600">No new discussions yet.</p>
                <?php endif; ?>
            </div>
             <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ongoing Discussions</h2>
                  <?php if ($discussions): ?>
                     <div class="flex flex-col gap-4">
                        <?php foreach ($discussions as $discussion): ?>
                            <?php if (categorize_discussion($discussion, $all_participants, $unread_counts, $last_message_times, $user_id) == 'Ongoing Discussions'): ?>
                               <a href="view_discussion.php?id=<?php echo $discussion['id']; ?>" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center hover:shadow-lg transition duration-300 border-l-4 border-green-500">
                                    <div class="flex-1">
                                       <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($discussion['title']); ?></p>
                                       <p class="text-gray-500 text-sm">
                                                Created By: <?php echo htmlspecialchars($discussion['username']); ?> ,
                                                <?php $participants = $all_participants[$discussion['id']];
                                                  if ($participants){
                                                    foreach($participants as $i => $participant){
                                                      if($i < 2){
                                                       if(isset($participant['name'])){
                                                            echo htmlspecialchars($participant['name']) . ($i < count($participants)-1 ? ',' : '');
                                                        }else {
                                                             echo htmlspecialchars($participant['username']) . ($i < count($participants)-1 ? ',' : '');
                                                       }
                                                          } else if($i == 2){
                                                           echo '...';
                                                       }
                                                     }
                                                  } else {
                                                    echo "No Participants";
                                                 }
                                                 ?>
                                       </p>
                                         <?php if ($last_message_times[$discussion['id']] ) : ?>
                                            <p class="text-gray-500 text-sm">Last message: <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($last_message_times[$discussion['id']]))) ?></p>
                                        <?php endif; ?>
                                   </div>
                                    <div class="flex items-center">
                                         <?php if($unread_counts[$discussion['id']] > 0): ?>
                                             <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full mr-2"><?php echo htmlspecialchars($unread_counts[$discussion['id']]); ?></span>
                                         <?php endif; ?>
                                       <i class="fas fa-arrow-right text-gray-500"></i>
                                  </div>
                            </a>
                            <?php endif; ?>
                       <?php endforeach; ?>
                     </div>
                    <?php else: ?>
                         <p class="text-gray-600">No ongoing discussions yet.</p>
                      <?php endif; ?>
            </div>
              <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Previous Discussions</h2>
                 <?php if ($discussions): ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($discussions as $discussion): ?>
                            <?php if (categorize_discussion($discussion, $all_participants, $unread_counts, $last_message_times, $user_id) == 'Previous Discussions'): ?>
                             <a href="view_discussion.php?id=<?php echo $discussion['id']; ?>" class="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center hover:shadow-lg transition duration-300 border-l-4 border-gray-500">
                                   <div class="flex-1">
                                       <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($discussion['title']); ?></p>
                                        <p class="text-gray-500 text-sm">
                                          Created By: <?php echo htmlspecialchars($discussion['username']); ?> ,
                                          <?php $participants = $all_participants[$discussion['id']];
                                            if ($participants){
                                                   foreach($participants as $i => $participant){
                                                       if($i < 2){
                                                          if(isset($participant['name'])){
                                                               echo htmlspecialchars($participant['name']) . ($i < count($participants)-1 ? ',' : '');
                                                           }else {
                                                                echo htmlspecialchars($participant['username']) . ($i < count($participants)-1 ? ',' : '');
                                                             }

                                                        } else if ($i == 2){
                                                           echo '...';
                                                       }
                                                     }

                                                } else {
                                                    echo "No Participants";
                                                 }
                                              ?>
                                            </p>
                                     </div>
                                      <div class="flex items-center">
                                         <i class="fas fa-arrow-right text-gray-500"></i>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                   </div>
                 <?php else: ?>
                           <p class="text-gray-600">No previous discussions yet.</p>
                    <?php endif; ?>
            </div>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>