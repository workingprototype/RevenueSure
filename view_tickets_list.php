<?php
 //This is a helper page to create a list layout
 ?>
<div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Title</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Assigned To</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Category</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Created By</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Priority</th>
                       <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Expected Resolution</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                 <?php if ($tickets): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr class="border-b transition hover:bg-gray-100">
                             <td class="px-4 py-3"><a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="text-gray-800 hover:underline"><?php echo htmlspecialchars($ticket['title']); ?></a></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($ticket['assigned_username'] ? $ticket['assigned_username'] : 'Unassigned'); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($ticket['category']); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($ticket['created_username']); ?></td>
                           <td class="px-4 py-3">
                                  <span class="px-2 py-1 rounded-full <?php
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
                                            echo 'bg-gray-200 text-gray-800';
                                              break;
                                        default:
                                             echo 'bg-gray-100 text-gray-800';
                                             break;
                                    }
                            ?>">
                                   <?php echo htmlspecialchars($ticket['status']); ?>
                              </span>
                           </td>
                             <td class="px-4 py-3">
                                  <span class="px-2 py-1 rounded-full <?php
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
                           </td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($ticket['expected_resolution_date'] ? $ticket['expected_resolution_date'] : 'N/A'); ?></td>
                               <td class="px-4 py-3 flex gap-2">
                                    <a href="edit_ticket.php?id=<?php echo $ticket['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i></a>
                                    <a href="delete_ticket.php?id=<?php echo $ticket['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i></a>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-4 py-2 text-center text-gray-600">No tickets found.</td>
                    </tr>
                 <?php endif; ?>
            </tbody>
        </table>
    </div>