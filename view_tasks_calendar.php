<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
   <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
    <?php
        $tasks = isset($tasks) ? $tasks : [];
        $project_id = isset($project_id) ? $project_id : null;
         $lead_id = isset($lead_id) ? $lead_id : null;
    ?>
   <div id='calendar' class="rounded-2xl overflow-hidden shadow-xl bg-white p-4"></div>
   <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
              if(calendarEl){
                  const tasks = <?php echo json_encode($tasks); ?>;
                      const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                          events: tasks.map(task => ({
                              title: task.task_name,
                              start: task.due_date,
                              end:  task.due_date,
                             url:  `view_task.php?id=${task.id}<?php if($project_id) echo "&project_id=" . $project_id ; ?><?php if($lead_id) echo "&lead_id=" . $lead_id ; ?>`,
                                backgroundColor:  task.priority === 'High' ? '#ef4444' : ( task.priority === 'Medium' ? '#facc15' : '#22c55e'),
                              borderColor:  task.priority === 'High' ? '#ef4444' : ( task.priority === 'Medium' ? '#facc15' : '#22c55e')
                         })) ,
                      });
                  calendar.render();
              }
        });
    </script>