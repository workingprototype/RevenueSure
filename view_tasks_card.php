<?php
 //This is a helper page to create a card layout
?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if ($tasks): ?>
            <?php foreach ($tasks as $task): ?>
                 <div class="bg-white rounded-2xl shadow-xl p-6 flex flex-col justify-between border-l-4  transition hover:shadow-2xl"
                     style="border-left-color:<?php
                          switch ($task['priority']) {
                                    case 'Low':
                                        echo '#22c55e';
                                          break;
                                    case 'Medium':
                                          echo '#facc15';
                                           break;
                                      case 'High':
                                         echo '#ef4444';
                                           break;
                                      default:
                                           echo '#94a3b8';
                                             break;
                                }
                     ?>;">
                   <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">
                              <?php if($project_id) : ?> <a href="view_task.php?id=<?php echo $task['id']; ?>&project_id=<?php echo $project_id; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                                <?php elseif($lead_id): ?>
                                      <a href="view_task.php?id=<?php echo $task['id']; ?>&lead_id=<?php echo $lead_id; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                                  <?php else :?>
                                    <a href="view_task.php?id=<?php echo $task['id']; ?>" class="text-gray-800 hover:underline"> <?php echo htmlspecialchars($task['task_name']); ?></a>
                                <?php endif; ?>
                        </h2>
                        <p class="text-gray-600 mb-2"><strong>Type:</strong> <?php echo htmlspecialchars($task['task_type']); ?></p>
                        <p class="text-gray-600">
                           <strong>Due:</strong> <?php echo htmlspecialchars($task['due_date']); ?>
                       </p>
                       <p class="text-gray-600 mt-2">
                                <strong>Status:</strong>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        <?php
                                        switch ($task['status']) {
                                            case 'To Do':
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                            case 'In Progress':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'Completed':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                              case 'Blocked':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                   break;
                                        case 'Canceled':
                                              echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                                break;
                                        }
                                        ?>
                                        "><?php echo htmlspecialchars($task['status']); ?>
                                    </span>
                                </p>
                       </div>
                      <div class="flex justify-between items-center mt-2">
                        <div class="flex space-x-2">
                              <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i></a>
                             <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-red-600 hover:underline"> <i class="fas fa-trash-alt"></i></a>
                         </div>
                        <div class="flex space-x-2">
                              <a href="toggle_task_status.php?id=<?php echo $task['id']; ?>&status=<?php echo $task['status'] === 'To Do' ? 'In Progress' : ($task['status'] === 'In Progress' ? 'Completed' : 'To Do'); ?>" class="text-gray-600 hover:underline">
                                 <?php
                                     if ($task['status'] === 'To Do') {
                                          echo '<i class="fas fa-play"></i>';
                                     } else if ($task['status'] === 'In Progress') {
                                          echo '<i class="fas fa-check-double"></i>';
                                       }else {
                                            echo '<i class="fas fa-history"></i>';
                                       }
                                    ?>
                               </a>
                                  <a href="add_reminder.php?id=<?php echo $task['id']; ?>&due_date=<?php echo urlencode($task['due_date']); ?>" class="text-gray-600 hover:underline"> <i class="fas fa-bell"></i></a>
                        </div>
                       </div>
                </div>
             <?php endforeach; ?>
        <?php else : ?>
            <p class="text-gray-600 text-center">No tasks found.</p>
        <?php endif; ?>
    </div>