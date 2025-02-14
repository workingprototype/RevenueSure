<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'mail/includes/email_functions.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$email_id = isset($_GET['id']) ? $_GET['id'] : null; // Now expecting UID string

if (!$email_id) {
    header("Location: " . BASE_URL . "mail/index");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch email settings
$settings = getUserEmailSettings($conn, $user_id);

if (!$settings) {
    $error = "Please configure your email settings first.";
} else {
    // Connect to IMAP
    if ($imap = imapConnect($settings)) {

         // Attempt to fetch the Email Overview.
        try {
        $overview = @imap_fetch_overview($imap, $email_id, FT_UID);

        } catch (Exception $e){
             $error = "Error fetching overview: " . $e->getMessage();
        }

 //To see if the email can load.
    if (empty($overview)) {
             $error = "Unable to load the email overview.
              Email not found.";
              $overview = null;
                error_log("Unable to load the email with id: " . $email_id);
         }  else {
            $overview = $overview[0];
                 $structure = @imap_fetchstructure($imap, $email_id, FT_UID);
                 if(!$structure){
                     $error = "Unable to load the structure of the email overview.";
                      error_log("Unable to load the email overview.");
                 } else {
                    $body = getEmailBody($imap, $email_id, $structure, FT_UID);

                     imap_close($imap);
                  }
          }
    } else {
        $error = "Failed to connect to IMAP server.";
    }
}
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">View Email</h1>
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
    <?php if ($overview): ?>
           <div class="bg-gray-100 p-4 rounded-lg mb-4">
                <p><strong>From:</strong> <?php echo htmlspecialchars($overview->from); ?></p>
                  <p><strong>To:</strong> <?php
                $to = is_array($overview->to) && !empty($overview->to)
                        ? (is_object($overview->to[0])
                            ? htmlspecialchars($overview->to[0]->mailbox . "@" . $overview->to[0]->host)
                            : htmlspecialchars(implode(", ", $overview->to))) // If it's an array of strings
                        : (is_object($overview->to)
                            ? htmlspecialchars($overview->to->mailbox . "@" . $overview->to->host)
                            : htmlspecialchars($overview->to)); // Handle cases where it's a string

                echo $to;

                ?></p>
                  <p><strong>Subject:</strong> <?php echo htmlspecialchars($overview->subject); ?></p>
                   <p><strong>Date:</strong> <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($overview->date))); ?></p>
             </div>
          <div>
               <h2>Message</h2>
                 <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($body ? $body : "No Body Present")); ?></p>
         </div>
            <div class="flex justify-between items-center mt-4">
                   <a href="<?php echo BASE_URL; ?>mail/index" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">Back to Mailbox</a>
              </div>
        <?php else: ?>
                 <p>Email not found.</p>
        <?php endif; ?>
</div>