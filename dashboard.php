<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Get the user's role from the session

// Fetch user details
$stmt = $conn->prepare("SELECT username, credits FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user['username'];

// Fetch leads count
$stmt = $conn->prepare("SELECT COUNT(*) as total_leads FROM leads");
$stmt->execute();
$leads_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_leads'];

// Fetch todos
$stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = :user_id ORDER BY due_date ASC");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch outstanding payments data
$stmt = $conn->prepare("SELECT 
    COUNT(*) as total_unpaid, 
    SUM(CASE WHEN status = 'Overdue' THEN 1 ELSE 0 END) as total_overdue,
     SUM(CASE WHEN status = 'Partially Paid' THEN 1 ELSE 0 END) as total_partially_paid,
     SUM(total - paid_amount) as outstanding_amount
     FROM invoices
        WHERE status != 'Paid'
");
$stmt->execute();
$outstanding_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

<!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        <!-- Credits Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-blue-500 transition hover:shadow-2xl">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your Credits</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $user['credits']; ?></p>
            <p class="text-gray-600 mt-2">Credits available for accessing leads.</p>
            <a href="manage_credits.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Manage Credits</a>
        </div>

        <!-- Leads Card -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-blue-500 transition hover:shadow-2xl">
             <h3 class="text-xl font-semibold text-gray-900 mb-2">Total Leads</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $leads_count; ?></p>
            <p class="text-gray-600 mt-2">Leads available in the platform.</p>
            <a href="search_leads.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Search Leads</a>
        </div>

    <!-- Admin-Specific Card -->
    <?php if ($role === 'admin'): ?>
           <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-green-500 transition hover:shadow-2xl">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Admin Actions</h3>
                 <p class="text-gray-600">Manage users and leads.</p>
                <a href="reporting_dashboard.php" class="mt-4 inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Go to Reporting Dashboard</a>
           </div>
            <div class="bg-white p-6 rounded-2xl shadow-xl border-l-4 border-purple-500 transition hover:shadow-2xl">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Outstanding Payments</h3>
                   <p class="text-gray-600 mb-2">
                    <strong>Total Unpaid Invoices:</strong>
                         <span class="bg-red-100 text-red-800 rounded-full px-2 py-1">
                                <?php echo htmlspecialchars($outstanding_data['total_unpaid'] ); ?>
                            </span>
                        </p>
                    <p class="text-gray-600 mb-2">
                        <strong>Total Partially Paid Invoices:</strong>
                           <span class="bg-yellow-100 text-yellow-800 rounded-full px-2 py-1">
                                 <?php echo htmlspecialchars($outstanding_data['total_partially_paid'] ? $outstanding_data['total_partially_paid'] : 0); ?>
                            </span>
                    </p>
                    <p class="text-gray-600 mb-2">
                        <strong>Total Overdue Invoices:</strong>
                           <span class="bg-gray-100 text-gray-800 rounded-full px-2 py-1">
                                <?php echo htmlspecialchars($outstanding_data['total_overdue'] ? $outstanding_data['total_overdue'] : 0); ?>
                            </span>
                    </p>

                    <p class="text-gray-600 mt-2">
                            <strong>Total Outstanding Amount:</strong> $<?php echo htmlspecialchars($outstanding_data['outstanding_amount'] ? $outstanding_data['outstanding_amount'] : 0); ?>
                    </p>
                    <a href="manage_invoices.php?status=unpaid" class="mt-4 inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">View Outstanding Invoices</a>
            </div>
    <?php endif; ?>
</div>
    
<div class="bg-white p-6 rounded-2xl shadow-xl mt-8 border-l-4 border-blue-500 transition hover:shadow-2xl">
<h2 class="text-2xl font-bold text-gray-900 mb-4 relative">
  <i class="fas fa-list-check absolute left-[-20px] top-[-5px] text-blue-500 text-sm"></i> To-Do List
    </h2>
 <form method="POST" action="add_todo.php" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
           <div class="mb-4">
              <label for="title" class="block text-gray-700">Title</label>
             <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
          </div>
           <div class="mb-4">
              <label for="due_date" class="block text-gray-700">Due Date</label>
               <input type="datetime-local" name="due_date" id="due_date" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
           </div>
          <div class="mb-4">
           <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300">Add To Do</button>
        </div>
    </div>
    <div class="mb-4">
         <label for="description" class="block text-gray-700">Description</label>
       <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
     </div>
     <div class="mb-4">
         <label for="related_type" class="block text-gray-700">Related to</label>
         <select name="related_type" id="related_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showRelatedInput(this.value)">
              <option value="">None</option>
             <option value="task">Task</option>
           <option value="lead">Lead</option>
            <option value="customer">Customer</option>
        </select>
    </div>
     <div id="related_id_container" class="mb-4 hidden">
         <label for="related_id" class="block text-gray-700">Related</label>
           <input type="text" name="related_id" id="related_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"  data-autocomplete-id="related-autocomplete">
            <div id="related-autocomplete-suggestions" class="absolute z-10 mt-2 w-full bg-white border rounded shadow-md hidden"></div>
      </div>
</form>
  <?php if ($todos): ?>
      <div class="mt-6">
       <label class="inline-flex items-center">
             <input type="checkbox" id="show_completed" class="mr-2" >
                <span class="text-gray-700">Show Completed</span>
           </label>
      </div>
       <ul id="todo_items" class="mt-4">
         <?php foreach ($todos as $todo): ?>
          <li class="mb-2 p-3 rounded-lg border relative <?php echo $todo['is_completed'] ? 'bg-green-100 border-green-200 line-through completed-todo' : 'border-gray-200'; ?>">
                <div class="flex justify-between items-center">
                  <div>
                    <h3 class="font-semibold <?php echo $todo['is_completed'] ? 'text-green-700' : 'text-gray-800'; ?>"> <?php echo htmlspecialchars($todo['title']); ?></h3>
                     <p class="text-gray-600"> <?php echo htmlspecialchars($todo['description'] ? $todo['description'] : ""); ?></p>
                           <?php if($todo['due_date']): ?>
                                <p class="text-gray-600 text-sm">
                                  Due Date:  <?php echo date('Y-m-d H:i', strtotime($todo['due_date'])); ?>
                               </p>
                            <?php endif; ?>
                     <?php if ($todo['related_type'] && $todo['related_id']): ?>
                        <p class="text-gray-500 text-sm">
                            <strong>Related:</strong>
                            <?php echo ucfirst(htmlspecialchars($todo['related_type'])); ?> #<?php echo htmlspecialchars($todo['related_id']); ?>
                        </p>
                     <?php endif; ?>
                </div>
                    <div class="flex gap-2">
                         <a href="edit_todo.php?id=<?php echo $todo['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                         <a href="delete_todo.php?id=<?php echo $todo['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                      <a href="mark_complete.php?id=<?php echo $todo['id']; ?>&completed=<?php echo $todo['is_completed'] == 1 ? 0 : 1 ?>" class="text-green-600 hover:underline"><?php echo $todo['is_completed'] == 1 ? 'Mark Incomplete' : 'Mark Complete'; ?></a>
                   </div>
                </div>
             </li>
         <?php endforeach; ?>
        </ul>
  <?php else: ?>
         <p class="text-gray-600">No to-dos added.</p>
  <?php endif; ?>
</div>
<script>
function showRelatedInput(type) {
const relatedIdContainer = document.getElementById('related_id_container');
if (type === 'task' || type === 'lead' || type === 'customer') {
     relatedIdContainer.classList.remove('hidden');
} else {
     relatedIdContainer.classList.add('hidden');
      document.getElementById('related_id').value = '';
}
//Clear input field when no relation
if(type == ""){
  document.getElementById('related_id').value = "";
}
}
document.addEventListener('DOMContentLoaded', function() {
const relatedTypeSelect = document.getElementById('related_type');
const relatedIdInput = document.querySelector('input[data-autocomplete-id="related-autocomplete"]');
 const suggestionsDiv = document.getElementById('related-autocomplete-suggestions');
   let selectedRelatedId = null;
    relatedIdInput.addEventListener('input', function () {
        const query = this.value.trim();
        const type = relatedTypeSelect.value.trim();
        if (query.length < 1 || type === "") {
            suggestionsDiv.classList.add('hidden');
            return;
        }

        fetch(`fetch_related_entities.php?search=${query}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                suggestionsDiv.innerHTML = '';
                if(data.length > 0){
                    data.slice(0, 10).forEach(related => {
                        const suggestion = document.createElement('div');
                        suggestion.textContent = related.name ? related.name : related.title;
                        suggestion.classList.add('px-4', 'py-2', 'hover:bg-gray-100', 'cursor-pointer');
                        suggestion.addEventListener('click', function () {
                            relatedIdInput.value = related.name ? related.name : related.title;
                             selectedRelatedId = related.id;
                            suggestionsDiv.classList.add('hidden');
                        });
                        suggestionsDiv.appendChild(suggestion);
                    });
                    suggestionsDiv.classList.remove('hidden');
                } else {
                        suggestionsDiv.classList.add('hidden');
                }

            })
            .catch(error => {
                console.error('Error fetching results:', error);
                suggestionsDiv.classList.add('hidden');
            });

    });
    document.addEventListener('click', function (event) {
        if (!relatedIdInput.contains(event.target) && !suggestionsDiv.contains(event.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });

   const form =  relatedIdInput.closest('form');
   form.addEventListener('submit', function(event) {
        if (relatedIdInput.value.trim() && selectedRelatedId == null ) {
          if(relatedTypeSelect.value != ""){
                event.preventDefault();
              alert("Please select a valid from the suggestions before saving.");
          }
        }
         if (relatedIdInput.value.trim() && selectedRelatedId != null ) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'related_id';
            hiddenInput.value =  selectedRelatedId;
            form.appendChild(hiddenInput);
       }
   });
    showRelatedInput(document.getElementById('related_type').value);
      const showCompletedCheckbox = document.getElementById('show_completed');
 const todoItems = document.querySelectorAll('#todo_items li');
      showCompletedCheckbox.addEventListener('change', function() {
       todoItems.forEach(function(todo){
             if (this.checked) {
              todo.classList.remove('hidden')
             }else {
                  if (todo.classList.contains('completed-todo')) {
                           todo.classList.add('hidden')
                     }else {
                           todo.classList.remove('hidden')
                     }
                }
         });
   });
});
</script>
<?php
// Include footer
require 'footer.php';
?>