<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
 // Fetch all requests
$stmt = $conn->prepare("SELECT knowledge_base_article_requests.*, users.username FROM knowledge_base_article_requests INNER JOIN users ON knowledge_base_article_requests.user_id = users.id ORDER BY created_at DESC");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
 <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Article Requests</h1>

    <!-- Article Requests Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <table class="w-full text-left">
                <thead>
                   <tr>
                         <th class="px-4 py-2">Request Type</th>
                           <th class="px-4 py-2">Description</th>
                          <th class="px-4 py-2">Requester</th>
                            <th class="px-4 py-2">Request Date</th>
                   </tr>
               </thead>
              <tbody>
                    <?php if ($requests): ?>
                        <?php foreach ($requests as $request): ?>
                           <tr class="border-b">
                               <td class="px-4 py-2"><?php echo htmlspecialchars($request['request_type']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($request['description']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($request['username']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($request['created_at']); ?></td>
                           </tr>
                          <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                             <td colspan="4" class="px-4 py-2 text-center text-gray-600">No article requests found.</td>
                       </tr>
                    <?php endif; ?>
             </tbody>
       </table>
  </div>
</div>
<?php
// Include footer
require 'footer.php';
?>