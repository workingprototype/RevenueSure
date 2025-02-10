<div class="flex overflow-x-auto gap-4 p-4">
        <?php
            $statuses = ['New', 'In Progress', 'Resolved', 'Closed'];
            foreach ($statuses as $status):
                $filtered_tickets = array_filter($tickets, function ($ticket) use ($status) {
                 return $ticket['status'] === $status;
              });
            ?>
            <div class="kanban-column w-72 min-w-72 bg-gray-100 rounded-2xl p-4 shadow-xl border-l-4" data-status="<?php echo $status; ?>"
                 style="border-left-color:
               <?php
                  switch ($status) {
                    case 'New':
                        echo '#007aff';
                         break;
                   case 'In Progress':
                     echo '#facc15';
                        break;
                   case 'Resolved':
                     echo '#22c55e';
                       break;
                       case 'Closed':
                           echo '#94a3b8';
                                 break;
                      default:
                          echo '#94a3b8';
                          break;
                    }
                 ?>;">
                <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                  <?php echo $status; ?>
                   <span class="absolute top-[3px] left-[-22px] text-sm">
                     <?php
                    switch ($status) {
                         case 'New':
                              echo '<i class="fas fa-plus-circle text-blue-500"></i>';
                             break;
                           case 'In Progress':
                              echo '<i class="fas fa-spinner text-yellow-500"></i>';
                              break;
                          case 'Resolved':
                            echo '<i class="fas fa-check-circle text-green-500"></i>';
                              break;
                           case 'Closed':
                                  echo '<i class="fas fa-times-circle text-gray-500"></i>';
                                   break;
                           default:
                              echo '<i class="fas fa-tasks text-gray-500"></i>';
                                 break;
                        }
                    ?>
                 </span>
                </h2>
                  <ul class="kanban-items space-y-4">
                    <?php if($filtered_tickets) :
                      foreach ($filtered_tickets as $ticket):
                        ?>
                        <li
                            class="kanban-item bg-white p-4 rounded-xl shadow-sm flex flex-col justify-between  transition hover:shadow-2xl"
                            data-task-id="<?php echo $ticket['id']; ?>" draggable="true"
                             style="border-left: 4px solid <?php
                                 switch ($ticket['priority']) {
                                     case 'High':
                                         echo 'red';
                                          break;
                                       case 'Medium':
                                         echo 'yellow';
                                           break;
                                       case 'Low':
                                         echo 'green';
                                            break;
                                    default:
                                     echo 'gray';
                                       break;
                                 }
                             ?>;"
                        >
                           <div>
                                <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($ticket['title']); ?></p>
                            </div>
                            <div class="flex justify-end gap-2">
                                 <a href="<?php echo BASE_URL; ?>support_tickets/edit?id=<?php echo $ticket['id']; ?>" class="text-blue-600 hover:underline"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo BASE_URL; ?>support_tickets/view?id=<?php echo $ticket['id']; ?>" class="text-purple-600 hover:underline"><i class="fas fa-eye"></i></a>
                                </div>
                        </li>
                     <?php  endforeach; else: ?>
                         <p class="text-gray-600 text-center">No tickets in this category</p>
                    <?php endif; ?>
                  </ul>
            </div>
         <?php endforeach; ?>
    </div>
      <script>
 document.addEventListener('DOMContentLoaded', function() {
    const kanbanColumns = document.querySelectorAll('.kanban-column');
     const kanbanItems = document.querySelectorAll('.kanban-item');

        kanbanItems.forEach(item => {
            item.addEventListener('dragstart', dragStart);
        });

    kanbanColumns.forEach(column => {
        column.addEventListener('dragover', dragOver);
        column.addEventListener('dragenter', dragEnter);
        column.addEventListener('dragleave', dragLeave);
        column.addEventListener('drop', dragDrop);
    });

      let draggedItem = null;

    function dragStart(event) {
        draggedItem = event.target;
        event.dataTransfer.effectAllowed = 'move';
         event.dataTransfer.setData('text/html', this.innerHTML); // required for firefox
       setTimeout(() => {
            this.classList.add('invisible'); // to hide
        }, 0);
    }
    function dragOver(event) {
        event.preventDefault();
    }
        function dragEnter(event) {
            event.preventDefault();
            this.classList.add('bg-gray-300');
         }
           function dragLeave(event) {
                this.classList.remove('bg-gray-300');
            }

     function dragDrop(event) {
         event.preventDefault();
         this.classList.remove('bg-gray-300');
         if(draggedItem){
              const taskId = draggedItem.dataset.taskId;
              const newStatus = this.dataset.status;

            fetch('tasks/update_status', {
                method: 'POST',
                headers: {
                   'Content-Type': 'application/x-www-form-urlencoded',
                  },
                body: `task_id=${taskId}&status=${newStatus}`
                 })
                     .then(response => {
                        if(!response.ok){
                             throw new Error('Network response was not ok');
                           }
                         return response.text()
                     })
                    .then(data => {
                        this.querySelector('.kanban-items').appendChild(draggedItem);
                      draggedItem.classList.remove('invisible');
                        draggedItem = null
                        })
                     .catch(error => {
                         console.error('Error updating task status', error);
                        draggedItem.classList.remove('invisible');
                        draggedItem = null
                     });
        }
    }
});
</script>