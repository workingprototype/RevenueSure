<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $role = $_POST['role'];
    $active = isset($_POST['active']) ? 1 : 0;

      // Validate inputs
        if (empty($name) || empty($email) || empty($role)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        }else {
            // Check if the email already exists
            $stmt = $conn->prepare("SELECT id FROM accountants WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                $error = "An employee with this email already exists.";
            } else {
             $stmt = $conn->prepare("INSERT INTO accountants (name, email, contact_number, role, active) VALUES (:name, :email, :contact_number, :role, :active)");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':contact_number', $contact_number);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':active', $active, PDO::PARAM_INT);

              if ($stmt->execute()) {
                  // Todo: Send invitation via email.
                  $success = "Accountant added successfully! Send the default password to the employee";
                   header("Location: " . BASE_URL . "accounting/manage_accountants?success=true");
                  exit();
                } else {
                      $error = "Error adding accountant.";
                 }
            }
         }
}

// Fetch all accountants for display
$stmt = $conn->prepare("SELECT * FROM accountants ORDER BY created_at DESC");
$stmt->execute();
$accountants = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Accountants</h1>

    <!-- Add Accountant Button -->
    <a href="#" onclick="document.getElementById('addAccountantModal').classList.toggle('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mb-4 inline-block">Add Accountant</a>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            Accountant added successfully!
        </div>
    <?php endif; ?>

    <!-- Accountants Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($accountants): ?>
                    <?php foreach ($accountants as $accountant): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($accountant['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($accountant['email']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($accountant['role']); ?></td>
                            <td class="px-4 py-2"><?php echo $accountant['active'] ? 'Active' : 'Inactive'; ?></td>
                            <td class="px-4 py-2">
                                <a href="#" class="text-blue-600 hover:underline">Edit</a>
                                 <a href="#" class="text-red-600 hover:underline ml-2">Delete</a>
                           </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-600">No accountants found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
     <div id="addAccountantModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

   <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
<form method="POST" action="">
<?php echo csrfTokenInput(); ?>
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
<h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
Add New Accountant
</h3>          <div class="mb-4">
              <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Accountant Name</label>
                 <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
          </div>
          <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="contact_number" class="block text-gray-700 text-sm font-bold mb-2">Contact Number</label>
                <input type="text" name="contact_number" id="contact_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
             <div class="mb-4">
                 <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role/Permissions</label>
                    <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="Accountant">Accountant</option>
                         <option value="Senior Accountant">Senior Accountant</option>
                     </select>
            </div>
            <div class="mb-4">
             <label class="inline-flex items-center">
                <input type="checkbox" name="active" class="form-checkbox h-5 w-5 text-blue-600"><span class="ml-2 text-gray-700 text-sm font-bold">Active</span>
               </label>
            </div>
          </div>
             <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
           <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
              Add
           </button>
            <button type="button" onclick="document.getElementById('addAccountantModal').classList.toggle('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
          </button>
      </div>
 </form>
</div></div>
</div>
<script>
</script>
<?php
/*
<!-- Implementation Notes:
- SQL queries need to be adjusted based on your database schema (relationships, data types).
- Implement edit and delete functionalities.
-->
*/
?>