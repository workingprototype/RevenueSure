<?php
 //This is a helper page to create a list layout
?>
  <div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left ">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Task Name</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Type</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Description</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Due Date</th>
                     <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Assigned To</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Priority</th>
                    <th class="px-4 py-3 font-semibold text-gray-700 text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                 <?php if ($tasks): ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="border-b transition hover:bg-gray-100">
                              <td class="px-4 py-3">
                                     <?php if($project_id) : ?> <a href="<?php echo BASE_URL; ?>tasks/viewtask?id=<?php echo $task['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                                    <?php elseif($lead_id): ?>
                                          <a href="<?php echo BASE_URL; ?>tasks/viewtask?id=<?php echo $task['id']; ?>&lead_id=<?php echo $lead_id; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                                      <?php else :?>
                                       <a href="<?php echo BASE_URL; ?>tasks/viewtask?id=<?php echo $task['id']; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                                  <?php endif; ?>
                               </td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($task['task_type']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($task['description']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($task['due_date']); ?></td>
                           <td class="px-4 py-3"><?php echo htmlspecialchars($task['username']); ?></td>
                            <td class="px-4 py-3">
                                  <span class="px-2 py-1 rounded-full <?php
                                switch ($task['status']) {
                                    case 'To Do':
                                        echo 'bg-gray-200 text-gray-800';
                                            break;
                                       case 'In Progress':
                                            echo 'bg-blue-200 text-blue-800';
                                            break;
                                        case 'Completed':
                                            echo 'bg-green-200 text-green-800';
                                              break;
                                       case 'Blocked':
                                             echo 'bg-yellow-200 text-yellow-800';
                                                break;
                                        case 'Canceled':
                                            echo 'bg-red-200 text-red-800';
                                            break;
                                        default:
                                             echo 'bg-gray-100 text-gray-800';
                                             break;
                                    }
                            ?>">
                                   <?php echo htmlspecialchars($task['status']); ?>
                              </span>
                           </td>
                                 <td class="px-4 py-3"><?php echo htmlspecialchars($task['priority']); ?></td>
                               <td class="px-4 py-3 flex gap-2">
                                      <a href="<?php echo BASE_URL; ?>tasks/edit?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i></a>
                                         <a href="<?php echo BASE_URL; ?>tasks/delete?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline ml-2"><i class="fas fa-trash-alt"></i></a>
                                           <a href="<?php echo BASE_URL; ?>tasks/toggle_status?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline ml-2">
                                           <?php
                                                if ($task['status'] === 'To Do') {
                                                     echo '<i class="fas fa-play"></i>';
                                                } else if ($task['status'] === 'In Progress') {
                                                    echo '<i class="fas fa-check-double"></i>';
                                                 }
                                                 else {
                                                      echo '<i class="fas fa-history"></i>';
                                                  }
                                             ?>
                                     </a>
                                       <a href="<?php echo BASE_URL; ?>reminders/add?id=<?php echo $task['id']; ?>&due_date=<?php echo urlencode($task['due_date']); ?>" class="text-gray-600 hover:underline ml-2">
                                          <i class="fas fa-bell"></i>
                                       </a>
                                       <?php if ($task['project_id']): ?>
                                       <a href="<?php echo BASE_URL; ?>projects/view?id=<?php echo $task['project_id']; ?>" class="text-purple-600 hover:underline"> <i class="fas fa-project-diagram"></i></a>
                                    <?php endif; ?>
                           </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-4 py-2 text-center text-gray-600">No tasks found.</td>
                    </tr>
                 <?php endif; ?>
            </tbody>
        </table>
    </div>