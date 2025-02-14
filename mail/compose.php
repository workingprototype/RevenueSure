<?php
require_once ROOT_PATH . 'helper/core.php';
require_once ROOT_PATH . 'mail/includes/email_functions.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';
//Get User ID
$user_id = $_SESSION['user_id'];
// Fetch leads and customers
$stmt = $conn->prepare("SELECT id, name, email FROM leads");
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, name, email FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch related tasks, projects and tickets
$stmt = $conn->prepare("SELECT id, task_name AS name FROM tasks WHERE user_id = :userid");
$stmt->bindParam(':userid', $_SESSION['user_id']);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM projects WHERE project_manager_id = :userid");
$stmt->bindParam(':userid', $_SESSION['user_id']);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, title AS name FROM support_tickets WHERE user_id = :userid");
$stmt->bindParam(':userid', $_SESSION['user_id']);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $to = trim($_POST['to']);
    $subject = trim($_POST['subject']);
    $body = $_POST['body'];
    $altBody = $_POST['altBody'];
    $relation = trim($_POST['related_type']);

     if (empty($to) || empty($subject) || empty($body)) {
         $error = "Recipient, subject, and body are required.";
     } else {
            // Fetch user's email settings
            $settings = getUserEmailSettings($conn, $user_id);

            if ($settings) {

             $attachments = [];
            if (!empty($_FILES['attachments']['name'][0])) { //If multiple attachmnents
                    foreach($_FILES['attachments']['name'] as $key => $name){
                         if ($_FILES['attachments']['error'][$key] == 0) {
                             $file_name = basename($name);
                               $file_tmp = $_FILES['attachments']['tmp_name'][$key];
                                $file_path = "public/uploads/" . uniqid() . "_" . $file_name;
                                if(move_uploaded_file($file_tmp, $file_path)){
                                    $attachments[] = $file_path;
                                } else {
                                    $error .= "Failed to upload : " . $file_name . "<br/>";
                                }
                         }

                   }

            }
            // Send email using SMTP

            $mail = new PHPMailer(true); // `true` enables exceptions
            try {
                 $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host       = $settings['smtp_server'];
                     $mail->SMTPAuth   = true;
                     $mail->Username   = $settings['smtp_username'];
                     $mail->Password   = decrypt($settings['smtp_password']);
                     $mail->SMTPSecure = $settings['smtp_security'];
                    $mail->Port       = $settings['smtp_port'];

                    //Recipients
                    $mail->setFrom($settings['smtp_username'], 'RevenueSure Mailer');
                   $mail->addAddress($to);
                   // Attachments
                    foreach ($attachments as $attachment) {
                        $mail->addAttachment($attachment);
                    }

                    // Content
                   $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $body;
                   $mail->AltBody = $altBody ?: 'This is a plain-text message alternative.';

                    $mail->send();
                     //Cleanup attachfiles

                     foreach ($attachments as $attachment) {
                            if (file_exists($attachment)) {
                               unlink($attachment);
                             }
                      }

                       $success = "Email sent successfully!";
                 } catch (Exception $e) {
                        $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            if(empty($error)){
               header("Location: " . BASE_URL . "mail/index?success=true");
               exit();
            }

        } else {
            $error = "Email settings not found. Please configure your email settings.";
        }
      }
}

function getRelatedItems($options = [], $name = null){
 if($name){
    $select ="<select name='".htmlspecialchars($name)."' class='related-type-select'>";

    foreach($options as $option) {
        $id = $option['id'];
        $name_r = htmlspecialchars($option['name']);
        $select .= "<option value='$id'>$name_r</option>";
    }
    $select .= "</select>";
    return $select;
 }
}

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Compose Email</h1>
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
           <form id="compose-form" method="POST" action=""  enctype="multipart/form-data">
           <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="prefill_contact" class="block text-gray-700">Prefill Contact</label>
                    <select name="prefill_contact" id="prefill_contact" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="updateToField(this.value)">
                      <option value="">Select Contact</option>
                       <optgroup label="Leads">
                            <?php foreach ($leads as $lead): ?>
                                <option value="<?php echo htmlspecialchars($lead['email']); ?>" data-name="<?php echo htmlspecialchars($lead['name']); ?>"><?php echo htmlspecialchars($lead['name']); ?> (Lead)</option>
                            <?php endforeach; ?>
                       </optgroup>
                        <optgroup label="Customers">
                            <?php foreach ($customers as $customer): ?>
                                 <option value="<?php echo htmlspecialchars($customer['email']); ?>" data-name="<?php echo htmlspecialchars($customer['name']); ?>"><?php echo htmlspecialchars($customer['name']); ?> (Customer)</option>
                             <?php endforeach; ?>
                       </select>
                </div>
                 <div class="mb-4">
                   <label for="to" class="block text-gray-700">To</label>
                   <input type="email" name="to" id="to" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label for="subject" class="block text-gray-700">Subject</label>
                   <input type="text" name="subject" id="subject" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
               </div>
                  <div class="mb-4">
                        <label for="related_type" class="block text-gray-700">Related to</label>
                         <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select one</option>
                                 <option value="task">Task</option>
                                   <option value="project">Project</option>
                                    <option value="ticket">Support Ticket</option>
                           </select>
                </div>
             <div id="related_select_container" class="hidden">
                     <label for="related_id" class="block text-gray-700">Related Id</label>
                          <?php
                               $tasks_list = getRelatedItems($tasks, "tasks");
                                $projects_list = getRelatedItems($projects, "projects");
                                 $tickets_list = getRelatedItems($tickets, "tickets");
                                 
                                  echo <<<HTML
                                    <div id="related_select_lists" class="flex flex-wrap gap-2">
                                        <div id="task_select" class="mb-4 hidden">$tasks_list</div>
                                        <div id="project_select" class="mb-4 hidden">$projects_list</div>
                                        <div id="ticket_select" class="mb-4 hidden">$tickets_list</div>
                                    </div>
                                  HTML;
                           ?>
                 </div>
                   <div class="mb-4">
                    <label for="body" class="block text-gray-700">Body</label>
                      <textarea name="body" id="body" rows="6" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required></textarea>
                  </div>
                  <div class="mb-4">
                     <label for="altBody" class="block text-gray-700">Plain Text Alternative (optional)</label>
                      <textarea name="altBody" id="altBody" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
                   </div>
                      <div class="mb-4">
                            <label for="attachments[]" class="block text-gray-700">Attachments (Optional)</label>
                              <input type="file" name="attachments[]" id="attachments" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" multiple>
                     </div>
                 <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Send Email</button>
           </form>
        </div>
       <div class="mt-6">
            <a href="<?php echo BASE_URL; ?>mail/index" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Mailbox</a>
       </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toInput = document.getElementById('to');
        const prefillContactSelect = document.getElementById('prefill_contact');
        const relatedTypeSelect = document.getElementById('related_type');
        const relatedSelectContainer = document.getElementById('related_select_container');
          const tasks_list = document.querySelector("select[name='tasks']");
          const projects_list = document.querySelector("select[name='projects']");
          const tickets_list = document.querySelector("select[name='tickets']");

        //Prefill Section:
             prefillContactSelect.addEventListener('change', function() {
                toInput.value = this.value;
               let name = (this.options[this.selectedIndex].dataset.name);
             });
        //Related section
              relatedTypeSelect.addEventListener('change', function() {
                  showRelatedIds();
                  updateSubjectLine(); // Call this function when related Type changes.
               });

              function showRelatedIds() {
                  const selectedType = relatedTypeSelect.value;
                  const relatedFields = document.getElementById('related_select_container');

                   document.getElementById('task_select').classList.add('hidden');
                     document.getElementById('project_select').classList.add('hidden');
                     document.getElementById('ticket_select').classList.add('hidden');

                if(selectedType == ""){
                        relatedFields.classList.add('hidden');
                  } else{
                        relatedFields.classList.remove('hidden');
                        document.getElementById(selectedType + '_select').classList.remove('hidden');
                   }
             }
   
        function updateSubjectLine() {
            const relatedTypeSelect = document.getElementById('related_type');
            const subjectInput = document.getElementById('subject');
            const selectedType = relatedTypeSelect.value;
                let relatedName = ''; //initialize to a blank value.
                let relatedText = '';

            let relatedSelect = null; // Get related Select
                  if (selectedType === 'task') {
                        relatedSelect = document.querySelector("select[name='tasks']");
                         relatedText= 'Task'
                      } else if (selectedType === 'project') {
                            relatedSelect = document.querySelector("select[name='projects']");
                             relatedText= 'Project'
                      } else if (selectedType === 'ticket') {
                            relatedSelect = document.querySelector("select[name='tickets']");
                             relatedText= 'Ticket'
                       }

           if(relatedSelect){
                 relatedName = relatedSelect.options[relatedSelect.selectedIndex].text
           }

            if (selectedType && relatedName) {
                subjectInput.value = "Regarding " + relatedText + " " + relatedName;  // Removes the 's'
            } else {
               subjectInput.value = ""; // Clear subject
            }
          }

        //Event listeners to detect the changes and call the functions.
        if(tasks_list){
            tasks_list.addEventListener("change", updateSubjectLine);
         }

       if(projects_list){
             projects_list.addEventListener("change", updateSubjectLine);
       }
       if(tickets_list){
               tickets_list.addEventListener("change", updateSubjectLine);
       }

    });
</script>