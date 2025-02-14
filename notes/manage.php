<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Fetch all notes
$stmt = $conn->prepare("SELECT notes.*, note_categories.name as category_name FROM notes LEFT JOIN note_categories ON notes.category_id = note_categories.id ORDER BY created_at DESC");
$stmt->execute();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 p-8">
  <div class="max-w-6xl mx-auto">

    <!-- Alert Messages (if any) -->
    <?php if ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
      <div class="mb-4 md:mb-0">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
          Digital Notebook
        </h1>
        <p class="text-gray-600 mt-2">Your thoughts, organized beautifully</p>
      </div>
      <a href="<?php echo BASE_URL; ?>notes/add" class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-purple-600 hover:to-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>New Note</span>
      </a>
    </div>

    <!-- Notes Grid -->
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($notes): ?>
            <?php foreach ($notes as $note): ?>
                 <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-2xl transition-shadow duration-300 border border-gray-100">
                   <div class="flex justify-between items-start">
                        <div class="w-full">
                            <h3 class="text-xl font-semibold text-gray-800">
                                <a href="<?php echo BASE_URL; ?>notes/view?id=<?php echo $note['id']; ?>" class="hover:underline">
                                    <?php
                                     $max_title_length = 50; // Adjust as needed
                                     $truncated_title = strlen($note['title']) > $max_title_length
                                        ? substr($note['title'], 0, $max_title_length) . '...'
                                         : $note['title'];
                                     echo htmlspecialchars($truncated_title);
                                    ?>
                                </a>
                            </h3>
                             <p class="mt-2 text-sm text-gray-800 whitespace-normal break-words">
                                  <?php
                                    $max_content_length = 150; // Adjust as needed
                                       $truncated_content = strlen($note['content']) > $max_content_length
                                        ? substr($note['content'], 0, $max_content_length) . '...'
                                         : $note['content'];
                                    echo $truncated_content; // No htmlspecialchars here
                                    ?>
                               </p>
                              <span class="block text-sm text-gray-500 mt-2">
                                  <?php echo date('M j, Y', strtotime($note['created_at'])); ?>
                                </span>
                         </div>
                          <span class="ml-4 px-3 py-1 rounded-full text-sm
                                    <?php echo $note['category_name'] ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?php echo htmlspecialchars($note['category_name'] ?: 'Uncategorized'); ?>
                         </span>
                    </div>

                      <!-- Action Buttons -->
                    <div class="mt-4 flex justify-end space-x-2">
                         <a href="<?php echo BASE_URL; ?>notes/view?id=<?php echo $note['id']; ?>" class="p-2 hover:bg-gray-100 rounded-full" title="View">
                             <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                             </svg>
                         </a>
                          <a href="<?php echo BASE_URL; ?>notes/edit?id=<?php echo $note['id']; ?>" class="p-2 hover:bg-gray-100 rounded-full" title="Edit">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                 </svg>
                          </a>
                           <a href="<?php echo BASE_URL; ?>notes/delete?id=<?php echo $note['id']; ?>" class="p-2 hover:bg-red-100 rounded-full" title="Delete">
                                 <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                 </svg>
                         </a>
                  </div>
            </div>
         <?php endforeach; ?>
     <?php else: ?>
         <div class="col-span-full text-center py-20">
           <div class="text-gray-400 mb-4">
              <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
             </svg>
           </div>
           <h3 class="text-xl text-gray-600 mb-2">No notes found</h3>
            <p class="text-gray-500 mb-4">Start capturing your ideas</p>
             <a href="<?= BASE_URL; ?>notes/add" class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 hover:from-purple-600 hover:to-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-300">
                Create Your First Note
            </a>
         </div>
      <?php endif; ?>
    </div>
</div>