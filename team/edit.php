<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "team/manage");
    exit();
}

$member_id = $_GET['id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $member_id);
$stmt->execute();
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header("Location: " . BASE_URL . "team/manage");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role_id = $_POST['role_id'];
         $department_id = $_POST['department_id'];

    if (empty($username) || empty($email) || empty($role_id) ) {
        $error = "All fields are required.";
    } else  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $error = "Invalid email format.";
    } else{
             $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $member_id);
            $stmt->execute();
                if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                    $error = "A user with this email already exists.";
                 } else {
                         $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, role_id = :role_id, department_id = :department_id WHERE id = :id");
                         $stmt->bindParam(':id', $member_id);
                          $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':email', $email);
                            $stmt->bindParam(':role_id', $role_id);
                           $stmt->bindParam(':department_id', $department_id);
                      if ($stmt->execute()) {
                          $success = "Member updated successfully!";
                            header("Location: " . BASE_URL . "team/manage?success=true");
                              exit();
                        } else {
                              $error = "Error updating member.";
                            }
                  }
            }
}
// Fetch roles for the dropdown
$stmt = $conn->prepare("SELECT * FROM team_roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
 // Fetch departments for the dropdown
$stmt = $conn->prepare("SELECT * FROM team_departments");
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);



?>

<h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Edit Team Member</h1>

<!-- Display error or success message -->
<?php if ($error): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        Member updated successfully!
    </div>
<?php endif; ?>
<div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
        <form method="POST" action="">
        <?php echo csrfTokenInput(); ?>
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Name</label>
                    <input type="text" name="username" id="username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($member['username']); ?>" required>
                </div>
                   <div class="mb-4">
                       <label for="email" class="block text-gray-700">Email</label>
                      <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($member['email']); ?>" required>
                    </div>
                   <div class="mb-4">
                        <label for="role_id" class="block text-gray-700">Role</label>
                        <select name="role_id" id="role_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <option value="">Select Role</option>
                           <?php foreach ($roles as $role): ?>
                             <option value="<?php echo $role['id']; ?>" <?php if($member['role_id'] == $role['id']) echo 'selected'; ?>><?php echo htmlspecialchars($role['name']); ?></option>
                            <?php endforeach; ?>
                       </select>
                    </div>
                <div class="mb-4">
                  <label for="department_id" class="block text-gray-700">Department</label>
                  <select name="department_id" id="department_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                         <option value="">Select Department</option>
                           <?php foreach ($departments as $department): ?>
                             <option value="<?php echo $department['id']; ?>" <?php if($member['department_id'] == $department['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($department['name']); ?></option>
                           <?php endforeach; ?>
                    </select>
               </div>
           <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 uppercase tracking-wide">Update Member</button>
            <div class="mt-4">
             <a href="<?php echo BASE_URL; ?>team/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 uppercase tracking-wide">Back to Team</a>
            </div>
      </form>
  </div>
