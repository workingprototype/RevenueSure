<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch ticket details
$stmt = $conn->prepare("SELECT support_tickets.*, users.username as assigned_username, users2.username as created_username, projects.name as project_name
                        FROM support_tickets
                        LEFT JOIN users ON support_tickets.assigned_to = users.id
                         LEFT JOIN users as users2 ON support_tickets.user_id = users2.id
                        LEFT JOIN projects ON support_tickets.project_id = projects.id
                        WHERE support_tickets.id = :id");
$stmt->bindParam(':id', $ticket_id);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    header("Location: manage_tickets.php");
    exit();
}
$error = "";
$success = "";

 // Fetch attachments for the ticket
$stmt = $conn->prepare("SELECT * FROM support_ticket_attachments WHERE ticket_id = :ticket_id ORDER BY created_at DESC");
$stmt->bindParam(':ticket_id', $ticket_id);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
     if (isset($_POST['add_comment'])) {
            $comment = $_POST['comment'];
             if(!empty($comment)){
                $stmt = $conn->prepare("INSERT INTO support_ticket_comments (ticket_id, user_id, comment) VALUES (:ticket_id, :user_id, :comment)");
                $stmt->bindParam(':ticket_id', $ticket_id);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->bindParam(':comment', $comment);

                if($stmt->execute()) {
                     $success = "Comment added successfully!";
                     header("Location: view_ticket.php?id=$ticket_id&success=true");
                      exit();
                } else {
                     $error = "Error adding comment.";
                   }
             }  else{
                 $error = "Comment cannot be empty";
            }
         }
}
    if(isset($_GET['success']) && $_GET['success'] == 'true'){
           $success = "Ticket updated successfully!";
      }

  // Fetch comments for the ticket
$stmt = $conn->prepare("SELECT support_ticket_comments.*, users.username FROM support_ticket_comments INNER JOIN users ON support_ticket_comments.user_id = users.id WHERE ticket_id = :ticket_id ORDER BY created_at ASC");
$stmt->bindParam(':ticket_id', $ticket_id);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Ticket Details</h1>

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

    <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg mb-8">
         <div class="mb-8">
                    <div class="flex justify-between items-start mb-4">
                         <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2 uppercase tracking-wide border-b-2 border-gray-400 pb-2">
                                Ticket #<?php echo htmlspecialchars($ticket['id']); ?>
                            </h2>
                          </div>
                          <div class="flex gap-2 items-center">
                            <span class="px-2 py-1 rounded-full text-sm  <?php
                                    switch ($ticket['priority']) {
                                        case 'Low':
                                             echo 'bg-green-100 text-green-800';
                                           break;
                                        case 'Medium':
                                             echo 'bg-yellow-100 text-yellow-800';
                                            break;
                                       case 'High':
                                             echo 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                           echo 'bg-gray-100 text-gray-800';
                                              break;
                                }
                                ?>"><?php echo htmlspecialchars($ticket['priority']); ?></span>
                                <span class="px-2 py-1 rounded-full text-sm <?php
                                    switch ($ticket['status']) {
                                        case 'New':
                                            echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'In Progress':
                                                echo 'bg-yellow-100 text-yellow-800';
                                               break;
                                            case 'Resolved':
                                                  echo 'bg-green-100 text-green-800';
                                                     break;
                                                case 'Closed':
                                                  echo 'bg-gray-100 text-gray-800';
                                                     break;
                                                default:
                                                   echo 'bg-gray-100 text-gray-800';
                                                      break;
                                        }
                                     ?>"><?php echo htmlspecialchars($ticket['status']); ?></span>
                           </div>
                    </div>
                 <p class="text-gray-700 text-lg mb-4">
                    <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
                 </p>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       <div>
                           <p class="text-gray-600 text-sm uppercase tracking-wide"><strong>Created by:</strong> <span class="font-semibold"><?php echo htmlspecialchars($ticket['created_username']); ?></span></p>
                            <p class="text-gray-600 text-sm uppercase tracking-wide"><strong>Assigned To:</strong> <span class="font-semibold"><?php echo htmlspecialchars($ticket['assigned_username'] ? $ticket['assigned_username'] : 'Unassigned'); ?></span></p>
                             <?php if ($ticket['project_name']): ?>
                                 <p class="text-gray-600 text-sm uppercase tracking-wide"><strong>Project:</strong> <span class="font-semibold"><a href="view_project.php?id=<?php echo htmlspecialchars($ticket['project_id']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($ticket['project_name']); ?></a></span></p>
                              <?php endif; ?>
                       </div>
                        <div class="text-right">
                           
                            <p class="text-gray-600 text-sm uppercase tracking-wide">
                                    <strong>Category:</strong> <span class="font-semibold"><?php echo htmlspecialchars($ticket['category']); ?></span>
                                </p>
                            <p class="text-gray-600 text-sm uppercase tracking-wide">
                            <strong>Expected Resolution Date:</strong> <span class="font-semibold"><?php echo htmlspecialchars($ticket['expected_resolution_date'] ? $ticket['expected_resolution_date'] : 'N/A'); ?></span>
                           </p>
                       </div>
                     </div>
               </div>
               <!--  Comments Section -->
               <div class="mt-8">
              <h2 class="text-xl font-bold text-gray-800 mb-4 relative border-b border-gray-400 pb-2">
                      <i class="fas fa-comments text-gray-600 absolute left-[-10px] top-[4px] mr-2"></i> Comments
                    </h2>
                    <form method="POST" action="" class="mb-8">
                        <textarea name="comment" id="comment" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Add a comment"></textarea>
                        <button type="submit" name="add_comment" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4">Add Comment</button>
                   </form>
                    <?php if($comments): ?>
                         <ul>
                            <?php foreach ($comments as $comment): ?>
                                 <li class="bg-gray-50 p-4 my-2 rounded-lg border border-gray-200">
                                     <div class="flex justify-between">
                                       <p class="text-gray-800"><?php echo htmlspecialchars($comment['comment']); ?></p>
                                      </div>
                                       <div class="text-right">
                                           <p class="text-gray-500 text-sm "><?php echo htmlspecialchars($comment['username']); ?> - <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($comment['created_at']))); ?></p>
                                     </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                            <p>No comments yet!</p>
                        <?php endif; ?>
                    </div>
                    <!-- Attachments section -->
                 <div class="mt-4">
                  <h2 class="text-xl font-bold text-gray-800 mb-4 relative border-b border-gray-400 pb-2">
                         <i class="fas fa-paperclip text-gray-600 absolute left-[-10px] top-[4px] mr-2"></i>Attachments
                    </h2>
                     <form method="POST" action="upload_ticket_attachment.php" enctype="multipart/form-data">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                          <div class="mb-4">
                            <label for="file" class="block text-gray-700">Choose File</label>
                            <input type="file" name="file" id="file" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                         </div>
                          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Upload</button>
                    </form>
                       <div class="mt-4">
                         <?php if ($attachments): ?>
                            <ul class="list-disc ml-6">
                                <?php foreach ($attachments as $attachment): ?>
                                    <li class="my-2 flex justify-between items-center">
                                       <a href="<?php echo $attachment['file_path']; ?>" class="text-blue-600 hover:underline" download><?php echo htmlspecialchars($attachment['file_name']); ?></a>
                                         <a href="delete_ticket_attachment.php?id=<?php echo $attachment['id']; ?>&ticket_id=<?php echo $ticket_id; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                                    </li>
                             <?php endforeach; ?>
                         </ul>
                       <?php else: ?>
                           <p>No attachments found!</p>
                         <?php endif; ?>
                      </div>
                    </div>
             <div class="mt-8">
                <a href="manage_tickets.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Tickets</a>
                 <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Edit Ticket</a>
            </div>
        </div>
    </div>
<?php
// Include footer
require 'footer.php';
?>