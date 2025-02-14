<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'mail/includes/email_functions.php';

redirectIfUnauthorized(true); //Requires admin

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_creation'])) {
    //Fetch all users without mail directories and then set them up with the directories.
    try {
        $stmt = $conn->prepare("SELECT id FROM users");
        $stmt->execute();
         $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

          foreach($users as $user_id){
             $maildirPath = getUserMaildir($user_id);
                if (!is_dir($maildirPath)) {
                     if(!createMaildir($user_id)){
                          echo "Could not create mail directory for user: " . $user_id;
                         }
                   }
          }
           echo "<script>alert('Maildirs creation complete'); </script>";
      } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
       echo "Database error. Please try again later.";
      }

}
?>
<div class="container mx-auto p-6 fade-in">
<h1 class="text-4xl font-bold text-gray-900 mb-6">Create Maildirs for Existing Users</h1>
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

<div class="bg-white p-6 rounded-2xl shadow-xl">
    <p>This script will create Maildir directories for all existing users who don't have one yet.  Be careful running this on a large database.</p>

    <form method="POST" action="">
    <?php echo csrfTokenInput(); ?>
        <button type="submit" name="confirm_creation" class="bg-red-700 text-white px-6 py-3 rounded-xl hover:bg-red-900 transition duration-300 shadow-md">Create Maildirs</button>
    </form>
</div>