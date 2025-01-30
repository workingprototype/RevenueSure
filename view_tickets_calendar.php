<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
   <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
    <?php
        $tickets = isset($tickets) ? $tickets : [];

    ?>
   <div id='calendar' class="rounded-2xl overflow-hidden shadow-xl bg-white p-4"></div>
   <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
              if(calendarEl){
                  const tickets = <?php echo json_encode($tickets); ?>;
                      const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                          events: tickets.map(ticket => ({
                              title: ticket.title,
                              start: ticket.created_at,
                              end: ticket.expected_resolution_date ? ticket.expected_resolution_date : ticket.created_at,
                             url:  `view_ticket.php?id=${ticket.id}`,
                                backgroundColor:  ticket.priority === 'High' ? '#ef4444' : ( ticket.priority === 'Medium' ? '#facc15' : '#22c55e'),
                                 borderColor:  ticket.priority === 'High' ? '#ef4444' : ( ticket.priority === 'Medium' ? '#facc15' : '#22c55e')
                         })),
                      });
                  calendar.render();
              }
        });
    </script>