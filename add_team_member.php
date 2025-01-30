<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = $_POST['role_id'];
    $department = trim($_POST['department']);
     $password = password_hash('default123', PASSWORD_BCRYPT); // Default Password

      if (empty($username) || empty($email) || empty($role_id)) {
            $error = "All fields are required.";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
             $error = "Invalid email format";
         } else{
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
                 $stmt->bindParam(':email', $email);
                 $stmt->execute();
                    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                        $error = "A user with this email already exists.";
                    } else {

                   $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id, department) VALUES (:username, :email, :password, :role_id, :department)");
                   $stmt->bindParam(':username', $username);
                   $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':role_id', $role_id);
                    $stmt->bindParam(':department', $department);

                     if ($stmt->execute()) {
                        $success = "Team member added successfully!";
                         header("Location: manage_teams.php?success=true");
                        exit();
                     } else {
                         $error = "Error adding team member.";
                         }
                }

            }
}
// Fetch roles for the dropdown
$stmt = $conn->prepare("SELECT * FROM team_roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Add New Team Member</h1>

    <!-- Display error or success message -->
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
       <!-- Add Team Member Form -->
    <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
    <form method="POST" action="">
         <div class="mb-4">
            <label for="username" class="block text-gray-700">Name</label>
            <input type="text" name="username" id="username" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
        </div>
         <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
           <input type="email" name="email" id="email" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
         </div>
          <div class="mb-4">
                <label for="role_id" class="block text-gray-700">Role</label>
                  <select name="role_id" id="role_id" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" required>
                       <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                           <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                       <?php endforeach; ?>
                  </select>
              </div>
         <div class="mb-4">
                <label for="department" class="block text-gray-700">Department</label>
                   <input type="text" name="department" id="department" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
              </div>
        <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">Add Team Member</button>
    </form>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>