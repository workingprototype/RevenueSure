<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all support tickets
$stmt = $conn->prepare("SELECT support_tickets.*, users.username as assigned_username, users2.username as created_username
                        FROM support_tickets
                        LEFT JOIN users ON support_tickets.assigned_to = users.id
                         LEFT JOIN users as users2 ON support_tickets.user_id = users2.id ORDER BY created_at DESC");

$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$view = isset($_GET['view']) ? $_GET['view'] : 'list';

// Include header
require 'header.php';
?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Support Tickets</h1>

    <!-- Add Ticket Button -->
    <div class="flex justify-between items-center mb-8">
        <a href="add_ticket.php" class="bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
            <i class="fas fa-plus-circle mr-2"></i> Add Ticket
         </a>
       <div class="flex gap-2">
           <a href="?view=list" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'list') echo "bg-gray-200"; ?>"><i class="fas fa-list"></i> List</a>
           <a href="?view=grid" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'grid') echo "bg-gray-200"; ?>"><i class="fas fa-th-large"></i> Grid</a>
           <a href="?view=card" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'card') echo "bg-gray-200"; ?>"><i class="fas fa-credit-card"></i> Cards</a>
           <a href="?view=kanban" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'kanban') echo "bg-gray-200"; ?>"><i class="fas fa-columns"></i> Kanban</a>
           <a href="?view=calendar" class="px-4 py-2 rounded-lg hover:bg-gray-200 transition <?php if($view === 'calendar') echo "bg-gray-200"; ?>"><i class="fas fa-calendar-alt"></i> Calendar</a>
       </div>
    </div>

     <div class="mb-8 border border-gray-400 bg-gray-100 rounded-lg p-6">
    <?php
    switch ($view) {
      case 'grid':
         include 'view_tickets_grid.php';
        break;
      case 'card':
          include 'view_tickets_card.php';
         break;
        case 'kanban':
                 include 'view_tickets_kanban.php';
                break;
         case 'calendar':
                 include 'view_tickets_calendar.php';
                 break;
       default:
                 include 'view_tickets_list.php';
          break;
    }
    ?>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>