<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_type = $_POST['request_type'];
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    if (empty($request_type) || empty($description)) {
        $error = "Request type and description are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO knowledge_base_article_requests (user_id, request_type, description) VALUES (:user_id, :request_type, :description)");
        $stmt->bindParam(':user_id', $user_id);
         $stmt->bindParam(':request_type', $request_type);
           $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $success = "Request submitted successfully! We'll get back to you soon.";
         } else {
            $error = "Error submitting request.";
         }
    }
}

?>
 <div class="container mx-auto p-6 fade-in">
        <h1 class="text-4xl font-bold text-gray-900 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Request New Article</h1>
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

    <!-- Request Article Form -->
    <div class="bg-white p-6 rounded-2xl shadow-xl">
            <form method="POST" action="">
            <?php echo csrfTokenInput(); ?>
                <div class="mb-4">
                    <label for="request_type" class="block text-gray-700">Request Type</label>
                     <select name="request_type" id="request_type" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                            <option value="">Select Type</option>
                              <option value="New Guide Needed">New Guide Needed</option>
                             <option value="Existing Guide Needs Update">Existing Guide Needs Update</option>
                            <option value="New FAQ Suggestion">New FAQ Suggestion</option>
                     </select>
                </div>
               <div class="mb-4">
                  <label for="description" class="block text-gray-700">Description</label>
                  <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" rows="6"></textarea>
              </div>
                <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">Submit Request</button>
             </form>
       </div>
</div>
