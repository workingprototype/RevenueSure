<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$ticket_id = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : null;
#add this

// Initialize variables
$lead_id = isset($_GET['lead_id']) ? (int)$_GET['lead_id'] : null;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;

// Validate Ticket ID
if($ticket_id){

    // Fetch task details
    $stmt = $conn->prepare("SELECT * FROM support_ticket_tasks WHERE ticket_id = :ticket_id ORDER BY created_at DESC");
    $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //Fetch Ticket by ID
   $stmt = $conn->prepare("SELECT * FROM support_tickets WHERE id = :ticket_id");
      $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
      $stmt->execute();
      $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

     if(!$ticket){
              displayAlert("Ticket not found!", 'error');
                exit;
          }
} else {
    $stmt = $conn->prepare("SELECT support_ticket_tasks.*, users.username, support_tickets.title as ticket_title
                            FROM support_ticket_tasks
                            LEFT JOIN users ON support_ticket_tasks.assigned_to = users.id
                            LEFT JOIN support_tickets ON support_ticket_tasks.ticket_id = support_tickets.id
                            ORDER BY support_ticket_tasks.created_at DESC");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<div class="container mx-auto p-6 fade-in">
   <h1 class="text-3xl font-bold text-gray-800 mb-6">
        <?php if($ticket_id): ?>
            Tasks for Ticket #<?php echo htmlspecialchars($ticket_id); ?>: <?php echo htmlspecialchars($ticket['title']); ?>
        <?php else: ?>
            Manage Support Ticket Tasks
        <?php endif; ?>
</h1>
   <div class="mb-4">
        <?php if ($ticket_id) : ?>
             <a href="<?php echo BASE_URL; ?>support_tickets/tasks/add?ticket_id=<?php echo $ticket_id; ?>" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                 <i class="fas fa-plus mr-2"></i>Add New Task
            </a>
        <?php else: ?>
            <p>View tasks linked to a specific Support Ticket or perform management with the tickets that are there. </p>
      <?php endif; ?>
   </div>
     <div class="bg-white shadow-md rounded my-6">
        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                 <th class="py-3 px-6 text-left">Id</th>
                    <th class="py-3 px-6 text-left">Related to Support Ticket</th>
                    <th class="py-3 px-6 text-left">Task Title</th>
                    <th class="py-3 px-6 text-left">Task Description</th>
                    <th class="py-3 px-6 text-left">Due Date</th>
                    <th class="py-3 px-6 text-left">Status</th>
                     <th class="py-3 px-6 text-left">Assigned To</th>
                   <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
               <?php if ($tasks): ?>
                 <?php foreach ($tasks as $task): ?>
                         <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['id']); ?></td>
                             <td class="py-3 px-6 text-left"> <?php echo htmlspecialchars($task['ticket_title'] ?? $ticket['title']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['title']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['description'] ?? 'N/A'); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['due_date']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['status']); ?></td>
                            <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($task['username'] ?: 'Unassigned'); ?></td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center">
                                    <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                        <a href="<?php echo BASE_URL; ?>support_tickets/tasks/edit?id=<?php echo urlencode($task['id']); ?>"><i class="fas fa-edit"></i></a>
                                    </div>
                                    <div class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                        <a href="<?php echo BASE_URL; ?>support_tickets/tasks/delete?id=<?php echo urlencode($task['id']); ?>" onclick="return confirm('Are you sure you want to delete this task?')"><i class="fas fa-trash-alt"></i></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                     <?php endforeach; ?>
                <?php else: ?>
                         <tr>
                              <td colspan="6" class="px-4 py-3 text-center text-gray-600">No Support Tickets found.</td>
                        </tr>
                 <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php if($ticket_id): ?>
       <div>
           <a href="<?php echo BASE_URL; ?>support_tickets/view?id=<?php echo $ticket_id; ?>" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 inline-block shadow-md">
             Back To Ticket
           </a>
       </div>
    <?php endif; ?>
</div>