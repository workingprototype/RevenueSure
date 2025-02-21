<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';



// Fetch all users, employees, and customers for dropdowns
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role='user' OR role='admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("SELECT id, name FROM customers");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $type = $_POST['discussion_type'];
     $participants = $_POST['participants'] ?? [];
    $initial_message = $_POST['initial_message'] ?? '';

      if (empty($title) || empty($type) ) {
        $error = "All fields are required.";
        } else {
                $stmt = $conn->prepare("INSERT INTO discussions (title, user_id, type) VALUES (:title, :user_id, :type)");
                $stmt->bindParam(':title', $title);
                 $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->bindParam(':type', $type);

            if ($stmt->execute()) {
                   $discussion_id = $conn->lastInsertId();
                    if(!empty($participants)){
                    foreach ($participants as $participant_type => $participant_ids) {
                       foreach($participant_ids as $participant_id){
                            $stmt = $conn->prepare("INSERT INTO discussion_participants (discussion_id, participant_id, participant_type) VALUES (:discussion_id, :participant_id, :participant_type)");
                            $stmt->bindParam(':discussion_id', $discussion_id);
                            $stmt->bindParam(':participant_id', $participant_id);
                            $stmt->bindParam(':participant_type', $participant_type);
                           $stmt->execute();
                       }
                    }
                  }
                  if(!empty($initial_message)) {
                        $stmt = $conn->prepare("INSERT INTO discussion_messages (discussion_id, user_id, message) VALUES (:discussion_id, :user_id, :message)");
                        $stmt->bindParam(':discussion_id', $discussion_id);
                        $stmt->bindParam(':user_id', $_SESSION['user_id']);
                        $stmt->bindParam(':message', $initial_message);
                        $stmt->execute();
                    }
                   header("Location: " . BASE_URL . "discussions/view?id=$discussion_id");
                    exit();
              } else {
                   $error = "Error creating discussion.";
              }
       }
}


?>

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Start a New Discussion</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <div class="bg-white p-6 rounded-lg shadow-md">
    <form method="POST" action="">
    <?php echo csrfTokenInput(); ?>
        <div class="mb-4">
            <label for="title" class="block text-gray-700">Discussion Title</label>
            <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
        </div>
        <div class="mb-4">
                <label for="discussion_type" class="block text-gray-700">Discussion Type</label>
                <select name="discussion_type" id="discussion_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                    <option value="internal">Internal (Employee to Employee)</option>
                    <option value="external">External (Employee to Client)</option>
                </select>
             </div>
           <div class="mb-4">
               <label for="participants" class="block text-gray-700">Add Participants</label>
                   <div class="relative">
                        <div class="flex flex-wrap gap-2">
                             <input type="text" id="user_input" placeholder="Search User"  data-autocomplete-id="user-autocomplete" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 mb-2">
                            <div id="user-autocomplete-suggestions" class="absolute z-10 mt-2 w-full bg-white border rounded shadow-md hidden"></div>
                              <input type="text" id="employee_input" placeholder="Search Employee" data-autocomplete-id="employee-autocomplete"  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 mb-2">
                            <div id="employee-autocomplete-suggestions" class="absolute z-10 mt-2 w-full bg-white border rounded shadow-md hidden"></div>
                            
                               <input type="text" id="customer_input" placeholder="Search Customer" data-autocomplete-id="customer-autocomplete"  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 mb-2">
                           <div id="customer-autocomplete-suggestions" class="absolute z-10 mt-2 w-full bg-white border rounded shadow-md hidden"></div>
                       </div>
                    <div id="selected_participants" class="flex flex-wrap gap-2 mt-2"></div>
                    </div>
             </div>
            <div class="mb-4">
                    <label for="initial_message" class="block text-gray-700">Initial Message (Optional)</label>
                       <textarea name="initial_message" id="initial_message" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
            </div>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Create Discussion</button>
    </form>
</div></div>

      <script>
 document.addEventListener('DOMContentLoaded', function () {
        const userInput = document.querySelector('input[data-autocomplete-id="user-autocomplete"]');
        const employeeInput = document.querySelector('input[data-autocomplete-id="employee-autocomplete"]');
        const customerInput = document.querySelector('input[data-autocomplete-id="customer-autocomplete"]');
         const userSuggestionsDiv = document.getElementById('user-autocomplete-suggestions');
        const employeeSuggestionsDiv = document.getElementById('employee-autocomplete-suggestions');
         const customerSuggestionsDiv = document.getElementById('customer-autocomplete-suggestions');
          const selectedParticipantsDiv = document.getElementById('selected_participants');
           let selectedUsers = {};
           let selectedEmployees = {};
           let selectedCustomers = {};
        function fetchSuggestions(input, div, type){
            input.addEventListener('input', function () {
                const query = this.value.trim();
                 if (query.length < 1) {
                      div.classList.add('hidden');
                      return;
                  }

                fetch(`fetch_related_entities.php?search=${query}&type=${type}`)
                    .then(response => response.json())
                    .then(data => {
                        div.innerHTML = '';
                           if(data.length > 0) {
                             data.slice(0, 10).forEach(item => {
                                   const suggestion = document.createElement('div');
                                      suggestion.textContent = item.name ? item.name : item.username;
                                   suggestion.classList.add('px-4', 'py-2', 'hover:bg-gray-100', 'cursor-pointer');
                                    suggestion.addEventListener('click', function () {
                                        input.value = item.name ? item.name : item.username;
                                          addParticipant(item, type)
                                          div.classList.add('hidden');
                                          input.value = '';
                                      });
                                    div.appendChild(suggestion);
                               });
                             div.classList.remove('hidden');
                            }else {
                                 div.classList.add('hidden');
                            }
                    })
                    .catch(error => {
                        console.error(`Error fetching ${type}:`, error);
                        div.classList.add('hidden');
                 });
            });
            document.addEventListener('click', function (event) {
                if (!input.contains(event.target) && !div.contains(event.target)) {
                  div.classList.add('hidden');
                 }
            });
       }
        function addParticipant(participant, type) {
             const participant_id = participant.id;
             let selectedItems = {};
              let type_name = 'name'
            if (type == 'user'){
               selectedItems = selectedUsers;
                type_name = 'username';
             } else if( type == 'employee'){
                  selectedItems = selectedEmployees
             }else {
                 selectedItems = selectedCustomers
              }

              if (!selectedItems[participant_id]) {
                selectedItems[participant_id] = { ...participant, type: type};
                 const partDiv = document.createElement('div');
                 partDiv.classList.add('bg-gray-200', 'rounded-full','px-3','py-1', 'text-gray-700','relative');
                  partDiv.innerHTML = `
                            <span>${participant[type_name] ? participant[type_name] : participant.username }</span>
                           <input type="hidden" name="participants[${type}][]" value="${participant_id}"/>
                              <button type="button" onclick="removeParticipant(this)" class="text-gray-600 hover:text-gray-900 ml-1"><i class="fas fa-times"></i></button>
                       `
                    selectedParticipantsDiv.appendChild(partDiv);
                  if (type == 'user'){
                  selectedUsers = selectedItems
                    } else if(type == 'employee'){
                      selectedEmployees = selectedItems
                  }else{
                       selectedCustomers = selectedItems
                     }
            }
      }
        function removeParticipant(button){
             const parentDiv = button.closest('div');
           const itemId = parentDiv.querySelector('input[type="hidden"]').value;
              const parent = button.parentElement
            parent.remove();
            let type_name = 'name'
           if(parentDiv.querySelector('input[type="hidden"]').name.includes("user")){
                delete selectedUsers[itemId]
                  type_name = 'username'
              } else if(parentDiv.querySelector('input[type="hidden"]').name.includes("employee")) {
                  delete selectedEmployees[itemId]
             } else {
                   delete selectedCustomers[itemId]
                }
        }
       fetchSuggestions(userInput, userSuggestionsDiv, 'user')
       fetchSuggestions(employeeInput, employeeSuggestionsDiv, 'employee')
       fetchSuggestions(customerInput, customerSuggestionsDiv, 'customer')

    });

</script>
