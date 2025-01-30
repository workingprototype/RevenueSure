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

// Fetch tasks for the ticket
$stmt = $conn->prepare("SELECT support_ticket_tasks.*, users.username FROM support_ticket_tasks JOIN users ON support_ticket_tasks.assigned_to = users.id WHERE ticket_id = :ticket_id ORDER BY due_date ASC");
$stmt->bindParam(':ticket_id', $ticket_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Ticket Details</h1>
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
    <div class="flex flex-wrap -mx-4">
        <!-- Main Ticket Details (Left Side) -->
        <div class="w-full md:w-3/4 px-4 mb-6">
            <div class="bg-white border-2 border-gray-200 p-6 rounded-lg mb-6">
                  <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                Ticket #<?php echo htmlspecialchars($ticket['id']); ?>
                            </h2>
                                <p class="text-gray-700 mb-4 text-lg "><?php echo htmlspecialchars($ticket['title']); ?></p>
                         </div>
                         <div class="flex gap-2 items-start">
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

                 
                 <p class="text-gray-700 leading-relaxed mb-4"><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       <div>
                            <p class="text-gray-600"><strong>Assigned To:</strong> <?php echo htmlspecialchars($ticket['assigned_username'] ? $ticket['assigned_username'] : 'Unassigned'); ?></p>
                           <p class="text-gray-600"><strong>Created By:</strong> <?php echo htmlspecialchars($ticket['created_username']); ?></p>
                      </div>
                      <div>
                        <p class="text-gray-600"><strong>Category:</strong> <?php echo htmlspecialchars($ticket['category']); ?></p>
                           <p class="text-gray-600">
                                <strong>Expected Resolution Date:</strong> <?php echo htmlspecialchars($ticket['expected_resolution_date'] ? $ticket['expected_resolution_date'] : 'N/A'); ?>
                            </p>
                        </div>
                 </div>
                 <?php if ($ticket['project_name']): ?>
                     <p class="mt-4 text-gray-600"><strong>Related Project:</strong> <a href="view_project.php?id=<?php echo htmlspecialchars($ticket['project_id']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($ticket['project_name']); ?></a></p>
                    <?php endif; ?>
           </div>
             <!-- Add Comment -->
        <div class="bg-white border-2 border-gray-200 p-6 rounded-lg mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Add Comment</h2>
            <form method="POST" action="">
                <textarea name="comment" id="comment" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Write a comment..."></textarea>
                <button type="submit" name="add_comment" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4">Add Comment</button>
            </form>
           <?php if($comments): ?>
            <h2 class="text-xl font-bold text-gray-800 mt-6 mb-4">Comments</h2>
                  <ul>
                        <?php foreach ($comments as $comment): ?>
                             <li class="p-4 border-b border-gray-100 my-2 bg-gray-50 rounded-lg">
                                 <div class="flex justify-between items-start mb-2">
                                      <p class="text-gray-800"><?php echo htmlspecialchars($comment['comment']); ?></p>
                                     <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($comment['username']); ?>  -  <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($comment['created_at']))); ?></p>
                                   </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
             </div>
           </div>

         <!--  Tasks and Attachments Section (Right Side) -->
        <div class="w-full md:w-1/4 px-4 mb-6">
            <div class="bg-white border-2 border-gray-200 p-6 rounded-lg mb-6 ">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Tasks</h2>
                      <div class="flex justify-end mb-2">
                         <a href="add_support_ticket_task.php?ticket_id=<?php echo $ticket_id; ?>" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 inline-block">Add Task</a>
                       </div>
                       <?php if($tasks): ?>
                           <ul class="space-y-2">
                               <?php foreach($tasks as $task): ?>
                                   <li class="bg-gray-100 p-4 rounded-lg mb-2"
                                       style="border-left: 4px solid <?php
                                      switch ($task['priority']) {
                                           case 'High':
                                               echo 'red';
                                                break;
                                           case 'Medium':
                                               echo 'yellow';
                                             break;
                                           case 'Low':
                                             echo 'green';
                                                break;
                                       default:
                                            echo 'gray';
                                             break;
                                        }
                                    ?>;">
                                        <div class="flex justify-between">
                                                <p class="font-semibold"><a href="edit_support_ticket_task.php?id=<?php echo $task['id']; ?>&ticket_id=<?php echo $ticket_id; ?>" class="hover:underline"> <?php echo htmlspecialchars($task['title']); ?></a></p>
                                                <div>
                                                    <a href="toggle_support_task_status.php?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline">
                                                      <?php
                                                           if ($task['status'] === 'To Do') {
                                                                echo '<i class="fas fa-play"></i>';
                                                           } else if ($task['status'] === 'In Progress') {
                                                                echo '<i class="fas fa-check-double"></i>';
                                                            }
                                                             else {
                                                                 echo '<i class="fas fa-history"></i>';
                                                              }
                                                       ?>
                                                  </a>
                                                    <a href="delete_support_ticket_task.php?id=<?php echo $task['id']; ?>&ticket_id=<?php echo $ticket_id; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i></a>
                                                   </div>
                                          </div>
                                           <p class="text-gray-600 text-sm">Due Date:  <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($task['due_date']))); ?>
                                           </p>
                                      <p class="text-gray-600 text-sm">Assigned To: <?php echo htmlspecialchars($task['username'] ? $task['username'] : 'Unassigned'); ?>  </p>
                                   </li>
                               <?php endforeach; ?>
                             </ul>
                         <?php else: ?>
                            <p>No tasks found.</p>
                         <?php endif; ?>

            </div>
              <div class="bg-white border-2 border-gray-200 p-6 rounded-lg">
                   <h2 class="text-xl font-bold text-gray-800 mb-4">Attachments</h2>
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
                                        <li class="flex justify-between items-center mb-2">
                                            <a href="<?php echo $attachment['file_path']; ?>" class="text-blue-600 hover:underline" download><?php echo htmlspecialchars($attachment['file_name']); ?></a>
                                               <a href="delete_ticket_attachment.php?id=<?php echo $attachment['id']; ?>&ticket_id=<?php echo $ticket_id; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                           <?php else: ?>
                                   <p>No attachments yet!</p>
                            <?php endif; ?>
                        </div>
            </div>
        </div>

    </div>
    <div class="mt-6">
        <a href="manage_tickets.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Tickets</a>
         <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">Edit Ticket</a>
   </div>
</div>
<?php
// Include footer
require 'footer.php';
?>