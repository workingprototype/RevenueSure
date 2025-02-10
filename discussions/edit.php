<?php

require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id']) || !isset($_GET['discussion_id'])) {
    header("Location: " . BASE_URL . "discussions/manage");
    exit();
}

$message_id = $_GET['id'];
$discussion_id = $_GET['discussion_id'];

// Fetch the message to ensure the current user can modify this
$stmt = $conn->prepare("SELECT discussion_messages.* FROM discussion_messages WHERE id = :message_id AND user_id = :user_id");
$stmt->bindParam(':message_id', $message_id);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$message = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$message) {
    header("Location: " . BASE_URL . "discussions/view?id=$discussion_id");
    exit();
}

// Check if the user can edit based on time limit
$timeLimit = strtotime($message['sent_at']) + (60*60); // 1 hour
$currentTime = time();
if ($currentTime > $timeLimit) {
     echo "<script>alert('You cannot edit this message as its older than 1 hour.'); window.location.href='discussions/view?id=$discussion_id';</script>";
    exit;
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updated_message = trim($_POST['message']);

      if (empty($updated_message)) {
            $error = "Message cannot be empty.";
        } else {
           $stmt = $conn->prepare("UPDATE discussion_messages SET message = :message WHERE id = :message_id");
           $stmt->bindParam(':message', $updated_message);
            $stmt->bindParam(':message_id', $message_id);
           if($stmt->execute()){
               $success = "Message updated successfully!";
               header("Location: " . BASE_URL . "discussions/view?id=$discussion_id&success=true");
                exit();
           }else{
                $error = "Error updating message";
           }
       }
}

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Message</h1>
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
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
              <label for="message" class="block text-gray-700">Message</label>
                 <textarea name="message" id="message" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($message['message']); ?></textarea>
            </div>
             <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Update Message</button>
          <div class="mt-4">
               <a href="<?php echo BASE_URL; ?>discussions/view?id=<?php echo $discussion_id; ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Discussion</a>
           </div>
        </form>
     </div>
</div>
