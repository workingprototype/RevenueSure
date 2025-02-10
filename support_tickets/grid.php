<?php
 //This is a helper page to create a grid layout
?>
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if ($tickets): ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="bg-white rounded-lg shadow-md p-4 flex flex-col justify-between fade-in">
                   <h2 class="text-xl font-semibold text-gray-800 mb-2"><a href="<?php echo BASE_URL; ?>support_tickets/view?id=<?php echo $ticket['id']; ?>" class="text-gray-800 hover:underline"><?php echo htmlspecialchars($ticket['title']); ?></a></h2>
                <p class="text-gray-600 text-sm mb-2"><strong>Assigned To:</strong> <?php echo htmlspecialchars($ticket['assigned_username'] ? $ticket['assigned_username'] : 'Unassigned'); ?></p>
                 <p class="text-gray-600 text-sm mb-2">
                   <strong>Status:</strong>
                     <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        <?php
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
                            ?>
                            "><?php echo htmlspecialchars($ticket['status']); ?>
                      </span>
                   </p>
                 <p class="text-gray-600 text-sm mb-2">
                    <strong>Priority:</strong>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            <?php
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
                              ?>"><?php echo htmlspecialchars($ticket['priority']); ?>
                         </span>
                  </p>

                 <div class="flex justify-end gap-2">
                        <a href="<?php echo BASE_URL; ?>support_tickets/edit?id=<?php echo $ticket['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i></a>
                         <a href="<?php echo BASE_URL; ?>support_tickets/delete?id=<?php echo $ticket['id']; ?>" class="text-red-600 hover:underline"><i class="fas fa-trash-alt"></i></a>
                      </div>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
         <p class="text-gray-600 text-center">No tickets found.</p>
    <?php endif; ?>
</div>