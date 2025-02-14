<?php
 // Access to $note is from the top.
?>
<!-- Inside note_card.php -->
<div class="bg-white rounded-lg shadow-md p-4 transition duration-300 hover:scale-105">
    <div class="flex justify-between items-start mb-2">
        <h3 class="text-md font-semibold text-gray-800 hover:underline"><a href="<?php echo BASE_URL; ?>notes/view?id=<?php echo $note['id']; ?>"><?php echo htmlspecialchars($note['title']); ?></a></h3>
    </div>
    <p class="text-gray-600 text-sm">Category: <?php echo htmlspecialchars($note['category_name'] ? $note['category_name'] : 'Uncategorized'); ?></p>
    <p class="text-gray-600 text-sm">Created: <?php echo htmlspecialchars(date('M d, Y', strtotime($note['created_at']))); ?></p>
     <a href="<?php echo BASE_URL; ?>notes/edit?id=<?php echo $note['id']; ?>" class="text-blue-600 hover:underline">  <i class="fas fa-edit"></i> Edit</a>
    
</div>