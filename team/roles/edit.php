<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "team/roles/manage");
    exit();
}

$role_id = $_GET['id'];

// Fetch role details
$stmt = $conn->prepare("SELECT * FROM team_roles WHERE id = :id");
$stmt->bindParam(':id', $role_id);
$stmt->execute();
$role = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$role) {
    header("Location: " . BASE_URL . "team/roles/manage");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);

    // Validate input
    if (empty($name)) {
        $error = "Role name is required.";
    } else {
        // Check if the role already exists
        $stmt = $conn->prepare("SELECT id FROM team_roles WHERE name = :name AND id != :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $role_id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "A role with this name already exists.";
        } else {
            // Update the role
            $stmt = $conn->prepare("UPDATE team_roles SET name = :name WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':id', $role_id);

            if ($stmt->execute()) {
                $success = "Role updated successfully!";
                 header("Location: " . BASE_URL . "team/roles/manage?success=true");
                 exit();
            } else {
                $error = "Error updating role.";
            }
        }
    }
}


?>

<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Edit Team Role</h1>

     <!-- Display error or success message -->
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success || isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            Role updated successfully!
        </div>
    <?php endif; ?>

       <!-- Edit Role Form -->
     <div class="bg-gray-100 border border-gray-400 p-6 rounded-lg">
            <form method="POST" action="">
            <?php echo csrfTokenInput(); ?>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Role Name</label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" value="<?php echo htmlspecialchars($role['name']); ?>" required>
               </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 uppercase tracking-wide">Update Role</button>
                <div class="mt-4">
                     <a href="<?php echo BASE_URL; ?>team/roles/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 uppercase tracking-wide">Back To Roles</a>
                </div>
           </form>
    </div>
</div>

